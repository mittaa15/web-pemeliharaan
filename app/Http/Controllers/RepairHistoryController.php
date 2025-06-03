<?php

namespace App\Http\Controllers;

use App\Models\RepairHistory;
use App\Models\RepairReport;
use App\Models\Notification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class RepairHistoryController extends Controller
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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'id' => 'required|exists:repair_history,id',
            'id_user' => 'required|exists:users,id',
            'repair_notes' => 'required|string|max:1000',
        ]);

        // Cari RepairHistory yang mau diupdate
        $history = RepairHistory::findOrFail($request->id);
        // Cari laporan terkait RepairHistory ini
        $report = RepairReport::findOrFail($history->id_report);

        // Cari laporan duplikat berdasarkan room, roomFacility, building, buildingFacility
        $duplicateReports = RepairReport::where('id_room', $report->id_room)
            ->where('id_facility_room', $report->id_facility_room)
            ->where('id_building', $report->id_building)
            ->where('id_facility_building', $report->id_facility_building)
            ->whereNotIn('status', ['Selesai', 'Ditolak', 'Dibatalkan'])
            ->get();

        // Ambil semua id laporan duplikat
        $duplicateReportIds = $duplicateReports->pluck('id')->toArray();

        // Update semua RepairHistory dari laporan-laporan duplikat tersebut
        RepairHistory::whereIn('id_report', $duplicateReportIds)
            ->where('status', 'Dalam proses pengerjaan')
            ->update(['repair_notes' => $request->repair_notes]);


        Notification::createNotification(
            $request->id_user,
            'Kendala Perbaikan',
            'Silahkan periksa di dihalaman daftar laporan'
        );

        return redirect()->back()->with('success', 'Tambah catatan berhasil disimpan untuk semua laporan terkait.');
    }


    public function reject(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'id_report' => 'required|exists:repair_report,id',
            'id_user' => 'required|exists:users,id',
            'repair_notes' => 'required|string|max:1000',
        ]);
        $report = RepairReport::find($request->id_report);
        $report->status = 'Ditolak';
        $report->save();

        RepairHistory::create([
            'id_report' => $request->id_report,
            'status' => 'Ditolak',
            'repair_notes' => $request->repair_notes,
            'complete_date' => Carbon::now(),
        ]);
        Notification::createNotification(
            $request->id_user,
            'Laporan ditolak',
            'Silahkan periksa di dihalaman daftar laporan'
        );
        return redirect()->back()->with('success', 'Laporan berhasil dibatalkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RepairHistory $repairHistory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RepairHistory $repairHistory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RepairHistory $repairHistory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RepairHistory $repairHistory)
    {
        //
    }
}
