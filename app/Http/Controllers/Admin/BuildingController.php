<?php

namespace App\Http\Controllers\Admin;

use App\Models\Building;
use App\Models\Room;
use App\Models\Notification;
use App\Models\RoomFacility;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


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
        $validator = Validator::make($request->all(), [
            'building_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('building', 'building_name'),
            ],
            'description' => 'nullable|string',
        ], [
            'building_name.required' => 'Nama gedung wajib diisi.',
            'building_name.unique' => 'Nama gedung sudah ada dalam data.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 422); // error validasi dikembalikan seperti changePassword
        }

        try {
            Building::create([
                'building_name' => $request->building_name,
                'description' => $request->description,
            ]);

            return response()->json([
                'message' => 'Gedung berhasil ditambahkan.'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan data.'
            ], 500);
        }
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
    // public function update(Request $request, Building $building)
    // {
    //     $request->validate([
    //         'building_name' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //     ]);

    //     // Debug sebelum update
    //     // dd('sebelum update', $building->toArray());

    //     $building->update([
    //         'building_name' => $request->building_name,
    //         'description' => $request->description,
    //     ]);

    //     // Debug sesudah update
    //     // dd('sesudah update', $building->fresh()->toArray());

    //     return redirect('/admin-data-gedung')->with('success', 'Gedung berhasil diperbarui.');
    // }

    public function update(Request $request, Building $building)
    {
        // dd($request->all()); // cek data

        $request->validate([
            'building_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $building->update([
            'building_name' => $request->building_name,
            'description' => $request->description,
        ]);

        // dd($building->fresh()->toArray());

        return redirect('/admin-data-gedung')->with('success', 'Gedung berhasil diperbarui.');
    }

    // public function update(Request $request, Building $id)
    // {
    //     // dd($request->all()); // cek data

    //     $request->validate([
    //         'building_name' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //     ]);

    //     $id->update([
    //         'building_name' => $request->building_name,
    //         'description' => $request->description,
    //     ]);

    //     dd($id->fresh()->toArray());
    //     return redirect('/admin-data-gedung')->with('success', 'Gedung berhasil diperbarui.');
    // }


    /**
     * Remove the specified resource from storage.
     */


    public function destroy(Building $building)
    {
        Log::info('Menerima request delete untuk building:', ['id' => $building->id]);

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

            // ✅ LOG setelah berhasil hapus
            Log::info('Building deleted:', ['id' => $building->id]);

            return redirect()->back()->with('success', 'Gedung dan semua data terkait berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal menghapus gedung: ' . $e->getMessage());
        }
    }
}