<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\BuildingFacility;
use App\Models\RepairHistory;
use App\Models\Room;
use App\Models\Notification;
use App\Models\RepairReport;
use App\Models\RoomFacility;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboardView()
    {
        $userId = Auth::id();

        $buildings = Building::all();
        $rooms = Room::all();

        $indoorFacilities = BuildingFacility::where('location', 'indoor')->get();

        // Format ulang data Room agar seolah seperti fasilitas
        $roomFacilities = $rooms->map(function ($room) {
            return (object)[
                'id' => $room->id, // karena bukan dari BuildingFacility
                'facility_name' => $room->room_name,
                'id_building' => $room->id_building,
                'id_room' => $room->id,
            ];
        });

        // Gabungkan BuildingFacility (indoor) dan Room (sebagai fasilitas indoor juga)
        $indoorFacilities = $indoorFacilities->map(function ($item) {
            return $item;
        })->concat($roomFacilities);

        $outdoorFacilities = BuildingFacility::where('location', 'outdoor')->get();


        $jumlahDiproses = RepairReport::where('id_user', $userId)->where('status', 'Diproses')->count();
        $jumlahDijadwalkan = RepairReport::where('id_user', $userId)->where('status', 'Dijadwalkan')->count();
        $jumlahPengerjaan = RepairReport::where('id_user', $userId)->where('status', 'Dalam Proses Pengerjaan')->count();
        $jumlahSelesai = RepairReport::where('id_user', $userId)->where('status', 'Selesai')->count();
        $jumlahDitolak = RepairReport::where('id_user', $userId)->where('status', 'Ditolak')->count();

        return view('user.dashboard', compact(
            'buildings',
            'rooms',
            'indoorFacilities',
            'outdoorFacilities',
            'jumlahDiproses',
            'jumlahDijadwalkan',
            'jumlahPengerjaan',
            'jumlahSelesai',
            'jumlahDitolak'
        ));
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
        $user = Auth::user();
        return view('user.profile', compact('user'));
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
