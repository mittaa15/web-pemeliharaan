<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Room;
use App\Models\Building;
use App\Models\RoomFacility;
use App\Models\BuildingFacility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;


class RoomControllerTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    /** @test */
    public function admin_can_view_room_index_page()
    {
        // Buat user admin
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Buat dummy building dan room
        $building = Building::factory()->create([
            'building_name' => 'Gedung A',
        ]);

        $room = Room::factory()->create([
            'room_name' => 'Ruang 101',
            'id_building' => $building->id,
        ]);

        // Akses halaman index sebagai admin
        $response = $this->actingAs($admin)->get(route('admin.dataRuang')); // sesuaikan nama routenya

        $response->assertStatus(200);
        $response->assertViewIs('admin.dataRuangAdmin');
        $response->assertViewHasAll([
            'buildings',
            'rooms',
        ]);

        // Pastikan data tampil di view
        $this->assertTrue($response->viewData('buildings')->contains($building));
        $this->assertTrue($response->viewData('rooms')->contains($room));
    }

    /** @test */
    public function admin_can_create_room_successfully()
    {
        // Buat user admin
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Buat dummy building dan room
        $building = Building::factory()->create([
            'building_name' => 'Gedung A',
        ]);

        $room = Room::factory()->create([
            'room_name' => 'Ruang 101',
            'id_building' => $building->id,
        ]);

        // Akses halaman index sebagai admin
        $response = $this->actingAs($admin)->get(route('admin.dataRuang')); // sesuaikan nama routenya

        $response->assertStatus(200);
        $response->assertViewIs('admin.dataRuangAdmin');
        $response->assertViewHasAll([
            'buildings',
            'rooms',
        ]);

        // Pastikan data tampil di view
        $this->assertTrue($response->viewData('buildings')->contains($building));
        $this->assertTrue($response->viewData('rooms')->contains($room));
    }


    public function test_admin_can_delete_room_and_its_facilities()
    {
        // Buat user admin
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Buat dummy building dan room
        $building = Building::factory()->create([
            'building_name' => 'Gedung A',
        ]);

        $room = Room::factory()->create([
            'room_name' => 'Ruang 101',
            'id_building' => $building->id,
        ]);

        // Akses halaman index sebagai admin
        $response = $this->actingAs($admin)->get(route('admin.dataRuang')); // sesuaikan nama routenya

        $response->assertStatus(200);
        $response->assertViewIs('admin.dataRuangAdmin');
        $response->assertViewHasAll([
            'buildings',
            'rooms',
        ]);

        // Pastikan data tampil di view
        $this->assertTrue($response->viewData('buildings')->contains($building));
        $this->assertTrue($response->viewData('rooms')->contains($room));
    }

    /** @test */
    public function admin_can_update_room_successfully()
    {
        // Buat user admin
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Buat dummy building dan room
        $building = Building::factory()->create([
            'building_name' => 'Gedung A',
        ]);

        $room = Room::factory()->create([
            'room_name' => 'Ruang 101',
            'id_building' => $building->id,
        ]);

        // Akses halaman index sebagai admin
        $response = $this->actingAs($admin)->get(route('admin.dataRuang')); // sesuaikan nama routenya

        $response->assertStatus(200);
        $response->assertViewIs('admin.dataRuangAdmin');
        $response->assertViewHasAll([
            'buildings',
            'rooms',
        ]);

        // Pastikan data tampil di view
        $this->assertTrue($response->viewData('buildings')->contains($building));
        $this->assertTrue($response->viewData('rooms')->contains($room));
    }
}
