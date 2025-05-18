<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\BuildingFacility;
use App\Models\Room;
use App\Models\RepairReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboardView()
    {
        $buildings = Building::all();
        $rooms = Room::all();
        $indoorFacilities = BuildingFacility::with('building:id,building_name')
            ->where('location', 'indoor')
            ->get();

        // Ambil fasilitas outdoor
        $outdoorFacilities = BuildingFacility::with('building:id,building_name')
            ->where('location', 'outdoor')
            ->get();

        return view('user.dashboard', compact('buildings', 'rooms', 'indoorFacilities', 'outdoorFacilities'));
    }
    public function daftarPermintaanView()
    {
        $userId = Auth::id();

        $RepairReports = RepairReport::with([
            'room',
            'room_facility',
            'building',
            'building_facility',
            'repair_historie'
        ])->where('id_user', $userId)->get();
        return view('user.daftarPermintaan', compact('RepairReports'));
    }
    public function ajukanKeluhanView()
    {
        return view('user.ajukanKeluhan');
    }
    public function detailLaporanView()
    {
        return view('user.detailLaporan');
    }
    public function profileView()
    {
        return view('user.profile');
    }
    public function riwayatLaporanPerbaikanView()
    {
        return view('user.riwayatLaporanPerbaikan');
    }
}
