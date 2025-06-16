<?php

namespace App\Http\Controllers\Sarpras;

use App\Models\RepairSchedule;
use App\Models\RepairReport;
use App\Models\RepairHistory;
use App\Http\Controllers\Controller;
use App\Models\Technician;
use App\Models\Notification;
use App\Models\Complaint;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class RepairScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $request->validate([
            'id_report' => 'required|exists:repair_report,id',
            'id_user' => 'required|exists:users,id',
            'status' => 'required|string|max:255',
        ]);

        // Ambil laporan utama
        $report = RepairReport::findOrFail($request->id_report);

        // Ambil semua laporan duplikat yang statusnya belum selesai/dibatalkan/ditolak
        $duplicateReports = RepairReport::where('id_room', $report->id_room)
            ->where('id_facility_room', $report->id_facility_room)
            ->where('id_building', $report->id_building)
            ->where('id_facility_building', $report->id_facility_building)
            ->whereNotIn('status', ['Selesai', 'Ditolak', 'Dibatalkan'])
            ->get();

        foreach ($duplicateReports as $dupReport) {
            // Update status
            $dupReport->status = $request->status;
            $dupReport->save();

            // Tambah riwayat
            RepairHistory::create([
                'id_report' => $dupReport->id,
                'status' => $request->status,
                'complete_date' => Carbon::now(),
            ]);

            // Kirim notifikasi
            Notification::createNotification(
                $dupReport->id_user,
                'Laporan Anda diupdate menjadi ' . $request->status,
                'Silakan periksa halaman daftar laporan Anda.'
            );
        }

        return redirect()->back()->with('success', 'Status dan riwayat berhasil diperbarui untuk semua laporan terkait.');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_report' => 'required|exists:repair_report,id',
            'id_user' => 'required|exists:users,id',
            'repair_date' => 'required|date',
        ]);

        $schedule = RepairSchedule::create([
            'id_report' => $request->id_report,
            'repair_date' => $request->repair_date,
        ]);

        $report = RepairReport::find($request->id_report);
        $report->status = 'Dijadwalkan';
        $report->save();

        RepairHistory::create([
            'id_report' => $request->id_report,
            'status' => 'Dijadwalkan',
            'complete_date' => Carbon::now(),
        ]);

        Notification::createNotification(
            $request->id_user,
            'Laporan Anda disetujui',
            'Silahkan periksa di dihalaman daftar laporan'
        );

        // Redirect atau response sukses
        return redirect()->back()->with('success', 'Jadwal perbaikan berhasil disimpan dan status diperbarui.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RepairSchedule $repairSchedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RepairSchedule $repairSchedule) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RepairSchedule $repairSchedule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RepairSchedule $repairSchedule)
    {
        //
    }

    public function uploadPerbaikan(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'id_report' => 'required|exists:repair_report,id',
            'id_user' => 'required|exists:users,id',
            'repair_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'repair_description' => 'required|string|max:1000',
        ]);

        // Ambil laporan utama
        $report = RepairReport::findOrFail($request->id_report);

        // Simpan file foto
        $path = $request->file('repair_photo')->store('kerusakan', 'public');

        // Ambil semua laporan duplikat (termasuk laporan utama) yang statusnya belum selesai/dibatalkan/ditolak
        $duplicateReports = RepairReport::where('id_room', $report->id_room)
            ->where('id_facility_room', $report->id_facility_room)
            ->where('id_building', $report->id_building)
            ->where('id_facility_building', $report->id_facility_building)
            ->whereNotIn('status', ['Selesai', 'Ditolak', 'Dibatalkan'])
            ->get();

        foreach ($duplicateReports as $dupReport) {
            // Update status
            $dupReport->status = 'Pengecekan akhir';
            $dupReport->save();

            // Simpan riwayat
            RepairHistory::create([
                'id_report' => $dupReport->id,
                'status' => 'Pengecekan akhir',
                'repair_notes' => $request->repair_description,
                'damage_photo' => $path,
                'complete_date' => Carbon::now(),
            ]);
        }

        Notification::createNotification(
            $request->id_user,
            'Laporan Anda diupdate menjadi Pengecekan Akhir',
            'Silahkan periksa di dihalaman daftar laporan'
        );

        return redirect()->back()->with('success', 'Data perbaikan berhasil disimpan untuk semua laporan terkait.');
    }


    public function createTechnician(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:technicians,email',
            'phone_number' => 'required|string|max:20',
        ]);

        Technician::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ]);

        return redirect()->back()->with('success', 'Teknisi berhasil ditambahkan.');
    }

    public function updateTechnician(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:technicians,email,{$id}",
            'phone_number' => 'required|string|max:20',
        ]);

        $technician = Technician::findOrFail($id);
        $technician->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ]);

        return redirect()->back()->with('success', 'Data teknisi berhasil diperbarui.');
    }


    public function destroyTechnician($id)
    {
        $technician = Technician::findOrFail($id);
        $technician->delete();

        return redirect()->back()->with('success', 'Teknisi berhasil dihapus.');
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

    public function sarprasDaftarKeluhanView()
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

        return view('sarpras.sarprasDaftarKeluhan', compact('Complaints'));
    }


    public function updateStatusOtomatis()
    {
        $today = Carbon::today();

        // Ambil laporan yang belum 'Dalam Proses Pengerjaan' tapi sudah waktunya
        $laporan = RepairReport::where('status', '!=', 'Dalam Proses Pengerjaan')
            ->where('tanggal_mulai_pengerjaan', '<=', $today)
            ->get();

        foreach ($laporan as $item) {
            $item->status = 'Dalam Proses Pengerjaan';
            $item->save();

            RepairHistory::create([
                'id_report' => $item->id,
                'status' => 'Dalam Proses Pengerjaan',
                'complete_date' => Carbon::now(),
            ]);
        }
    }
}
