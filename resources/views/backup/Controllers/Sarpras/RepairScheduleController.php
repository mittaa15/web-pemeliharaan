<?php

namespace App\Http\Controllers\Sarpras;

use App\Models\RepairSchedule;
use App\Models\RepairReport;
use App\Models\RepairHistory;
use App\Http\Controllers\Controller;
use App\Models\Technician;
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
        ])->get();

        return view('sarpras.sarprasDaftarKeluhan', compact('Complaints'));
    }
}
