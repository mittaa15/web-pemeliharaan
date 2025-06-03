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
use App\Http\Controllers\EntrypointController;
use App\Http\Controllers\Sarpras\RepairScheduleController;
use App\Http\Controllers\User\ComplaintController;
use App\Http\Controllers\User\RepairReportController;
use App\Http\Controllers\RepairHistoryController;


Route::get('/', [EntrypointController::class, 'index'])->name('/');
Route::middleware(['auth.common'])->group(function () {
    Route::get('/login', [AuthController::class, 'loginview']);
    Route::post('/login', [AuthController::class, 'loginHandler'])->name('login');
    Route::get('/register', [AuthController::class, 'registerview']);
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail']);
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
    Route::get('/dashboard', [DashboardController::class, 'dashboardView'])->name('dashboard');
    Route::get('/form-pelaporan',  [RepairReportController::class, 'index'])->name('repair-report.index');
    Route::post('/create-pelaporan',  [RepairReportController::class, 'create'])->name('create-laporan');
    Route::post('/create-keluhan',  [ComplaintController::class, 'create'])->name('create-keluhan');
    Route::post('/cancel-report',  [RepairReportController::class, 'edit'])->name('cancel-report');
    Route::get('/daftar-permintaan', [DashboardController::class, 'daftarPermintaanView']);
    Route::put('/laporan-user/{repairReport}', [RepairReportController::class, 'update'])->name('laporan.update');
    Route::get('/detail-report', [DashboardController::class, 'detail'])->name('repair-detail');
    // Route::get('/riwayat-status', [DashboardController::class, 'riwayatStatus'])->name('riwayat-status');
    Route::get('/riwayat-status/{id}', [DashboardController::class, 'riwayatStatus'])->name('riwayat-status');
    Route::get('/ajukan-keluhan', [DashboardController::class, 'ajukanKeluhanView']);
    Route::get('/detail-laporan', [DashboardController::class, 'detailLaporanView']);
    Route::get('/profile', [DashboardController::class, 'profileView']);
    Route::patch('/update-sarpras-user', [AdminDashboardController::class, 'updateProfile'])->name('update-profile-user');
    Route::post('/change-password-user', [AuthController::class, 'changePassword'])->name('change-password-user');
    Route::get('/riwayat-laporan-perbaikan', [DashboardController::class, 'riwayatLaporanPerbaikanView'])->name('riwayat-laporan-perbaikan');
});

Route::middleware(['auth', 'auth.sarpras'])->group(function () {
    Route::get('/sarpras-dashboard', [SarprasDashboardController::class, 'sarprasDashboardView'])->name('dashboard-sarpras');
    Route::get('/daftar-permintaan-perbaikan', [SarprasDashboardController::class, 'daftarPermintaanPerbaikanView']);
    Route::post('/update-schedule', [RepairScheduleController::class, 'store'])->name('update-schedule');
    Route::post('/update-status-sarpras', [RepairScheduleController::class, 'create'])->name('update-status-sarpras');
    Route::post('/upload-perbaikan-sarpras', [RepairScheduleController::class, 'uploadPerbaikan'])->name('upload-perbaikan-sarpras');
    Route::post('/update-repair-notes-sarpras', [RepairHistoryController::class, 'store'])->name('update-repair-notes-sarpras');
    Route::post('/reject', [RepairHistoryController::class, 'reject'])->name('reject-repair');
    Route::delete('/delete-report/{repairReport}', [RepairReportController::class, 'destroy'])->name('delete-report');
    Route::post('/isi-teknisi', [RepairScheduleController::class, 'isiTeknisi'])->name('isi-teknisi');
    Route::get('/sarpras-profile', [SarprasDashboardController::class, 'sarprasProfileView']);
    Route::get('/riwayat-perbaikan', [SarprasDashboardController::class, 'riwayatPerbaikanView']);
    Route::get('/data-gedung', [SarprasDashboardController::class, 'dataGedungView']);
    Route::patch('/update-sarpras-profile', [AdminDashboardController::class, 'updateProfile'])->name('update-profile-sarpras');
    Route::post('/change-password-sarpras', [AuthController::class, 'changePassword'])->name('change-password-sarpras');
    Route::get('/data-fasilitas-gedung', [SarprasDashboardController::class, 'dataFasilitasGedungView']);
    Route::put('/laporan/sarpras/{repairReport}', [RepairReportController::class, 'updateAdmin'])->name('laporan-update-sarpras');
    Route::get('/sarpras-data-ruang', [SarprasDashboardController::class, 'sarprasDataRuangView']);
    Route::get('/data-fasilitas-ruang', [SarprasDashboardController::class, 'dataFasilitasRuangView']);
    Route::get('/building-facility/{buildingFacility}', [BuildingFacilityController::class, 'show'])->name('show_building_facility');
    Route::get('/sarpras-data-teknisi', [SarprasDashboardController::class, 'sarprasDataTeknisiView']);
    Route::get('/sarpras/laporan-fasilitas/{id}', [SarprasDashboardController::class, 'getLaporanByFacility'])->name('get-laporan-facility');
    Route::get('/sarpras-daftar-keluhan', [RepairScheduleController::class, 'sarprasDaftarKeluhanView']);
});

