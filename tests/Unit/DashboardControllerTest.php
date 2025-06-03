<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\User;
use App\Models\Building;
use App\Models\Room;
use App\Models\RoomFacility;
use App\Models\BuildingFacility;
use App\Models\RepairReport;
use App\Models\RepairHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_dashboard_with_all_expected_data()
    {
        // Setup: buat user dan login sebagai user
        $user = User::factory()->create([
            'role' => 'user' // sesuaikan jika ada sistem role
        ]);

        // Buat data dummy untuk dashboard
        $building = Building::factory()->create();
        $room = Room::factory()->create();

        $roomFacility = RoomFacility::factory()->create([
            'id_room' => $room->id,
        ]);

        $buildingFacility = BuildingFacility::factory()->create();

        // Buat repair report dengan status berbeda
        RepairReport::factory()->create(['id_user' => $user->id, 'status' => 'Diproses']);
        RepairReport::factory()->create(['id_user' => $user->id, 'status' => 'Dijadwalkan']);
        RepairReport::factory()->create(['id_user' => $user->id, 'status' => 'Dalam Proses Pengerjaan']);
        RepairReport::factory()->create(['id_user' => $user->id, 'status' => 'Selesai']);
        RepairReport::factory()->create(['id_user' => $user->id, 'status' => 'Ditolak']);

        // Akses halaman dashboard sebagai user
        $response = $this->actingAs($user)->get('/dashboard');

        // Pastikan berhasil
        $response->assertStatus(200);

        // Pastikan view yang digunakan
        $response->assertViewIs('user.dashboard');

        // Pastikan semua variabel tersedia di view
        $response->assertViewHasAll([
            'buildings',
            'rooms',
            'indoorFacilities',
            'outdoorFacilities',
            'jumlahDiproses',
            'jumlahDijadwalkan',
            'jumlahPengerjaan',
            'jumlahSelesai',
            'jumlahDitolak',
        ]);

        // Optional: Cek isi jumlah
        $this->assertEquals(1, $response->viewData('jumlahDiproses'));
        $this->assertEquals(1, $response->viewData('jumlahDijadwalkan'));
        $this->assertEquals(1, $response->viewData('jumlahPengerjaan'));
        $this->assertEquals(1, $response->viewData('jumlahSelesai'));
        $this->assertEquals(1, $response->viewData('jumlahDitolak'));
    }

    /** @test */
    public function test_user_can_view_daftar_permintaan_with_filtered_reports()
    {
        // Arrange: buat user dan login
        $user = User::factory()->create();
        $this->actingAs($user);

        // Buat data repair report dengan berbagai status
        $includedStatuses = ['Diproses', 'Dijadwalkan', 'Dalam Proses Pengerjaan', 'Pengecekan Akhir'];
        $excludedStatuses = ['Selesai', 'Ditolak', 'Dibatalkan'];

        foreach ($includedStatuses as $status) {
            RepairReport::factory()->create([
                'id_user' => $user->id,
                'status' => $status,
            ]);
        }

        foreach ($excludedStatuses as $status) {
            RepairReport::factory()->create([
                'id_user' => $user->id,
                'status' => $status,
            ]);
        }

        // Act: akses route daftarPermintaanView
        $response = $this->get('/daftar-permintaan'); // Sesuaikan route jika berbeda

        // Assert: respon sukses dan view benar
        $response->assertStatus(200);
        $response->assertViewIs('user.daftarPermintaan');
        $response->assertViewHas('RepairReports');

        // Pastikan hanya status yang diizinkan yang ditampilkan
        $viewReports = $response->viewData('RepairReports');
        $this->assertCount(count($includedStatuses), $viewReports);

        foreach ($viewReports as $report) {
            $this->assertNotContains($report->status, $excludedStatuses);
        }
    }

    /** @test */
    public function test_user_can_view_profile()
    {
        // 1. Buat user dummy dengan factory (pastikan kamu sudah punya User factory)
        $user = User::factory()->create();

        // 2. Login sebagai user tersebut
        $response = $this->actingAs($user)->get('/profile');

        // 3. Assert response berhasil (status 200)
        $response->assertStatus(200);

        // 4. Assert view yang digunakan adalah 'user.profile'
        $response->assertViewIs('user.profile');

        // 5. Assert view menerima data 'user' yang sama dengan user yang login
        $response->assertViewHas('user', function ($viewUser) use ($user) {
            return $viewUser->id === $user->id;
        });
    }

    /** @test */
    public function test_user_can_view_riwayat_laporan_perbaikan()
    {
        // 1. Siapkan user dan login
        $user = User::factory()->create();

        // 2. Buat beberapa RepairReport untuk user ini dengan status tertentu
        $reports = RepairReport::factory()
            ->count(3)
            ->for($user, 'user') // Pastikan relasi id_user sesuai
            ->state(function () {
                return ['status' => 'Selesai'];
            })
            ->create();

        // Buat juga laporan dengan status lain agar tidak muncul
        RepairReport::factory()->create([
            'id_user' => $user->id,
            'status' => 'Diproses',
        ]);

        // 3. Akses route yang memanggil method riwayatLaporanPerbaikanView
        // Pastikan route-nya sudah ada dan punya nama misal 'riwayat-laporan'
        $response = $this->actingAs($user)->get(route('riwayat-laporan-perbaikan'));

        // 4. Assert status OK dan view yang dipanggil
        $response->assertStatus(200);
        $response->assertViewIs('user.riwayatLaporanPerbaikan');

        // 5. Assert view memiliki variabel RepairReports dan data sesuai
        $response->assertViewHas('RepairReports');

        $repairReports = $response->viewData('RepairReports');

        // Cek apakah hanya laporan user dan status tertentu yang diambil
        foreach ($repairReports as $report) {
            $this->assertEquals($user->id, $report->id_user);
            $this->assertContains($report->status, ['Selesai', 'Ditolak', 'Dibatalkan']);
        }

        // Cek jumlah laporan yang muncul sama dengan yang dibuat dengan status filter
        $this->assertCount(3, $repairReports);
    }

    public function user_can_view_riwayat_status_with_correct_history_data()
    {
        $user = User::factory()->create();

        $report = RepairReport::factory()->create(['id_user' => $user->id]);

        $history1 = RepairHistory::factory()->create([
            'id_report' => $report->id,
            'created_at' => now()->subDays(2),
        ]);
        $history2 = RepairHistory::factory()->create([
            'id_report' => $report->id,
            'created_at' => now()->subDay(),
        ]);

        // Request dengan query param ?id=...
        $response = $this->actingAs($user)->get(route('riwayat-status', ['id' => $report->id]));

        $response->assertStatus(200);
        $response->assertViewIs('user.repairDetail');
        $response->assertViewHas('History');

        $histories = $response->viewData('History');
        $this->assertCount(2, $histories);
        $this->assertTrue($histories->contains($history1));
        $this->assertTrue($histories->contains($history2));
    }


    // public function test_user_can_view_repair_report_detail_with_latest_history()
    // {
    //     // Arrange: buat user dan login
    //     $user = User::factory()->create();
    //     $this->actingAs($user);

    //     // Buat repair report untuk user
    //     $report = RepairReport::factory()->create([
    //         'id_user' => $user->id,
    //     ]);

    //     // Tambahkan beberapa repair history
    //     RepairHistory::factory()->create([
    //         'id_report' => $report->id,
    //         'status' => 'Diproses',
    //         'created_at' => now()->subDays(2),
    //     ]);

    //     $latestHistory = RepairHistory::factory()->create([
    //         'id_report' => $report->id,
    //         'status' => 'Dijadwalkan',
    //         'created_at' => now()->subDay(),
    //     ]);

    //     // Act: akses halaman detail
    //     $response = $this->get("/repair-detail/{$report->id}"); // Ganti route jika perlu

    //     // Assert
    //     $response->assertStatus(200);
    //     $response->assertViewIs('user.repairDetail');
    //     $response->assertViewHasAll(['report', 'latestHistory']);

    //     // Cek isi data yang dilempar ke view
    //     $this->assertEquals($report->id, $response->viewData('report')->id);
    //     $this->assertEquals($latestHistory->id, $response->viewData('latestHistory')->id);
    // }
}
