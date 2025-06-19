<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RepairReport;
use App\Models\Building;
use App\Models\BuildingFacility;
use App\Models\Room;
use App\Models\RoomFacility;
use App\Models\Technician;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SarprasDashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_sarpras_dashboard_view_returns_correct_data()
    {
        // Buat user sarpras dan login
        $user = User::factory()->create([
            'role' => 'sarpras'
        ]);
        $this->actingAs($user);

        // Buat data RepairReport
        RepairReport::factory()->create(['status' => 'Diproses']);
        RepairReport::factory()->create(['status' => 'Dijadwalkan']);
        RepairReport::factory()->create(['status' => 'Dalam Proses Pengerjaan']);
        RepairReport::factory()->create(['status' => 'Pengecekan Akhir']);
        RepairReport::factory()->create(['status' => 'Ditolak']);
        RepairReport::factory()->create(['status' => 'Selesai']);
        RepairReport::factory()->create(['status' => 'Dibatalkan']);

        $response = $this->get('/sarpras-dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('sarpras.sarprasDashboard');
        $response->assertViewHasAll([
            'permintaanPerbaikan',
            'sedangDiproses',
            'perbaikanSelesai',
            'laporanTerakhir',
            'perbaikanDitolak'
        ]);

        // Cek jumlah berdasarkan seed
        $response->assertViewHas('permintaanPerbaikan', 6); // Semua kecuali 'Dibatalkan'
        $response->assertViewHas('sedangDiproses', 5); // Termasuk 'Ditolak'
        $response->assertViewHas('perbaikanSelesai', 1);
        $response->assertViewHas('perbaikanDitolak', 1);
    }

    /** @test */
    public function daftar_permintaan_perbaikan_view_returns_correct_data()
    {
        // Buat user yang sudah login dengan role 'sarpras' (sesuaikan role atau middleware kamu)
        $sarprasUser = User::factory()->create([
            // misal kamu pakai kolom role untuk middleware auth.sarpras
            'role' => 'sarpras',
        ]);

        // Buat beberapa RepairReport untuk testing
        RepairReport::factory()->count(5)->create([
            'status' => 'Diproses',  // pastikan status sesuai syarat whereNotIn di controller
        ]);
        RepairReport::factory()->count(3)->create([
            'status' => 'Dalam Proses',
        ]);
        RepairReport::factory()->count(2)->create([
            'status' => 'Selesai',  // harus tidak muncul karena whereNotIn
        ]);

        // Login sebagai user sarpras
        $response = $this->actingAs($sarprasUser)
            ->get('/daftar-permintaan-perbaikan');

        // Assert response sukses dan view yang dipakai sesuai
        $response->assertStatus(200);
        $response->assertViewIs('sarpras.daftarPermintaanPerbaikan');

        // Assert view punya variabel RepairReports
        $response->assertViewHas('RepairReports');

        // Ambil data RepairReports dari view dan cek jumlahnya (harusnya exclude status 'Selesai')
        $repairReports = $response->viewData('RepairReports');
        $this->assertEquals(8, $repairReports->count());

        // Contoh cek data RepairReports sudah diurutkan sesuai kriteria:
        // 1. Status 'Diproses' di awal
        $firstStatus = $repairReports->first()->status;
        $this->assertEquals('Diproses', $firstStatus);

        // 2. Status 'Selesai' tidak muncul
        $this->assertFalse($repairReports->contains('status', 'Selesai'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function sarpras_profile_view_returns_correct_data()
    {
        // Arrange: Buat user dengan role 'sarpras'
        $sarprasUser = User::factory()->create([
            'role' => 'sarpras',
        ]);

        // Act: Login sebagai user dan akses halaman profil
        $response = $this->actingAs($sarprasUser)
            ->get('/sarpras-profile');

        // Assert: Cek respons dan isi view
        $response->assertStatus(200);
        $response->assertViewIs('sarpras.sarprasProfile');
        $response->assertViewHas('user', function ($user) use ($sarprasUser) {
            return $user->id === $sarprasUser->id;
        });
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function riwayat_perbaikan_view_returns_correct_data()
    {
        // Arrange: Buat user dengan role sarpras
        $sarprasUser = User::factory()->create([
            'role' => 'sarpras',
        ]);

        // Buat repair report yang sesuai filter
        $completedReport = RepairReport::factory()->create([
            'status' => 'Selesai',
        ]);

        // Buat repair report yang tidak sesuai filter
        $inProgressReport = RepairReport::factory()->create([
            'status' => 'Diproses',
        ]);

        // Act: Login sebagai user dan akses riwayat
        $response = $this->actingAs($sarprasUser)
            ->get('/riwayat-perbaikan');

        // Assert: Cek view, status, dan data yang tampil
        $response->assertStatus(200);
        $response->assertViewIs('sarpras.riwayatPerbaikan');
        $response->assertViewHas('RepairReports', function ($reports) use ($completedReport, $inProgressReport) {
            return $reports->contains($completedReport) &&
                !$reports->contains($inProgressReport);
        });
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function data_gedung_view_returns_building_data()
    {
        // Arrange: buat user sarpras dan data building
        $sarprasUser = User::factory()->create([
            'role' => 'sarpras',
        ]);

        $building1 = Building::factory()->create(['building_name' => 'Gedung A']);
        $building2 = Building::factory()->create(['building_name' => 'Gedung B']);

        // Act: login dan akses halaman
        $response = $this->actingAs($sarprasUser)
            ->get('/data-gedung');

        // Assert: view dan data benar
        $response->assertStatus(200);
        $response->assertViewIs('sarpras.dataGedung');
        $response->assertViewHas('facilities', function ($facilities) use ($building1, $building2) {
            return $facilities->contains($building1) && $facilities->contains($building2);
        });
    }

    // #[\PHPUnit\Framework\Attributes\Test]
    // public function data_fasilitas_gedung_view_returns_correct_data()
    // {
    //     // Arrange: Buat user sarpras
    //     $sarprasUser = User::factory()->create([
    //         'role' => 'sarpras',
    //     ]);

    //     // Buat 2 gedung
    //     $building1 = Building::factory()->create(['building_name' => 'Gedung A']);
    //     $building2 = Building::factory()->create(['building_name' => 'Gedung B']);

    //     // Buat fasilitas indoor & outdoor untuk kedua gedung
    //     $indoorFacility1 = BuildingFacility::factory()->create([
    //         'id_building' => $building1->id,
    //         'location' => 'indoor',
    //     ]);

    //     $outdoorFacility1 = BuildingFacility::factory()->create([
    //         'id_building' => $building2->id,
    //         'location' => 'outdoor',
    //     ]);

    //     // Act: Login & akses endpoint
    //     $response = $this->actingAs($sarprasUser)->get('/data-fasilitas-gedung');

    //     // Assert: View, status, dan data
    //     $response->assertStatus(200);
    //     $response->assertViewIs('sarpras.dataFasilitasGedung');
    //     $response->assertViewHasAll([
    //         'buildings',
    //         'indoorFacilities',
    //         'outdoorFacilities',
    //     ]);

    //     // Validasi data di dalam view
    //     $response->assertViewHas('buildings', function ($buildings) use ($building1, $building2) {
    //         return $buildings->contains($building1) && $buildings->contains($building2);
    //     });

    //     $response->assertViewHas('indoorFacilities', function ($facilities) use ($indoorFacility1) {
    //         return $facilities->contains($indoorFacility1);
    //     });

    //     $response->assertViewHas('outdoorFacilities', function ($facilities) use ($outdoorFacility1) {
    //         return $facilities->contains($outdoorFacility1);
    //     });
    // }

    public function test_sarpras_data_ruang_view_returns_correct_data(): void
    {
        // Buat user dan login sebagai sarpras
        $user = User::factory()->create([
            'role' => 'sarpras',
        ]);

        // Buat data dummy untuk building dan room
        $building = Building::factory()->create([
            'building_name' => 'Gedung A',
        ]);

        $room = Room::factory()->create([
            'room_name' => 'Ruang 101',
            'id_building' => $building->id,
        ]);

        // Akses halaman
        $response = $this->actingAs($user)->get('/sarpras-data-ruang');

        // Periksa apakah berhasil diakses dan menggunakan view yang benar
        $response->assertStatus(200);
        $response->assertViewIs('sarpras.sarprasDataRuang');

        // Periksa data yang dikirim ke view
        $response->assertViewHas('buildings', function ($buildings) use ($building) {
            return $buildings->contains($building);
        });

        $response->assertViewHas('rooms', function ($rooms) use ($room) {
            return $rooms->contains($room);
        });
    }

    public function test_data_fasilitas_ruang_view_returns_correct_data()
    {
        // Buat user dengan role sarpras
        $user = User::factory()->create([
            'role' => 'sarpras',
        ]);

        // Buat building dan room
        $building = Building::factory()->create(['building_name' => 'Gedung A']);
        $room = Room::factory()->create([
            'room_name' => 'Ruang 101',
            'id_building' => $building->id
        ]);

        // Buat fasilitas ruangan
        $facility = RoomFacility::factory()->create([
            'facility_name' => 'AC',
            'id_room' => $room->id
        ]);

        // Akses halaman sebagai user sarpras
        $response = $this->actingAs($user)->get('/data-fasilitas-ruang');

        // Pastikan status OK dan view benar
        $response->assertStatus(200);
        $response->assertViewIs('sarpras.dataFasilitasRuang');

        // Periksa data dikirim ke view
        $response->assertViewHas('rooms', function ($rooms) use ($room) {
            return $rooms->contains('id', $room->id);
        });

        $response->assertViewHas('facilities', function ($facilities) use ($facility) {
            return $facilities->contains('id', $facility->id);
        });
    }

    public function test_sarpras_data_teknisi_view_returns_correct_data()
    {
        // Buat user dengan role sarpras (pastikan di app ada role dan middleware 'auth.sarpras')
        $user = User::factory()->create([
            // misal ada kolom role, sesuaikan dengan app kamu
            'role' => 'sarpras',
        ]);

        // Buat beberapa data teknisi di database
        $technicians = Technician::factory()->count(3)->create();

        // Acting as user yang sudah login dan memiliki role sarpras
        $response = $this->actingAs($user)
            ->get('/sarpras-data-teknisi');

        // Assert status 200 OK
        $response->assertStatus(200);

        // Assert view yang dipakai adalah 'sarpras.sarprasDataTeknisi'
        $response->assertViewIs('sarpras.sarprasDataTeknisi');

        // Assert view menerima data 'technicians' dan data teknisinya benar
        $response->assertViewHas('technicians', function ($viewTechnicians) use ($technicians) {
            return $viewTechnicians->count() === $technicians->count()
                && $viewTechnicians->pluck('id')->sort()->values()
                ->all() === $technicians->pluck('id')->sort()->values()->all();
        });
    }

    // public function test_get_laporan_by_facility_returns_laporan_for_building_or_room_facility()
    // {
    //     // Setup: Buat user sarpras
    //     $user = User::factory()->create(['role' => 'sarpras']);
    //     $this->actingAs($user);

    //     // Buat data dummy
    //     $buildingFacility = BuildingFacility::factory()->create();
    //     $roomFacility = RoomFacility::factory()->create();

    //     $repair1 = RepairReport::factory()->create([
    //         'id_facility_building' => $buildingFacility->id,
    //         'id_facility_room' => $roomFacility->id,
    //     ]);

    //     $repair2 = RepairReport::factory()->create([
    //         'id_facility_building' => $buildingFacility->id,
    //         'id_facility_room' => $roomFacility->id,
    //     ]);

    //     // Tambahkan relasi terkait (bisa lewat factory atau manual)
    //     $repair1->user()->associate(User::factory()->create())->save();
    //     $repair1->technician()->associate(User::factory()->create())->save();
    //     $repair1->histories()->create(['status' => 'diproses']);
    //     $repair1->photos()->create(['file_path' => 'path/to/photo1.jpg']);

    //     $repair2->user()->associate(User::factory()->create())->save();
    //     $repair2->technician()->associate(User::factory()->create())->save();
    //     $repair2->histories()->create(['status' => 'ditolak']);
    //     $repair2->photos()->create(['file_path' => 'path/to/photo2.jpg']);

    //     // Login sebagai user sarpras
    //     $this->actingAs($user);

    //     // Test untuk building_facility_id
    //     $response1 = $this->getJson("get-laporan-facility/{$buildingFacility->id}");
    //     $response1->assertStatus(200);
    //     $response1->assertJsonFragment(['id_facility_building' => $buildingFacility->id]);

    //     // Test untuk room_facility_id
    //     $response2 = $this->getJson("get-laporan-facility/{$roomFacility->id}");
    //     $response2->assertStatus(200);
    //     $response2->assertJsonFragment(['id_facility_room' => $roomFacility->id]);
    // }
}
