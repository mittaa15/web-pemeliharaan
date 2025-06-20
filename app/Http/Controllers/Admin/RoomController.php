<?php

namespace App\Http\Controllers\Admin;

use App\Models\Room;
use App\Models\RoomFacility;
use App\Models\Building;
use App\Models\Notification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    /**
     * Menampilkan daftar ruangan.
     */
    public function index()
    {
        $buildings = Building::all();
        $rooms = Room::with('building:id,building_name')
            ->orderBy('room_name', 'asc')
            ->get();

        return view('admin.dataRuangAdmin', compact('buildings', 'rooms'));
    }

    /**
     * Menampilkan form untuk menambah ruangan baru.
     */
    public function create(Request $request)
    {
        $request->validate([
            'id_building' => 'required|exists:building,id',
            'room_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('room')->where(function ($query) use ($request) {
                    return $query->where('id_building', $request->id_building);
                }),
            ],
            'room_type' => 'nullable|string',
            'capacity' => 'nullable|integer',
            'description' => 'nullable|string',
        ], [
            'room_name.unique' => 'Nama ruangan untuk gedung ini sudah ada, silakan gunakan nama lain.',
        ]);

        Room::create([
            'id_building' => $request->id_building,
            'room_name' => $request->room_name,
            'room_type' => $request->room_type,
            'capacity' => $request->capacity,
            'description' => $request->description,
        ]);

        return response()->json(['message' => 'Ruangan berhasil ditambahkan.']);
    }

    /**
     * Menyimpan ruangan yang baru ditambahkan ke dalam database.
     */
    public function store(Request $request) {}

    /**
     * Menampilkan form untuk mengedit data ruangan.
     */
    public function edit() {}

    /**
     * Memperbarui data ruangan yang telah ada.
     */
    public function update(Request $request, Room $room)
    {
        $request->validate([
            'room_name' => 'required|string|max:255',
            'room_type' => 'nullable|string',
            'capacity' => 'nullable|integer',
            'description' => 'nullable|string',
        ]);

        // Memperbarui data ruangan
        $room->update([
            'room_name' => $request->room_name,
            'room_type' => $request->room_type,
            'capacity' => $request->capacity,
            'description' => $request->description,
        ]);

        return redirect('/admin-data-ruang')->with('success', 'Ruangan berhasil diperbarui.');
    }

    /**
     * Menghapus data ruangan.
     */
    public function destroy(Room $room)
    {
        // Hapus semua fasilitas yang terkait dengan ruangan ini
        RoomFacility::where('id_room', $room->id)->delete();

        // Hapus data ruangan
        $room->delete();

        return redirect('/admin-data-ruang')->with('success', 'Ruangan dan fasilitas terkait berhasil dihapus.');
    }
}