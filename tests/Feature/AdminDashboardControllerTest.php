<?php

namespace tests\Feature;

use GuzzleHttp\Middleware;
use Tests\TestCase;
use App\Models\User;
use App\Models\RepairReport;
use App\Models\Notification;
use App\Models\Technician;
use App\Models\Complaint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;


class AdminDashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_access_dashboard_with_correct_data()
    {
        $this->withoutMiddleware();
        // Buat user sebagai admin
        $admin = User::factory()->create([
            'role' => 'admin', // Pastikan kamu punya kolom 'role'
        ]);

        // Login sebagai admin
        $this->actingAs($admin);

        // Buat dummy data RepairReport
        RepairReport::factory()->create(['status' => 'Diproses']);
        RepairReport::factory()->create(['status' => 'Selesai']);
        RepairReport::factory()->create(['status' => 'Ditolak']);
        RepairReport::factory()->create(['status' => 'Dibatalkan']); // Tidak akan dihitung

        // Buat dummy notifikasi
        Notification::factory()->count(3)->create([
            'id_user' => $admin->id,
        ]);

        // Akses dashboard
        $response = $this->get('/admin-dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('admin.adminDashboard');

        // Pastikan data tersedia di view
        $response->assertViewHasAll([
            'permintaanPerbaikan',
            'sedangDiproses',
            'perbaikanSelesai',
            'laporanTerakhir',
            'perbaikanDitolak',
            'notifications',
        ]);
    }

    /** @test */
    public function admin_can_view_daftar_permintaan_perbaikan_with_correct_data()
    {
        $this->withoutMiddleware();
        // Buat user admin dan login
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // Buat beberapa repair report dengan berbagai status
        RepairReport::factory()->create(['status' => 'Diproses', 'damage_point' => 10]);
        RepairReport::factory()->create(['status' => 'Dijadwalkan', 'damage_point' => 5]);
        RepairReport::factory()->create(['status' => 'Selesai']); // Tidak muncul di daftar
        RepairReport::factory()->create(['status' => 'Ditolak']); // Tidak muncul di daftar

        // Buat data teknisi
        Technician::factory()->count(3)->create();

        // Buat beberapa notifikasi untuk admin
        Notification::factory()->count(2)->create(['id_user' => $admin->id]);

        // Kirim request dengan query parameter id
        $selectedId = RepairReport::first()->id;
        $response = $this->get('/admin-daftar-permintaan-perbaikan?id=' . $selectedId);

        $response->assertStatus(200);
        $response->assertViewIs('admin.adminDaftarPermintaanPerbaikan');

        $response->assertViewHasAll([
            'RepairReports',
            'TeknisiLists',
            'selectedId',
            'notifications',
        ]);

        // Pastikan selectedId sesuai query param
        $this->assertEquals($selectedId, $response->viewData('selectedId'));

        // Pastikan hanya repair report yang statusnya bukan selesai/ditolak/dibatalkan
        $repairReports = $response->viewData('RepairReports');
        foreach ($repairReports as $report) {
            $this->assertNotContains($report->status, ['Selesai', 'Ditolak', 'Dibatalkan']);
        }

        // Pastikan teknisi list tersedia dan tidak kosong
        $this->assertNotEmpty($response->viewData('TeknisiLists'));

        // Pastikan notifikasi ada dan usernya sesuai
        $notifications = $response->viewData('notifications');
        foreach ($notifications as $notif) {
            $this->assertEquals($admin->id, $notif->id_user);
        }
    }

    /** @test */
    public function admin_can_view_riwayat_perbaikan_with_correct_data()
    {
        $this->withoutMiddleware();
        // Buat user admin dan login
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // Buat repair reports dengan status selesai dan ditolak
        RepairReport::factory()->create(['status' => 'Selesai']);
        RepairReport::factory()->create(['status' => 'Ditolak']);

        // Buat repair reports dengan status lain (tidak akan muncul)
        RepairReport::factory()->create(['status' => 'Diproses']);

        // Akses route adminRiwayatPerbaikanView (ubah sesuai route kamu)
        $response = $this->get('/admin-riwayat-perbaikan');

        $response->assertStatus(200);
        $response->assertViewIs('admin.adminRiwayatPerbaikan');

        $response->assertViewHas('RepairReports');

        $repairReports = $response->viewData('RepairReports');

        // Pastikan hanya repair reports dengan status Selesai dan Ditolak yang diambil
        foreach ($repairReports as $report) {
            $this->assertContains($report->status, ['Selesai', 'Ditolak']);
        }
    }

    /** @test */
    public function admin_can_view_data_teknisi()
    {

        $this->withoutMiddleware(VerifyCsrfToken::class);
        // Buat user admin dan login
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // Buat beberapa data teknisi
        Technician::factory()->count(3)->create();

        // Akses route adminDataTeknisiView (ubah sesuai route kamu)
        $response = $this->get('/admin-data-teknisi');

        $response->assertStatus(200);
        $response->assertViewIs('admin.adminDataTeknisi');

        $response->assertViewHas('Technicians');

        $technicians = $response->viewData('Technicians');

        // Pastikan jumlah teknisi sesuai yang dibuat
        $this->assertCount(3, $technicians);
    }

    // /** @test */
    // public function it_requires_all_fields_and_valid_file()
    // {
    //     $this->withoutMiddleware(VerifyCsrfToken::class); // Nonaktifkan CSRF

    //     // $response = $this->get('/admin-data-teknisi');
    //     // dd($response->exception);

    //     $user = User::factory()->create();
    //     $this->actingAs($user);

    //     // Submit tanpa data apapun
    //     $response = $this->post('/upload-perbaikan', []); // ganti dengan route sebenarnya

    //     $response->assertSessionHasErrors(['id_report', 'repair_photo', 'repair_description']);
    // }

    // /** @test */
    // public function it_uploads_repair_photo_and_updates_report()
    // {
    //     // $response = $this->get('/admin-data-teknisi');
    //     // dd($response->exception);

    //     Storage::fake('public');

    //     $user = User::factory()->create();
    //     $this->actingAs($user);

    //     // Buat dummy repair report
    //     $report = RepairReport::factory()->create();

    //     $file = UploadedFile::fake()->image('repair.jpg');

    //     $response = $this->post('/upload-perbaikan', [
    //         'id_report' => $report->id,
    //         'repair_photo' => $file,
    //         'repair_description' => 'Perbaikan selesai dengan baik.',
    //     ]);

    //     $response->assertRedirect();
    //     $response->assertSessionHas('success', 'Data perbaikan berhasil disimpan.');

    //     // Cek file tersimpan di storage
    //     Storage::disk('public')->assertExists(str_replace('public/', '', $report->fresh()->repair_photo));

    //     // Cek database update
    //     $this->assertEquals('Perbaikan selesai dengan baik.', $report->fresh()->repair_description);
    // }

    // public function test_isi_teknisi_requires_all_fields_and_valid_data()
    // {
    //     $this->withoutMiddleware(VerifyCsrfToken::class);

    //     $user = User::factory()->create();
    //     $this->actingAs($user);

    //     // Submit tanpa data
    //     $response = $this->post('/isi-teknisi', []);
    //     $response->assertSessionHasErrors(['id_report', 'nama_teknisi', 'deskripsi_pekerjaan']);

    //     // Submit dengan id_report yang tidak ada, nama_teknisi bukan array, dan deskripsi kosong
    //     $response = $this->post('/isi-teknisi', [
    //         'id_report' => 9999,
    //         'nama_teknisi' => 'not-an-array',
    //         'deskripsi_pekerjaan' => '',
    //     ]);
    //     $response->assertSessionHasErrors(['id_report', 'nama_teknisi', 'deskripsi_pekerjaan']);
    // }

    // public function test_isi_teknisi_saves_data_and_redirects()
    // {
    //     $this->withoutMiddleware(VerifyCsrfToken::class);

    //     $user = User::factory()->create();
    //     $this->actingAs($user);

    //     // Buat data laporan dan teknisi
    //     $report = RepairReport::factory()->create();
    //     $technician1 = Technician::factory()->create();
    //     $technician2 = Technician::factory()->create();

    //     $payload = [
    //         'id_report' => $report->id,
    //         'nama_teknisi' => [$technician1->id, $technician2->id],
    //         'deskripsi_pekerjaan' => 'Pekerjaan selesai dengan baik.',
    //     ];

    //     $response = $this->post('/isi-teknisi', $payload);

    //     $response->assertRedirect();
    //     $response->assertSessionHas('success', 'Laporan berhasil diperbarui dengan teknisi.');

    //     // Cek data teknisi sudah tersimpan
    //     $this->assertDatabaseHas('repair_technicians', [
    //         'id_report' => $report->id,
    //         'id_technisian' => $technician1->id,
    //         'description_work' => 'Pekerjaan selesai dengan baik.',
    //     ]);
    //     $this->assertDatabaseHas('repair_technicians', [
    //         'id_report' => $report->id,
    //         'id_technisian' => $technician2->id,
    //         'description_work' => 'Pekerjaan selesai dengan baik.',
    //     ]);

    //     // Cek status laporan sudah berubah jadi 'Selesai'
    //     $this->assertEquals('Selesai', $report->fresh()->status);

    //     // Cek history perbaikan sudah dibuat
    //     $this->assertDatabaseHas('repair_histories', [
    //         'id_report' => $report->id,
    //         'status' => 'Selesai',
    //     ]);
    // }

    public function test_hapus_laporan_berhasil_dengan_status_diproses()
    {
        $this->withoutMiddleware();

        $user = User::factory()->create();
        $report = RepairReport::factory()->create([
            'status' => 'Diproses',
            'id_user' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete('/hapus-laporan', [
            'id_report' => $report->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Laporan berhasil dihapus.');
        $this->assertDatabaseMissing('repair_report', ['id' => $report->id]);
    }

    public function test_hapus_laporan_berhasil_dengan_status_ditolak()
    {
        $this->withoutMiddleware();

        $user = User::factory()->create();
        $report = RepairReport::factory()->create([
            'status' => 'Ditolak',
            'id_user' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete('/hapus-laporan', [
            'id_report' => $report->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Laporan berhasil dihapus.');
        $this->assertDatabaseMissing('repair_report', ['id' => $report->id]);
    }

    public function test_hapus_laporan_gagal_dengan_status_selesai()
    {
        $this->withoutMiddleware();

        $user = User::factory()->create();
        $report = RepairReport::factory()->create([
            'status' => 'Selesai',
            'id_user' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete('/hapus-laporan', [
            'id_report' => $report->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Laporan tidak dapat dihapus karena statusnya tidak sesuai.');
        $this->assertDatabaseHas('repair_report', ['id' => $report->id]);
    }

    // public function test_admin_can_view_daftar_keluhan()
    // {
    //     $this->withoutMiddleware();

    //     // 1. Buat user admin dan login
    //     $admin = User::factory()->create([
    //         'role' => 'admin',
    //     ]);

    //     // 2. Buat data RepairReport
    //     $repairReport = RepairReport::factory()->create([
    //         'status' => 'Diproses',
    //     ]);

    //     // 3. Buat keluhan (Complaint)
    //     $complaint = Complaint::factory()->create([
    //         'id_user' => $admin->id,
    //         'id_report' => $repairReport->id,
    //     ]);

    //     // 4. Akses halaman daftar keluhan
    //     $response = $this->actingAs($admin)->get('/admin-daftar-keluhan');

    //     // 5. Validasi halaman tampil
    //     $response->assertStatus(200);
    //     $response->assertViewIs('admin.adminDaftarKeluhan');
    //     $response->assertViewHas('Complaints', function ($complaints) use ($complaint) {
    //         return $complaints->contains($complaint);
    //     });
    // }

    public function test_admin_can_view_profile()
    {
        // 1. Buat user admin
        $admin = User::factory()->create([
            'role' => 'admin', // sesuaikan jika ada pengecekan role admin
        ]);

        // 2. Login sebagai admin dan akses halaman profil
        $response = $this->actingAs($admin)->get('/admin-profile'); // pastikan URL-nya sesuai route

        // 3. Pastikan response berhasil dan view yang tepat ditampilkan
        $response->assertStatus(200);
        $response->assertViewIs('admin.adminProfile');

        // 4. Pastikan view menerima data 'user' yang sama dengan admin
        $response->assertViewHas('user', function ($viewUser) use ($admin) {
            return $viewUser->id === $admin->id;
        });
    }
    public function test_admin_can_update_profile_name()
    {
        $this->withoutMiddleware();

        $admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'Old Name',
        ]);

        $response = $this->actingAs($admin)->patch('/update-admin-profile', [
            'name' => 'New Name',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Nama profil berhasil diperbarui.');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'New Name',
        ]);
    }

    public function test_update_profile_name_validation_error()
    {
        $this->withoutMiddleware();

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->patch('/update-admin-profile', [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }
}
