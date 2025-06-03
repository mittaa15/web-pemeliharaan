<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Building;
use App\Models\BuildingFacility;
use App\Models\RepairReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;


class BuildingFacilityControllerTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    public function test_index_displays_view_with_buildings_and_facilities()
    {
        // Buat user admin (anggap ada middleware auth + role)
        $admin = User::factory()->create(['role' => 'admin']);

        // Buat data bangunan
        $building = Building::factory()->create();

        // Buat fasilitas indoor dan outdoor untuk bangunan tersebut
        $indoorFacility = BuildingFacility::factory()->create([
            'id_building' => $building->id,
            'location' => 'indoor',
        ]);

        $outdoorFacility = BuildingFacility::factory()->create([
            'id_building' => $building->id,
            'location' => 'outdoor',
        ]);

        // Akses route index sebagai admin
        $response = $this->actingAs($admin)->get(route('fasilitas-gedung')); // sesuaikan route name kalau beda

        // Cek response berhasil (status 200)
        $response->assertStatus(200);

        // Cek view yang digunakan (jika kamu menggunakan view bernama admin.dataFasilitasGedungAdmin)
        $response->assertViewIs('admin.dataFasilitasGedungAdmin');

        // Cek data yang dikirim ke view ada dan benar
        $response->assertViewHas('buildings', function ($buildings) use ($building) {
            return $buildings->contains($building);
        });

        $response->assertViewHas('indoorFacilities', function ($indoorFacilities) use ($indoorFacility) {
            return $indoorFacilities->contains($indoorFacility);
        });

        $response->assertViewHas('outdoorFacilities', function ($outdoorFacilities) use ($outdoorFacility) {
            return $outdoorFacilities->contains($outdoorFacility);
        });
    }

    public function test_admin_can_create_building_facility_successfully()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $building = Building::factory()->create();

        $data = [
            'id_building' => $building->id,
            'facility_name' => 'AC Baru',
            'location' => 'indoor',
            'description' => 'AC yang sangat dingin',
        ];

        $response = $this->actingAs($admin)
            ->post(route('create_building_facility'), $data);

        $response->assertRedirect('/admin-data-fasilitas-gedung');
        $response->assertSessionHas('success', 'Data gedung berhasil ditambahkan.');

        $this->assertDatabaseHas('building_facility', [
            'id_building' => $building->id,
            'facility_name' => 'AC Baru',
            'location' => 'indoor',
            'description' => 'AC yang sangat dingin',
        ]);
    }


    public function test_create_building_facility_validation_error()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Kirim data kosong (seharusnya gagal validasi)
        $response = $this->actingAs($admin)
            ->post(route('create_building_facility'), []);

        // Pastikan ada error validasi
        $response->assertSessionHasErrors(['id_building', 'facility_name', 'location']);
    }

    public function test_show_building_facility_returns_correct_json()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        // Buat data building dan building facility dengan relasi repairReports
        $building = Building::factory()->create();
        $facility = BuildingFacility::factory()->create([
            'id_building' => $building->id,
        ]);
        // Misal juga buat repairReports terkait (jika ada factory-nya)
        $repairReport = RepairReport::factory()->create([
            'id_facility_building' => $facility->id,
        ]);

        // Panggil route show, misal route-nya: /building-facility/{buildingFacility}
        $response = $this->getJson(route('show_building_facility', $facility->id));

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'id' => $facility->id,
                    'id_building' => $building->id,
                    'facility_name' => $facility->facility_name,
                    // Bisa tambahkan field lain yang ingin dicek
                ],
            ])
            ->assertJsonStructure([
                'status',
                'data' => [
                    'id',
                    'id_building',
                    'facility_name',
                    'building',
                    'repair_reports',
                ]
            ]);
    }

    public function test_admin_can_update_building_facility_successfully()
    {

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // Buat data dummy building dan building facility
        $building = Building::factory()->create();
        $facility = BuildingFacility::factory()->create([
            'id_building' => $building->id,
            'facility_name' => 'Fasilitas Lama',
            'description' => 'Deskripsi lama',
        ]);

        // Data baru untuk update
        $updateData = [
            'facility_name' => 'Fasilitas Baru',
            'description' => 'Deskripsi diperbarui',
        ];

        // Panggil route update (gunakan PATCH atau PUT sesuai route kamu)
        $response = $this->patch(route('update_building_facility', $facility->id), $updateData);

        // Assert redirect ke halaman yang benar dan session success ada
        $response->assertRedirect('/admin-data-fasilitas-gedung');
        $response->assertSessionHas('success', 'Fasilitas Gedung berhasil diperbarui.');

        // $facilityFresh = $facility->fresh();
        // dd($facilityFresh->facility_name, $facilityFresh->description);

        // Assert data di database sudah berubah sesuai updateData
        $this->assertDatabaseHas('building_facility', [
            'id' => $facility->id,
            'facility_name' => 'Fasilitas Baru',
            'description' => 'Deskripsi diperbarui',
        ]);
    }

    // public function test_admin_can_delete_building_facility_successfully()
    // {
    //     $admin = User::factory()->create(['role' => 'admin']);
    //     $this->actingAs($admin);

    //     $facility = BuildingFacility::factory()->create();

    //     $response = $this->delete(route('delete_building_facility', $facility->id));

    //     $response->assertRedirect('/admin-data-fasilitas-gedung');
    //     $response->assertSessionHas('success', 'Fasilitas Gedung berhasil dihapus.');

    //     // Cek apakah data ada di DB sebelum assertDatabaseMissing
    //     $facilityCheck = BuildingFacility::find($facility->id);
    //     $this->assertNull($facilityCheck, 'Data fasilitas gedung tidak terhapus dari database.');

    //     $this->assertDatabaseMissing('building_facility', [
    //         'id' => $facility->id,
    //     ]);
    // }
}
