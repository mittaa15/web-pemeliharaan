<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function adminDashboardView()
    {
        return view('admin.adminDashboard');
    }
    public function dataGedungAdminView()
    {
        return view('admin.dataGedungAdmin');
    }
    public function dataFasilitasGedungAdminView()
    {
        return view('admin.dataFasilitasGedungAdmin');
    }
    public function dataFasilitasRuangAdminView()
    {
        return view('admin.dataFasilitasRuangAdmin');
    }
    public function adminProfileView()
    {
        return view('admin.adminProfile');
    }
    public function adminDaftarPermintaanPerbaikanView()
    {
        return view('admin.adminDaftarPermintaanPerbaikan');
    }
    public function adminRiwayatPerbaikanView()
    {
        return view('admin.adminRiwayatPerbaikan');
    }
    public function dataRuangAdminView()
    {
        return view('admin.dataRuangAdmin');
    }
}