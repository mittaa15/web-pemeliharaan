<?php

namespace App\Http\Controllers\Sarpras;

use App\Models\Building;
use App\Models\BuildingFacility;
use App\Models\Room;
use App\Models\RoomFacility;
use App\Models\RepairReport;
use App\Models\Notification;
use App\Models\Technician;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SarprasDashboardController extends Controller
{
    public function sarprasDashboardView()
    {
        $permintaanPerbaikan = RepairReport::where('status', '!=', 'Dibatalkan')->count(); // Semua laporan kecuali Dibatalkan

        $sedangDiproses = RepairReport::whereIn('status', [
            'Diproses',
            'Dijadwalkan',
            'Dalam Proses Pengerjaan',
            'Pengecekan Akhir',
            'Ditolak'
        ])->count();

        $perbaikanSelesai = RepairReport::where('status', 'Selesai')->count();
        $perbaikanDitolak = RepairReport::where('status', 'Ditolak')->count();

        // Ambil 5 laporan terakhir yang tidak dibatalkan
        $laporanTerakhir = RepairReport::where('status', '!=', 'Dibatalkan')
            ->latest()
            ->take(5)
            ->get();


        return view('sarpras.sarprasDashboard', [
            'permintaanPerbaikan' => $permintaanPerbaikan,
            'sedangDiproses' => $sedangDiproses,
            'perbaikanSelesai' => $perbaikanSelesai,
            'laporanTerakhir' => $laporanTerakhir,
            'perbaikanDitolak' => $perbaikanDitolak,
        ]);
    }


    public function daftarPermintaanPerbaikanView(Request $request)
    {
        // Ambil laporan yang statusnya 'Diproses'
        $diprosesReports = RepairReport::with([
            'room',
            'roomFacility',
            'building',
            'buildingFacility',
            'latestHistory',
            'schedules',
            'user',
        ])->where('status', 'Diproses')->get();

        // Ambil laporan selain status tertentu dan lakukan grouping
        $otherReportsRaw = RepairReport::with([
            'room',
            'roomFacility',
            'building',
            'buildingFacility',
            'latestHistory',
            'schedules',
            'user',
        ])
            ->whereNotIn('status', ['Diproses', 'Selesai', 'Ditolak', 'Dibatalkan'])
            ->get();

        // Grouping berdasarkan kombinasi ID
        $grouped = $otherReportsRaw->groupBy(function ($item) {
            return implode('-', [
                $item->id_room,
                $item->id_facility_room,
                $item->id_building,
                $item->id_facility_building,
            ]);
        });

        $groupedOtherReports = collect();
        foreach ($grouped as $group) {
            $groupedOtherReports->push($group->first());
        }

        // Gabungkan kedua hasil
        $RepairReports = $diprosesReports->merge($groupedOtherReports);

        // Urutkan berdasarkan damage_point DESC, lalu created_at ASC
        $RepairReports = $RepairReports->sortBy([
            ['damage_point', 'desc'],
            ['created_at', 'asc'],
        ])->values(); // reset index

        // Ambil teknisi & notifikasi
        $TeknisiLists = Technician::all();
        $selectedId = $request->query('id');

        $notifications = Notification::where('id_user', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();


        return view('sarpras.daftarPermintaanPerbaikan', compact(
            'RepairReports',
            'TeknisiLists',
            'selectedId',
            'notifications'
        ));
    }

    public function sarprasProfileView()
    {
        $user = Auth::user();
        return view('sarpras.sarprasProfile', compact('user'));
    }
    public function riwayatPerbaikanView()
    {
        $RepairReports = RepairReport::with([
            'room',
            'roomFacility',
            'building',
            'buildingFacility',
            'histories',
            'schedules'
        ])->whereIn('status', ['Selesai', 'Ditolak'])->orderBy('created_at', 'desc')->get();

        return view('sarpras.riwayatPerbaikan', compact('RepairReports'));
    }

    //Kopas dari admin
    public function dataGedungView()
    {
        $facilities = Building::orderBy('building_name', 'asc')->get();
        return view('sarpras.dataGedung', compact('facilities'));
    }

    public function dataFasilitasGedungView()
    {
        $buildings = Building::orderBy('building_name', 'asc')->get(); // ini juga diurutkan

        $indoorFacilities = BuildingFacility::with('building:id,building_name')
            ->where('location', 'indoor')
            ->get()
            ->sortBy(function ($item) {
                return $item->building->building_name;
            });

        $outdoorFacilities = BuildingFacility::with('building:id,building_name')
            ->where('location', 'outdoor')
            ->get()
            ->sortBy(function ($item) {
                return $item->building->building_name;
            });

        return view('sarpras.dataFasilitasGedung', compact('buildings', 'indoorFacilities', 'outdoorFacilities'));
    }

    public function sarprasDataRuangView()
    {
        $buildings = Building::orderBy('building_name', 'asc')->get();
        $rooms = Room::with('building:id,building_name')
            ->orderBy('room_name', 'asc')
            ->get();

        return view('sarpras.sarprasDataRuang', compact('buildings', 'rooms'));
    }

    public function dataFasilitasRuangView()
    {
        $rooms = Room::with('building:id,building_name')
            ->orderBy('room_name', 'asc')
            ->get(['id', 'room_name', 'id_building']);

        $facilities = RoomFacility::with(['room' => function ($query) {
            $query->with('building:id,building_name')->orderBy('room_name', 'asc');
        }, 'repairReports'])->get();

        return view('sarpras.dataFasilitasRuang', compact('facilities', 'rooms'));
    }

    public function getLaporanByFacility($id)
    {
        $laporan = RepairReport::with(['user', 'technician', 'histories', 'photos']) // pastikan relasi ini sesuai
            ->where('id_facility_building', $id)
            ->orWhere('id_facility_room', $id)
            ->get();

        return response()->json($laporan);
    }

    public function sarprasDataTeknisiView()
    {
        $technicians = Technician::all();
        return view('sarpras.sarprasDataTeknisi', compact('technicians'));
    }
}
