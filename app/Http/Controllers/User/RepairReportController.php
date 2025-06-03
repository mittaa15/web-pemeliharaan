<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\RepairReport;
use App\Http\Controllers\Controller;
use App\Models\RoomFacility;
use App\Models\Notification;
use App\Models\RepairHistory;
use App\Models\Building;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RepairReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $idRoom = $request->query('room');

        $roomFacilitys = RoomFacility::where('id_room', $idRoom)->get();
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
    // public function edit(Request $request)
    // {
    //     $request->validate([
    //         'id_report' => 'required|exists:repair_report,id',
    //     ]);

    //     DB::transaction(function () use ($request) {
    //         $report = RepairReport::findOrFail($request->id_report);

    //         if ($report->status !== 'Pending') {
    //             abort(400, 'Laporan hanya bisa dibatalkan jika status Pending.');
    //         }

    //         $report->status = 'Dibatalkan';
    //         $report->save();

    //         RepairHistory::create([
    //             'id_report' => $request->id_report,
    //             'status' => 'Dibatalkan',
    //             'complete_date' => Carbon::now(),
    //         ]);
    //     });

    //     return redirect()->back()->with('success', 'Laporan berhasil dibatalkan.');
    // }


    // public function update(Request $request, $id)
    // {
    //     // Validasi input
    //     $validated = $request->validate([
    //         'damage_impact' => 'required|string|max:255',
    //         'damage_description' => 'required|string',
    //     ]);

    //     // Cari laporan berdasarkan ID
    //     $report = RepairReport::findOrFail($id);

    //     // Update data laporan
    //     $report->update([
    //         'damage_impact' => $validated['damage_impact'],
    //         'damage_description' => $validated['damage_description'],
    //     ]);

    //     // Redirect atau kembalikan response
    //     return redirect()->back()->with('success', 'Laporan berhasil diperbarui.');
    // }

    public function update(Request $request, $id)
    {
        // Validasi input (ubah damage_description jadi nullable jika ingin optional)
        $validated = $request->validate([
            'damage_impact' => 'required|string|max:255',
            'damage_description' => 'required|string', // ubah ke 'nullable|string' jika opsional
        ]);

        $report = RepairReport::findOrFail($id);

        // Hitung damage_point berdasarkan damage_impact
        $damagePoint = match ($validated['damage_impact']) {
            'Keselamatan pengguna' => 75,
            'Penghentian operasional' => 35,
            'Menghambat pekerjaan' => 5,
            default => 0,
        };

        $specialRooms = ['ruang pimpinan', 'ruang rapat'];
        $specialBuildings = ['auditorium', 'masjid al-fatih', 'asrama mahasiswa'];

        $roomName = strtolower($report->room->room_name ?? '');
        $buildingName = strtolower($report->building->building_name ?? '');

        if (in_array($roomName, $specialRooms) || in_array($buildingName, $specialBuildings)) {
            $damagePoint += 40;
        }

        // Update data laporan, termasuk damage_point
        $report->update([
            'damage_impact' => $validated['damage_impact'],
            'damage_description' => $validated['damage_description'],
            'damage_point' => $damagePoint,
        ]);

        return redirect()->back()->with('success', 'Laporan berhasil diperbarui.');
    }



    public function updateAdmin(Request $request, RepairReport $repairReport)
    {
        // Validasi input
        $request->validate([
            'damage_impact' => 'required|string|max:255',
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

        $duplicateReports = RepairReport::where('id_room', $repairReport->id_room)
            ->where('id_facility_room', $repairReport->id_facility_room)
            ->where('id_building', $repairReport->id_building)
            ->where('id_facility_building', $repairReport->id_facility_building)
            ->whereNotIn('status', ['Selesai', 'Ditolak', 'Dibatalkan'])
            ->get();

        foreach ($duplicateReports as $dupReport) {
            $dupReport->update([
                'damage_impact' => $request->damage_impact,
                'damage_point' => $damagePoint,
            ]);
        }

        return redirect()->back()->with('success', 'Laporan berhasil diperbarui.');
    }


    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(RepairReport $repairReport)
    // {
    //     $repairReport->histories()->delete();
    //     $repairReport->repairTechnicians()->delete();
    //     $repairReport->complaints()->delete();

    //     if ($repairReport->schedules) {
    //         $repairReport->schedules()->delete();
    //     }

    //     // Hapus file foto kerusakan jika ada
    //     if ($repairReport->damage_photo && Storage::exists($repairReport->damage_photo)) {
    //         Storage::delete($repairReport->damage_photo);
    //     }

    //     // Hapus laporan perbaikan
    //     $repairReport->delete();

    //     return redirect()->back()->with('success', 'Laporan berhasil dihapus.');
    // }

    public function destroy($id)
    {
        $report = RepairReport::findOrFail($id);

        // Hapus file foto jika ada
        if ($report->damage_photo && Storage::exists($report->damage_photo)) {
            Storage::delete($report->damage_photo);
        }

        // Hapus semua relasi terkait
        $report->histories()->delete();
        $report->repairTechnicians()->delete();
        $report->complaints()->delete();
        $report->schedules()->delete();

        // Hapus laporan utama
        $report->delete();

        return redirect()->back()->with('success', 'Laporan berhasil dihapus.');
    }
}
