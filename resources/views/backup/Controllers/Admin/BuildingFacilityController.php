<?php

namespace App\Http\Controllers\Admin;

use App\Models\BuildingFacility;
use App\Http\Controllers\Controller;
use App\Models\Building;
use Illuminate\Http\Request;

class BuildingFacilityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
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

        return view('admin.dataFasilitasGedungAdmin', compact('buildings', 'indoorFacilities', 'outdoorFacilities'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Validasi data input
        $validatedData = $request->validate([
            'id_building'   => 'required|integer|exists:building,id',
            'facility_name' => 'required|string|max:255',
            'location'      => 'required|string|max:255',
            'description'   => 'nullable|string'
        ]);

        BuildingFacility::create($validatedData);

        return redirect('/admin-data-fasilitas-gedung')->with('success', 'Data gedung berhasil ditambahkan.');
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
    public function show(BuildingFacility $buildingFacility)
    {
        //
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
    public function update(Request $request, BuildingFacility $buildingFacility)
    {
        // dd($request->all());
        $request->validate([
            'facility_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Update data gedung
        $buildingFacility->update([
            'facility_name' => $request->facility_name,
            'description' => $request->description,
        ]);

        // Redirect ke halaman dengan pesan sukses
        return redirect('/admin-data-fasilitas-gedung')->with('success', 'Fasilitas Gedung berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BuildingFacility $buildingFacility)
    {
        $buildingFacility->delete();

        return redirect('/admin-data-fasilitas-gedung')->with('success', 'Fasilitas Gedung berhasil dihapus.');
    }
}