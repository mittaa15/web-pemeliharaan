<?php

namespace App\Http\Controllers\Admin;

use App\Models\BuildingFacility;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BuildingFacilityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $buildings = Building::all();

        // Ambil fasilitas indoor
        $facilities = BuildingFacility::with('building:id,building_name', 'repairReports')
            ->get();

        return view('admin.dataFasilitasGedungAdmin', compact('buildings', 'facilities'));
    }


    /**
     * Show the form for creating a new resource.
     */

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'id_building' => [
                'required',
                'integer',
                'exists:building,id'
            ],
            'facility_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('building_facility')->where(function ($query) use ($request) {
                    return $query->where('id_building', $request->id_building);
                }),
            ],
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'facility_name.unique' => 'Nama fasilitas untuk gedung ini sudah ada, silakan gunakan nama lain.',
            'id_building.required' => 'Gedung wajib dipilih.',
            'facility_name.required' => 'Nama fasilitas wajib diisi.',
            'location.required' => 'Lokasi fasilitas wajib diisi.',
        ]);

        // Simpan ke database
        BuildingFacility::create($validatedData);

        return response()->json(['message' => 'Fasilitas berhasil ditambahkan.']);
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
    // public function show(BuildingFacility $buildingFacility)
    // {
    //     $buildingFacility->load(['building', 'repairReports']);

    //     return response()->json([
    //         'status' => true,
    //         'data' => $buildingFacility
    //     ]);
    // }

    public function show($id)
    {
        $facility = BuildingFacility::with(['building', 'repairReports'])->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $facility->id,
                'id_building' => $facility->id_building,
                'facility_name' => $facility->facility_name,
                'building' => $facility->building,
                'repair_reports' => $facility->repairReports,
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BuildingFacility $buildingFacility)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, BuildingFacility $buildingFacility)
    // {
    //     // dd($request->all());
    //     $request->validate([
    //         'facility_name' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //     ]);

    //     // Update data gedung
    //     $buildingFacility->update([
    //         'facility_name' => $request->facility_name,
    //         'description' => $request->description,
    //     ]);

    //     // Redirect ke halaman dengan pesan sukses
    //     return redirect('/admin-data-fasilitas-gedung')->with('success', 'Fasilitas Gedung berhasil diperbarui.');
    // }

    public function update(Request $request, $id)
    {
        $facility = BuildingFacility::findOrFail($id);

        $validated = $request->validate([
            'facility_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            // validasi lainnya jika ada
        ]);

        $facility->update($validated);

        return redirect('/admin-data-fasilitas-gedung')->with('success', 'Fasilitas Gedung berhasil diperbarui.');
    }



    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(BuildingFacility $buildingFacility)
    // {
    //     $buildingFacility->delete();

    //     return redirect('/admin-data-fasilitas-gedung')->with('success', 'Fasilitas Gedung berhasil dihapus.');
    // }
    public function destroy(BuildingFacility $buildingFacility)
    {
        $buildingFacility->delete();

        return redirect('/admin-data-fasilitas-gedung')->with('success', 'Fasilitas Gedung berhasil dihapus.');
    }
}