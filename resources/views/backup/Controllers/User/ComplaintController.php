<?php

namespace App\Http\Controllers\User;

use App\Models\Complaint;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $request->validate([
            'report_id' => 'required|exists:repair_report,id', // pastikan laporan ada
            'keluhan' => 'required|string|min:5',
        ]);

        Complaint::create([
            'id_report' => $request->report_id,
            'id_user' => Auth::id(),
            'complaint_description' => $request->keluhan,
        ]);

        return redirect()->back()->with('success', 'Keluhan berhasil dikirim.');
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
    public function show(Complaint $complaint)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Complaint $complaint)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Complaint $complaint)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Complaint $complaint)
    {
        //
    }
}