Route::middleware(['auth', 'auth.admin'])->group(function () {
    Route::get('/admin-dashboard', [AdminDashboardController::class, 'adminDashboardView']);
    Route::get('/laporan/{id}', [RepairReportController::class, 'show'])->name('laporan.show');
    Route::get('/admin-data-gedung', [BuildingController::class, 'index'])->name('building_index');
    Route::post('/create-building', [BuildingController::class, 'create'])->name('create_building');
    Route::patch('/update-gedung/{building}', [BuildingController::class, 'update'])->name('update_building');
    Route::delete('/delete-gedung/{building}', [BuildingController::class, 'destroy'])->name('delete_building');
    Route::delete('/delete-report-admin/{repairReport}', [RepairReportController::class, 'destroy'])->name('delete-report-admin');
    Route::post('/update-schedule-admin', [RepairScheduleController::class, 'store'])->name('update-schedule-admin');
    Route::get('/admin-data-fasilitas-gedung', [BuildingFacilityController::class, 'index'])->name('fasilitas-gedung');
    Route::post('/reject-repair-admin', [RepairHistoryController::class, 'reject'])->name('reject-repair-admin');
    Route::post('/create-fasilitas-gedung', [BuildingFacilityController::class, 'create'])->name('create_building_facility');
    Route::patch('/update-facility-gedung/{buildingFacility}', [BuildingFacilityController::class, 'update'])->name('update_building_facility');
    Route::delete('/delete-facility-gedung/{buildingFacility}', [BuildingFacilityController::class, 'destroy'])->name('delete_building_facility');
    Route::get('/admin-data-ruang', [RoomController::class, 'index'])->name('admin.dataRuang');
    Route::post('/create-room', [RoomController::class, 'create'])->name('create_room');
    Route::patch('/update-room/{room}', [RoomController::class, 'update'])->name('update_room');
    Route::delete('/delete-room/{room}', [RoomController::class, 'destroy'])->name('delete_room');
    Route::get('/admin-data-fasilitas-ruang', [RoomFacilityController::class, 'index'])->name('admin-data-fasilitas-ruang');
    Route::post('/upload-perbaikan-admin', [RepairScheduleController::class, 'uploadPerbaikan'])->name('upload-perbaikan-admin');
    Route::post('/create-room-facility', [RoomFacilityController::class, 'create'])->name('create_room_facility');
    Route::patch('/update-facility-room/{roomFacility}', [RoomFacilityController::class, 'update'])->name('update_room_facility');
    Route::delete('/delete-facility-room/{roomFacility}', [RoomFacilityController::class, 'destroy'])->name('delete_room_facility');
    Route::get('/admin-profile', [AdminDashboardController::class, 'adminProfileView']);
    Route::patch('/update-admin-profile', [AdminDashboardController::class, 'updateProfile'])->name('update-profile');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change-password');
    Route::get('/admin-daftar-permintaan-perbaikan', [AdminDashboardController::class, 'adminDaftarPermintaanPerbaikanView']);
    Route::post('/update-repair-notes-admin', [RepairHistoryController::class, 'store'])->name('update-repair-notes-admin');
    Route::put('/laporan/{repairReport}', [RepairReportController::class, 'updateAdmin'])->name('laporan-update');
    Route::post('/create-teknisi', [AdminDashboardController::class, 'isiTeknisi'])->name('create-teknisi');
    Route::post('/update-status-admin', [RepairScheduleController::class, 'create'])->name('update-status-admin');
    Route::get('/admin-riwayat-perbaikan', [AdminDashboardController::class, 'adminRiwayatPerbaikanView']);
    Route::get('/admin-data-teknisi', [AdminDashboardController::class, 'adminDataTeknisiView']);
    Route::post('/upload-perbaikan', [AdminDashboardController::class, 'uploadPerbaikan'])->name('upload-perbaikan');
    Route::delete('/hapus-laporan', [AdminDashboardController::class, 'hapusLaporan'])->name('hapus-laporan');
    Route::patch('/update-data-teknisi/{id}', [RepairScheduleController::class, 'updateTechnician'])->name('update-technician');
    Route::post('/create-technician', [RepairScheduleController::class, 'createTechnician'])->name('create-technician');
    Route::delete('/delete-data-teknisi/{id}', [RepairScheduleController::class, 'destroyTechnician'])->name('delete-data-teknisi');
    Route::get('/admin-daftar-keluhan', [AdminDashboardController::class, 'adminDaftarKeluhanView']);
    Route::get('/sarpras/technicians', [RepairScheduleController::class, 'sarprasDataTeknisiView']);
});
