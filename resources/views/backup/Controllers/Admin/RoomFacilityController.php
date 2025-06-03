<?php

namespace App\Http\Controllers\Admin;

use App\Models\RoomFacility;
use App\Models\Room;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoomFacilityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::with(['building:id,building_name'])->get(['id', 'room_name', 'id_building']);

        // Tambahkan eager loading sampai ke building
        $facilities = RoomFacility::with('room.building:id,building_name')->get();

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
        ]);

        // Menyimpan data ruangan baru
        RoomFacility::create([
            'id_room' => $request->id_room,
            'facility_name' => $request->facility_name,
            'room_type' => $request->room_type,
            'number_units' => $request->number_units,
            'description' => $request->description,
        ]);

        return redirect('/admin-data-fasilitas-ruang')->with('success', 'Ruangan berhasil ditambahkan.');
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