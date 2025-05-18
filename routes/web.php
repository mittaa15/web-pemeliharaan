<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\DashboardController;
use Illuminate\Http\Request;
use App\Http\Controllers\Sarpras\SarprasDashboardController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\BuildingController;
use App\Http\Controllers\Admin\BuildingFacilityController;
use App\Http\Controllers\Admin\RoomFacilityController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\User\RepairReportController;

Route::middleware(['auth.common'])->group(function () {
    Route::get('/', [AuthController::class, 'loginview']);
    Route::post('/login', [AuthController::class, 'loginHandler'])->name('login');
    Route::get('/register', [AuthController::class, 'registerview']);
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::get('/forget-password', [AuthController::class, 'forgetPasswordView']);
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail']);
    // Route::get('/reset-password/{token}', [AuthController::class, 'resetPasswordView'])->name('password.reset');
    Route::get('/reset-password/{token}', function (Request $request, $token) {
        return view('auth.resetPassword', [ // nama view kamu
            'token' => $token,
            'email' => $request->query('email')
        ]);
    })->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'auth.user'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboardView']);
    Route::get('/form-pelaporan',  [RepairReportController::class, 'index']);
    Route::post('/create-pelaporan',  [RepairReportController::class, 'create'])->name('create-laporan');
    Route::get('/daftar-permintaan', [DashboardController::class, 'daftarPermintaanView']);
    Route::get('/ajukan-keluhan', [DashboardController::class, 'ajukanKeluhanView']);
    Route::get('/detail-laporan', [DashboardController::class, 'detailLaporanView']);
    Route::get('/profile', [DashboardController::class, 'profileView']);
    Route::get('/riwayat-laporan-perbaikan', [DashboardController::class, 'riwayatLaporanPerbaikanView']);
});

Route::middleware(['auth', 'auth.sarpras'])->group(function () {
    Route::get('/sarpras-dashboard', [SarprasDashboardController::class, 'sarprasDashboardView']);
    Route::get('/daftar-permintaan-perbaikan', [SarprasDashboardController::class, 'daftarPermintaanPerbaikanView']);
    Route::get('/sarpras-profile', [SarprasDashboardController::class, 'sarprasProfileView']);
    Route::get('/riwayat-perbaikan', [SarprasDashboardController::class, 'riwayatPerbaikanView']);
    Route::get('/data-gedung', [SarprasDashboardController::class, 'dataGedungView']);
    Route::get('/data-fasilitas-gedung', [SarprasDashboardController::class, 'dataFasilitasGedungView']);
    Route::get('/sarpras-data-ruang', [SarprasDashboardController::class, 'sarprasDataRuangView']);
    Route::get('/data-fasilitas-ruang', [SarprasDashboardController::class, 'dataFasilitasRuangView']);
});

Route::middleware(['auth', 'auth.admin'])->group(function () {
    Route::get('/admin-dashboard', [AdminDashboardController::class, 'adminDashboardView']);
    Route::get('/admin-data-gedung', [BuildingController::class, 'index']);
    Route::post('/create-building', [BuildingController::class, 'create'])->name('create_building');
    Route::patch('/update-gedung/{building}', [BuildingController::class, 'update'])->name('update_building');
    Route::delete('/delete-gedung/{building}', [BuildingController::class, 'destroy'])->name('delete_building');
    Route::get('/admin-data-fasilitas-gedung', [BuildingFacilityController::class, 'index']);
    Route::post('/create-fasilitas-gedung', [BuildingFacilityController::class, 'create'])->name('create_building_facility');
    Route::patch('/update-facility-gedung/{buildingFacility}', [BuildingFacilityController::class, 'update'])->name('update_building_facility');
    Route::delete('/delete-facility-gedung/{buildingFacility}', [BuildingFacilityController::class, 'destroy'])->name('delete_building_facility');
    Route::get('/admin-data-ruang', [RoomController::class, 'index'])->name('admin.dataRuang');
    Route::post('/create-room', [RoomController::class, 'create'])->name('create_room');
    Route::patch('/update-room/{room}', [RoomController::class, 'update'])->name('update_room');
    Route::delete('/delete-room/{room}', [RoomController::class, 'destroy'])->name('delete_room');
    Route::get('/admin-data-fasilitas-ruang', [RoomFacilityController::class, 'index']);
    Route::post('/create-room-facility', [RoomFacilityController::class, 'create'])->name('create_room_facility');
    Route::patch('/update-facility-room/{roomFacility}', [RoomFacilityController::class, 'update'])->name('update_room_facility');
    Route::delete('/delete-facility-room/{roomFacility}', [RoomFacilityController::class, 'destroy'])->name('delete_room_facility');
    Route::get('/admin-profile', [AdminDashboardController::class, 'adminProfileView']);
    Route::get('/admin-daftar-permintaan-perbaikan', [AdminDashboardController::class, 'adminDaftarPermintaanPerbaikanView']);
    Route::get('/admin-riwayat-perbaikan', [AdminDashboardController::class, 'adminRiwayatPerbaikanView']);
});
