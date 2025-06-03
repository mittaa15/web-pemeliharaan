<?php

namespace App\Http\Controllers\Admin;

use App\Models\Building;
use App\Models\Room;
use App\Models\RoomFacility;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class BuildingController extends Controller
{

    public function index()
    {
        $facilities = Building::orderBy('building_name')->get();
        return view('admin.dataGedungAdmin', compact('facilities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $request->validate([
            'building_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Simpan ke database
        Building::create([
            'building_name' => $request->building_name,
            'description' => $request->description,
        ]);

        // Redirect atau responreturn 
        return redirect('/admin-data-gedung')->with('success', 'Data gedung berhasil ditambahkan.');
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
    public function show(Building $building)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Building $building) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Building $building)
    {
        // dd($request->all());
        $request->validate([
            'building_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Update data gedung
        $building->update([
            'building_name' => $request->building_name,
            'description' => $request->description,
        ]);

        // Redirect ke halaman dengan pesan sukses
        return redirect('/admin-data-gedung')->with('success', 'Gedung berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */


    public function destroy(Building $building)
    {
        try {
            // Ambil semua ruangan di gedung ini
            $rooms = Room::where('id_building', $building->id)->get();

            // Hapus semua fasilitas ruangan yang terkait
            foreach ($rooms as $room) {
                RoomFacility::where('id_room', $room->id)->delete();
            }

            // Hapus semua ruangan di gedung ini
            Room::where('id_building', $building->id)->delete();

            // Hapus semua entri terkait dari tabel building_facility
            DB::table('building_facility')->where('id_building', $building->id)->delete();

            // Hapus gedung
            $building->delete();

            return redirect()->back()->with('success', 'Gedung dan semua data terkait berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus gedung: ' . $e->getMessage());
        }
    }
}