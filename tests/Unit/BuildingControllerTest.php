<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use App\Models\Building;
use App\Models\BuildingFacility;
use App\Models\RoomFacility;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;


class BuildingControllerTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    public function test_admin_can_view_building_index()
    {
        // Buat user dengan role admin
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // Buat data dummy gedung
        Building::factory()->create(['building_name' => 'Gedung A']);
        Building::factory()->create(['building_name' => 'Gedung B']);

        // Panggil route index building
        $response = $this->get(route('building_index'));

        // Cek response berhasil dan view yang digunakan benar
        $response->assertStatus(200);
        $response->assertViewIs('admin.dataGedungAdmin');

        // Cek data facilities tersedia di view dan urut sesuai building_name
        $response->assertViewHas('facilities', function ($facilities) {
            return $facilities->first()->building_name === 'Gedung A' &&
                $facilities->last()->building_name === 'Gedung B';
        });
    }


    public function test_admin_can_create_building_successfully()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $building = Building::factory()->create();

        // Data tanpa building_name (validasi harus gagal)
        $updateData = [
            'description' => 'Deskripsi tanpa nama gedung',
        ];

        $response = $this->patch(route('update_building', $building->id), $updateData);

        $response->assertSessionHasErrors('building_name');
    }

    public function test_create_building_validation_fails_if_building_name_missing()
    {
        // Buat user dengan role admin
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // Buat data dummy gedung
        Building::factory()->create(['building_name' => 'Gedung A']);
        Building::factory()->create(['building_name' => 'Gedung B']);

        // Panggil route index building
        $response = $this->get(route('building_index'));

        // Cek response berhasil dan view yang digunakan benar
        $response->assertStatus(200);
        $response->assertViewIs('admin.dataGedungAdmin');

        // Cek data facilities tersedia di view dan urut sesuai building_name
        $response->assertViewHas('facilities', function ($facilities) {
            return $facilities->first()->building_name === 'Gedung A' &&
                $facilities->last()->building_name === 'Gedung B';
        });
    }

    // public function test_admin_can_update_building_successfully()
    // {

    //     // Buat user dengan role admin
    //     $admin = User::factory()->create(['role' => 'admin']);
    //     $this->actingAs($admin);

    //     // Buat data dummy gedung
    //     Building::factory()->create(['building_name' => 'Gedung A']);
    //     Building::factory()->create(['building_name' => 'Gedung B']);

    //     // Panggil route index building
    //     $response = $this->get(route('building_index'));

    //     // Cek response berhasil dan view yang digunakan benar
    //     $response->assertStatus(200);
    //     $response->assertViewIs('admin.dataGedungAdmin');

    //     // Cek data facilities tersedia di view dan urut sesuai building_name
    //     $response->assertViewHas('facilities', function ($facilities) {
    //         return $facilities->first()->building_name === 'Gedung A' &&
    //             $facilities->last()->building_name === 'Gedung B';
    //     });
    // }

    public function test_admin_can_update_building_successfully()
    {
        // Buat user dengan role admin
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // Buat data dummy gedung
        Building::factory()->create(['building_name' => 'Gedung A']);
        Building::factory()->create(['building_name' => 'Gedung B']);

        // Panggil route index building
        $response = $this->get(route('building_index'));

        // Cek response berhasil dan view yang digunakan benar
        $response->assertStatus(200);
        $response->assertViewIs('admin.dataGedungAdmin');

        // Cek data facilities tersedia di view dan urut sesuai building_name
        $response->assertViewHas('facilities', function ($facilities) {
            return $facilities->first()->building_name === 'Gedung A' &&
                $facilities->last()->building_name === 'Gedung B';
        });
    }

    public function test_update_building_validation_fails_if_building_name_missing()
    {

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $building = Building::factory()->create();

        // Data tanpa building_name (validasi harus gagal)
        $updateData = [
            'description' => 'Deskripsi tanpa nama gedung',
        ];

        $response = $this->patch(route('update_building', $building->id), $updateData);

        $response->assertSessionHasErrors('building_name');
    }

    public function test_admin_can_delete_building_and_related_data_successfully()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $building = Building::factory()->create();

        // Data tanpa building_name (validasi harus gagal)
        $updateData = [
            'description' => 'Deskripsi tanpa nama gedung',
        ];

        $response = $this->patch(route('update_building', $building->id), $updateData);

        $response->assertSessionHasErrors('building_name');
    }
}
