<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RepairReport;
use App\Models\RepairHistory;
use App\Models\RepairTechnicians;
use App\Models\Notification;
use App\Models\Technician;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

class AdminDashboardController extends Controller
{
    public function adminDashboardView()
    {
        $permintaanPerbaikan = RepairReport::where('status', '!=', 'Dibatalkan')->count(); // Semua laporan kecuali Dibatalkan

        $sedangDiproses = RepairReport::whereIn('status', [
            'Diproses',
            'Dijadwalkan',
            'Dalam Proses Pengerjaan',
            'Pengecekan Akhir',
            'Ditolak'
        ])->count();

        $perbaikanSelesai = RepairReport::where('status', 'Selesai')->count();
        $perbaikanDitolak = RepairReport::where('status', 'Ditolak')->count();

        // Ambil 5 laporan terakhir yang tidak dibatalkan
        $laporanTerakhir = RepairReport::where('status', '!=', 'Dibatalkan')
            ->latest()
            ->take(5)
            ->get();

        // ✅ Tambahkan ini
        $notifications = Notification::where('id_user', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();


        return view('admin.adminDashboard', [
            'permintaanPerbaikan' => $permintaanPerbaikan,
            'sedangDiproses' => $sedangDiproses,
            'perbaikanSelesai' => $perbaikanSelesai,
            'laporanTerakhir' => $laporanTerakhir,
            'perbaikanDitolak' => $perbaikanDitolak,
            'notifications' => $notifications,
        ]);
    }

    public function adminDaftarPermintaanPerbaikanView(Request $request)
    {
        // Ambil semua laporan yang statusnya bukan 'Selesai', 'Ditolak', atau 'Dibatalkan'
        $allReports = RepairReport::with([
            'room',
            'roomFacility',
            'building',
            'buildingFacility',
            'latestHistory',
            'schedules',
            'user',
        ])
            ->whereNotIn('status', ['Selesai', 'Ditolak', 'Dibatalkan'])
            ->get();

        // Pisahkan laporan dengan status 'Diproses'
        $diprosesReports = $allReports->filter(function ($report) {
            return $report->status === 'Diproses';
        })->sortByDesc('damage_point');

        // Laporan lainnya
        $otherReports = $allReports->filter(function ($report) {
            return $report->status !== 'Diproses';
        })->sortByDesc('damage_point');

        // Gabungkan laporan: Diproses dulu, lalu lainnya
        $RepairReports = $diprosesReports->concat($otherReports)->values();

        // Ambil teknisi & notifikasi
        $TeknisiLists = Technician::all();
        $selectedId = $request->query('id');

        $notifications = Notification::where('id_user', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.adminDaftarPermintaanPerbaikan', compact(
            'RepairReports',
            'TeknisiLists',
            'selectedId',
            'notifications'
        ));
    }



    public function adminRiwayatPerbaikanView()
    {
        $RepairReports = RepairReport::with([
            'room',
            'roomFacility',
            'building',
            'buildingFacility',
            'histories',
            'schedules',
            'repairTechnicians.technician'
        ])->whereIn('status', ['Selesai', 'Ditolak'])->orderBy('created_at', 'desc')->get();

        return view('admin.adminRiwayatPerbaikan', compact('RepairReports'));
    }

    public function dataRuangAdminView()
    {
        return view('admin.dataRuangAdmin');
    }

    public function adminDataTeknisiView()
    {
        $Technicians = Technician::all();
        return view('admin.adminDataTeknisi', compact('Technicians'));
    }
    public function repairReport()
    {
        return $this->belongsTo(RepairReport::class, 'id_report');
    }
    public function uploadPerbaikan(Request $request)
    {
        $request->validate([
            'id_report' => 'required|exists:repair_reports,id',
            'repair_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'repair_description' => 'required|string|max:1000',
        ]);

        $report = RepairReport::findOrFail($request->id_report);

        // Simpan foto
        $path = $request->file('repair_photo')->store('public/repair_photos');

        // Update laporan
        $report->repair_photo = $path;
        $report->repair_description = $request->repair_description;
        $report->save();

        return redirect()->back()->with('success', 'Data perbaikan berhasil disimpan.');
    }

    public function isiTeknisi(Request $request)
    {
        $request->validate([
            'id_report' => 'required|exists:repair_report,id',
            'id_user' => 'required|exists:users,id',
            'nama_teknisi' => 'required|array|min:1',
            'nama_teknisi.*' => 'exists:technicians,id',
            'deskripsi_pekerjaan' => 'required|string|max:1000',
        ]);

        // Ambil laporan utama
        $report = RepairReport::findOrFail($request->id_report);

        // Ambil semua laporan duplikat (termasuk utama) yang belum selesai/dibatalkan/ditolak
        $duplicateReports = RepairReport::where('id_room', $report->id_room)
            ->where('id_facility_room', $report->id_facility_room)
            ->where('id_building', $report->id_building)
            ->where('id_facility_building', $report->id_facility_building)
            ->whereNotIn('status', ['Selesai', 'Ditolak', 'Dibatalkan'])
            ->get();

        foreach ($duplicateReports as $dupReport) {
            // Simpan teknisi untuk laporan ini
            foreach ($request->nama_teknisi as $teknisiId) {
                RepairTechnicians::create([
                    'id_report' => $dupReport->id,
                    'id_technisian' => $teknisiId,
                    'description_work' => $request->deskripsi_pekerjaan,
                ]);
            }

            // Ubah status menjadi 'Selesai'
            $dupReport->status = 'Selesai';
            $dupReport->save();

            // Tambahkan riwayat
            RepairHistory::create([
                'id_report' => $dupReport->id,
                'status' => 'Selesai',
                'complete_date' => Carbon::now(),
            ]);

            Notification::createNotification(
                $dupReport->id_user,
                'Laporan Anda diupdate menjadi Selesai',
                'Silakan periksa halaman daftar laporan Anda.'
            );
        }

        return redirect()->back()->with('success', 'Laporan berhasil diperbarui dengan teknisi untuk semua laporan terkait.');
    }


    public function hapusLaporan(Request $request)
    {
        $report = RepairReport::findOrFail($request->id_report);

        // Cek status hanya boleh hapus jika diproses atau ditolak
        if (in_array(strtolower($report->status), ['diproses', 'ditolak'])) {
            $report->delete();
            return redirect()->back()->with('success', 'Laporan berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Laporan tidak dapat dihapus karena statusnya tidak sesuai.');
    }

    public function adminDaftarKeluhanView()
    {
        $Complaints = Complaint::with([
            'user',
            'repairReport',
            'repairReport.building',
            'repairReport.room'
        ])
            ->whereHas('repairReport', function ($query) {
                $query->where('status', '!=', 'Dibatalkan');
            })
            ->orderBy('created_at', 'desc') // Urutkan dari yang terbaru
            ->get();

        return view('admin.adminDaftarKeluhan', compact('Complaints'));
    }


    public function adminProfileView()
    {
        $user = Auth::user();

        return view('admin.adminProfile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Update menggunakan query builder atau Eloquent dengan where
        User::where('id', $userId)->update([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Nama profil berhasil diperbarui.');
    }
}
