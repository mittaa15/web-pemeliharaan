<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\BuildingFacility;
use App\Models\RepairHistory;
use App\Models\Room;
use App\Models\RepairReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboardView()
    {
        $userId = Auth::id();
        $jumlahDiproses = RepairReport::where('id_user', $userId)->where('status', 'Diproses')->count();
        $jumlahSelesai = RepairReport::where('id_user', $userId)->where('status', 'Selesai')->count();
        $jumlahDitolak = RepairReport::where('id_user', $userId)->where('status', 'Ditolak')->count();
        $jumlahPengerjaan = RepairReport::where('id_user', $userId)->where('status', 'Dalam Proses Pengerjaan')->count();
        $jumlahDijadwalkan = RepairReport::where('id_user', $userId)->where('status', 'Dijadwalkan')->count();

        $buildings = Building::all();
        $rooms = Room::all();
        $indoorFacilities = BuildingFacility::with('building:id,building_name')
            ->where('location', 'indoor')
            ->get();

        // Ambil fasilitas outdoor
        $outdoorFacilities = BuildingFacility::with('building:id,building_name')
            ->where('location', 'outdoor')
            ->get();

        return view('user.dashboard', compact('buildings', 'rooms', 'indoorFacilities', 'outdoorFacilities', 'jumlahDiproses', 'jumlahSelesai', 'jumlahDitolak', 'jumlahPengerjaan', 'jumlahDijadwalkan'));
    }
    public function daftarPermintaanView()
    {
        $userId = Auth::id();

        $RepairReports = RepairReport::with([
            'room',
            'roomFacility',
            'building',
            'buildingFacility',
            'histories',
            'schedules'
        ])->where('id_user', $userId)->whereNotIn('status', ['Selesai', 'Ditolak', 'Dibatalkan'])->orderBy('created_at', 'desc')->get();

        return view('user.daftarPermintaan', compact('RepairReports'));
    }

    public function detail($id)
    {
        $report = RepairReport::with(['room', 'roomFacility', 'building', 'buildingFacility', 'histories'])
            ->findOrFail($id);

        // Ambil RepairHistory terbaru (status terkini)
        $latestHistory = $report->histories()->latest('created_at')->first();

        return view('user.repairDetail', compact('report', 'latestHistory'));
    }

    public function riwayatStatus($id)
    {

        // Ambil RepairHistory terbaru (status terkini)
        $History = RepairHistory::where('id_report', $id)->orderBy('created_at', 'desc')->get();

        return view('user.repairDetail', compact('History'));
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
        $userId = Auth::id();

        $RepairReports = RepairReport::with([
            'room',
            'roomFacility',
            'building',
            'buildingFacility',
            'histories',
            'schedules'
        ])->where('id_user', $userId)->whereIn('status', ['Selesai', 'Ditolak', 'Dibatalkan'])->orderBy('created_at', 'desc')->get();
        return view('user.riwayatLaporanPerbaikan', compact('RepairReports'));
    }
}