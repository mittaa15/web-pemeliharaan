<?php

namespace App\Http\Controllers\Sarpras;

use App\Models\RepairSchedule;
use App\Models\RepairReport;
use App\Models\RepairHistory;
use App\Http\Controllers\Controller;
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
            'status' => 'required|string|max:255',
        ]);

        $report = RepairReport::find($request->id_report);
        $report->status = $request->status;
        $report->save();

        RepairHistory::create([
            'id_report' => $request->id_report,
            'status' => $request->status,
            'complete_date' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', 'Jadwal perbaikan berhasil disimpan dan status diperbarui.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_report' => 'required|exists:repair_report,id',
            'technician_name' => 'required|string|max:255',
            'repair_date' => 'required|date',
        ]);

        $schedule = RepairSchedule::create([
            'id_report' => $request->id_report,
            'technician_name' => $request->technician_name,
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

    public function repairReport()
    {
        return $this->belongsTo(RepairReport::class, 'id_report');
    }
}