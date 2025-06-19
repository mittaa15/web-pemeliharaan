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
        $admin = User::factory()->create(['role' => 'admin']);

        // Kirim data kosong (seharusnya gagal validasi)
        $response = $this->actingAs($admin)
            ->post(route('create_building_facility'), []);

        // Pastikan ada error validasi
        $response->assertSessionHasErrors(['id_building', 'facility_name', 'location']);
    }

    public function test_admin_can_create_building_facility_successfully()
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

    public function test_admin_can_delete_building_facility_successfully()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Kirim data kosong (seharusnya gagal validasi)
        $response = $this->actingAs($admin)
            ->post(route('create_building_facility'), []);

        // Pastikan ada error validasi
        $response->assertSessionHasErrors(['id_building', 'facility_name', 'location']);
    }
}
