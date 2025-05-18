<?php

namespace App\Http\Controllers\Sarpras;

use App\Models\Building;
use App\Models\BuildingFacility;
use App\Models\Room;
use App\Models\RoomFacility;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SarprasDashboardController extends Controller
{
    public function sarprasDashboardView()
    {
        return view('sarpras.sarprasDashboard');
    }
    public function daftarPermintaanPerbaikanView()
    {
        return view('sarpras.daftarPermintaanPerbaikan');
    }
    public function sarprasProfileView()
    {
        return view('sarpras.sarprasProfile');
    }
    public function riwayatPerbaikanView()
    {
        return view('sarpras.riwayatPerbaikan');
    }

    //Kopas dari admin
    public function dataGedungView()
    {
        $facilities = Building::all();
        return view('sarpras.dataGedung', compact('facilities'));
    }
    public function dataFasilitasGedungView()
    {
        $buildings = Building::all();

        // Ambil fasilitas indoor
        $indoorFacilities = BuildingFacility::with('building:id,building_name')
            ->where('location', 'indoor')
            ->get();

        // Ambil fasilitas outdoor
        $outdoorFacilities = BuildingFacility::with('building:id,building_name')
            ->where('location', 'outdoor')
            ->get();

        return view('sarpras.dataFasilitasGedung', compact('buildings', 'indoorFacilities', 'outdoorFacilities'));
    }
    public function sarprasDataRuangView()
    {
        $buildings = Building::all();
        $rooms = Room::with('building:id,building_name')->get();
        return view('sarpras.sarprasDataRuang', compact('buildings', 'rooms'));
    }
    public function dataFasilitasRuangView()
    {
        $rooms = Room::with(['building:id,building_name'])->get(['id', 'room_name', 'id_building']);

        // Tambahkan eager loading sampai ke building
        $facilities = RoomFacility::with('room.building:id,building_name')->get();

        return view('sarpras.dataFasilitasRuang', compact('facilities', 'rooms'));
    }
}