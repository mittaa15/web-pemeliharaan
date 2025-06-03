<?php

namespace App\Http\Controllers\User;

use App\Models\RepairReport;
use App\Http\Controllers\Controller;
use App\Models\RoomFacility;
use App\Models\RepairHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RepairReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roomFacilitys = RoomFacility::all();
        return view('user.formPelaporan', compact('roomFacilitys'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'id_user' => 'required|integer',
            'id_building' => 'nullable|integer',
            'id_room' => 'nullable|integer',
            'id_facility_building' => 'nullable|integer',
            'id_facility_room' => 'nullable|integer',
            'damage_description' => 'nullable|string',
            'damage_photo' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'location_type' => 'nullable|string',
            'damage_impact' => 'nullable|string',
            'room_name' => 'nullable|string',
            'building_name' => 'nullable|string',
        ]);

        $status = "Diproses";

        $damagePoint = match ($request->damage_impact) {
            'Keselamatan pengguna' => 75,
            'Penghentian operasional' => 35,
            'Menghambat pekerjaan' => 5,
            default => 0,
        };

        $specialRooms = ['ruang pimpinan', 'ruang rapat'];
        $specialBuildings = ['auditorium', 'masjid al-fatih', 'asrama mahasiswa'];

        $roomName = strtolower($request->input('room_name', ''));
        $buildingName = strtolower($request->input('building_name', ''));

        if (in_array($roomName, $specialRooms) || in_array($buildingName, $specialBuildings)) {
            $damagePoint += 40;
        }


        $photoPath = $request->file('damage_photo')->store('kerusakan', 'public');

        $report = RepairReport::create([
            'id_user' => $request->id_user,
            'id_building' => $request->id_building,
            'id_room' => $request->id_room,
            'id_facility_building' => $request->id_facility_building,
            'id_facility_room' => $request->id_facility_room,
            'damage_description' => $request->damage_description,
            'damage_photo' => $photoPath,
            'status' => $status,
            'location_type' => $request->location_type,
            'damage_impact' => $request->damage_impact,
            'damage_point' => $damagePoint,
        ]);
        RepairHistory::create([
            'id_report' => $report->id,
            'status' => $status,
            'complete_date' => Carbon::now(), // atau bisa gunakan now()
        ]);

        if ($request->input('action') === 'dashboard') {
            return redirect('/dashboard')->with('success', 'Laporan berhasil ditambahkan.');
        }

        return redirect()->back()->with('success', 'Laporan berhasil ditambahkan.');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(RepairReport $repairReport)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $request->validate([
            'id_report' => 'required|exists:repair_report,id',
        ]);
        $report = RepairReport::find($request->id_report);
        $report->status = 'Dibatalkan';
        $report->save();

        RepairHistory::create([
            'id_report' => $request->id_report,
            'status' => 'Dibatalkan',
            'complete_date' => Carbon::now(),
        ]);
        return redirect()->back()->with('success', 'Laporan berhasil dibatalkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RepairReport $repairReport)
    {
        // Validasi input
        $request->validate([
            'damage_impact' => 'required|string|max:255',
            'damage_description' => 'required|string',
        ]);

        // Hitung damage_point berdasarkan impact
        $damagePoint = match ($request->damage_impact) {
            'Keselamatan pengguna' => 75,
            'Penghentian operasional' => 35,
            'Menghambat pekerjaan' => 5,
            default => 0,
        };

        // Tambahan poin jika ruangan/gedung istimewa
        $specialRooms = ['ruang pimpinan', 'ruang rapat'];
        $specialBuildings = ['auditorium', 'masjid al-fatih', 'asrama mahasiswa'];

        // Ambil nama ruangan/gedung dari relasi
        $roomName = strtolower($repairReport->room->room_name ?? '');
        $buildingName = strtolower($repairReport->building->building_name ?? '');

        if (in_array($roomName, $specialRooms) || in_array($buildingName, $specialBuildings)) {
            $damagePoint += 40;
        }

        // Update data laporan
        $repairReport->update([
            'damage_impact' => $request->damage_impact,
            'damage_description' => $request->damage_description,
            'damage_point' => $damagePoint, // <-- nilai akhir
        ]);

        return redirect()->back()->with('success', 'Laporan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RepairReport $repairReport)
    {
        //
    }
}
