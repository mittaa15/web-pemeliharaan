<?php

namespace App\Http\Controllers\Admin;

use App\Models\RoomFacility;
use App\Models\Room;
use App\Models\Notification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoomFacilityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil data ruang beserta nama gedung, diurutkan berdasarkan nama gedung dan nama ruang
        $rooms = Room::with(['building:id,building_name'])
            ->join('building', 'room.id_building', '=', 'building.id')
            ->orderBy('building.building_name', 'asc')
            ->orderBy('room.room_name', 'asc')
            ->select('room.id', 'room.room_name', 'room.id_building')
            ->get();

        // Mengambil data fasilitas ruang, dengan eager loading hingga gedung, dan diurutkan berdasarkan nama gedung dan nama ruang
        $facilities = RoomFacility::with('room.building:id,building_name', 'repairReports')
            ->join('room', 'room_facility.id_room', '=', 'room.id')
            ->join('building', 'room.id_building', '=', 'building.id')
            ->orderBy('building.building_name', 'asc')
            ->orderBy('room.room_name', 'asc')
            ->select('room_facility.*')
            ->get();

        return view('admin.dataFasilitasRuangAdmin', compact('facilities', 'rooms'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $request->validate([
            'id_room' => 'required|exists:room,id',
            'facility_name' => 'required|string|max:255',
            'number_units' => 'nullable|integer',
            'description' => 'nullable|string',
        ], [
            'id_room.required' => 'Ruangan wajib dipilih.',
            'facility_name.required' => 'Nama fasilitas wajib diisi.',
        ]);

        // Cek apakah kombinasi ruangan dan fasilitas sudah ada
        $exists = RoomFacility::where('id_room', $request->id_room)
            ->where('facility_name', $request->facility_name)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Fasilitas dengan nama yang sama sudah terdaftar pada ruangan ini.'
            ], 422); // 422 = Unprocessable Entity
        }

        // Simpan data jika belum ada
        RoomFacility::create([
            'id_room' => $request->id_room,
            'facility_name' => $request->facility_name,
            'number_units' => $request->number_units,
            'description' => $request->description,
        ]);

        return response()->json(['message' => 'Fasilitas ruangan berhasil ditambahkan.']);
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
    public function show(RoomFacility $roomFacility)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RoomFacility $roomFacility)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RoomFacility $roomFacility)
    {
        // dd($request->all());
        $request->validate([
            'facility_name' => 'required|string|max:255',
            'number_units' => 'nullable|integer',
            'description' => 'nullable|string',
        ]);

        // Update data gedung
        $roomFacility->update([
            'facility_name' => $request->facility_name,
            'number_units' => $request->number_units,
            'description' => $request->description,
        ]);

        return redirect('/admin-data-fasilitas-ruang')->with('success', 'Fasilitas Gedung berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RoomFacility $roomFacility)
    {
        $roomFacility->delete();

        return redirect()->back()->with('success', 'Fasilitas ruangan berhasil dihapus.');
    }
}