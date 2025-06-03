<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RepairReport;
use App\Models\Technician;
use App\Models\Building;
use App\Models\BuildingFacility;
use App\Models\Room;
use App\Models\RoomFacility;
use App\Models\Complaint;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function adminDashboardView()
    {
        $permintaanPerbaikan = RepairReport::where('status', '!=', 'Dibatalkan')->count(); // Semua laporan kecuali Dibatalkan

        $sedangDiproses = RepairReport::whereIn('status', [
            'Diproses',
            'Dijadwalkan',
            'Dalam Proses Pengerjaan',
            'Pengecekan Akhir'
        ])->count();

        $perbaikanSelesai = RepairReport::where('status', 'Selesai')->count();

        // Ambil 5 laporan terakhir yang tidak dibatalkan
        $laporanTerakhir = RepairReport::where('status', '!=', 'Dibatalkan')
            ->latest()
            ->take(5)
            ->get();


        return view('admin.adminDashboard', [
            'permintaanPerbaikan' => $permintaanPerbaikan,
            'sedangDiproses' => $sedangDiproses,
            'perbaikanSelesai' => $perbaikanSelesai,
            'laporanTerakhir' => $laporanTerakhir,
        ]);
    }


    public function adminProfileView()
    {
        return view('admin.adminProfile');
    }
    public function adminDaftarPermintaanPerbaikanView()
    {
        $RepairReports = RepairReport::with([
            'room',
            'roomFacility',
            'building',
            'buildingFacility',
            'histories',
            'schedules',
            'user',
        ])->whereNotIn('status', ['Selesai', 'Ditolak', 'Dibatalkan'])->orderByDesc('damage_point')  // Urutkan damage_point dari besar ke kecil
            ->orderBy('created_at', 'asc')->get();
        return view('admin.adminDaftarPermintaanPerbaikan', compact('RepairReports'));
    }
    public function adminRiwayatPerbaikanView()
    {
        $RepairReports = RepairReport::with([
            'room',
            'roomFacility',
            'building',
            'buildingFacility',
            'histories',
            'schedules'
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
            'id_report' => 'required|exists:repair_reports,id',
            'nama_teknisi' => 'required|string|max:255',
        ]);

        $report = RepairReport::findOrFail($request->id_report);
        $report->teknisi = $request->nama_teknisi;
        $report->status = 'Selesai';
        $report->save();

        return redirect()->back()->with('success', 'Laporan berhasil diperbarui dengan teknisi.');
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
        ])->get();

        return view('admin.adminDaftarKeluhan', compact('Complaints'));
    }
}
