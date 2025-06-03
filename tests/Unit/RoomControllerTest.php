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

        // Buat building sebagai relasi
        $building = Building::factory()->create();

        // Data ruangan baru
        $data = [
            'id_building' => $building->id,
            'room_name' => 'Ruang 201',
            'room_type' => 'Laboratorium',
            'capacity' => 30,
            'description' => 'Ruangan laboratorium kimia',
        ];

        // Kirim request sebagai admin
        $response = $this->actingAs($admin)->post(route('create_room'), $data); // sesuaikan dengan route

        // Redirect ke halaman yang dituju
        $response->assertRedirect('/admin-data-ruang');
        $response->assertSessionHas('success', 'Ruangan berhasil ditambahkan.');

        // Pastikan data tersimpan di database
        $this->assertDatabaseHas('room', [
            'id_building' => $building->id,
            'room_name' => 'Ruang 201',
            'room_type' => 'Laboratorium',
            'capacity' => 30,
            'description' => 'Ruangan laboratorium kimia',
        ]);
    }


    public function test_admin_can_delete_room_and_its_facilities()
    {
        // Buat user sebagai admin
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Buat ruangan
        $room = Room::factory()->create();

        // Buat fasilitas yang terkait dengan ruangan
        $facility1 = RoomFacility::factory()->create(['id_room' => $room->id]);
        $facility2 = RoomFacility::factory()->create(['id_room' => $room->id]);

        // Akses endpoint destroy
        $response = $this->actingAs($admin)->delete(route('delete_room', ['room' => $room->id]));

        // Pastikan redirect dan session flash
        $response->assertRedirect('/admin-data-ruang');
        $response->assertSessionHas('success', 'Ruangan dan fasilitas terkait berhasil dihapus.');

        // Pastikan ruangan sudah terhapus
        $this->assertDatabaseMissing('room', [
            'id' => $room->id,
        ]);

        // Pastikan semua fasilitas juga terhapus
        $this->assertDatabaseMissing('room_facility', [
            'id' => $facility1->id,
        ]);
        $this->assertDatabaseMissing('room_facility', [
            'id' => $facility2->id,
        ]);
    }
}


    // /** @test */
    // public function admin_can_update_room_successfully()
    // {
    //     // Buat user admin
    //     $admin = User::factory()->create([
    //         'role' => 'admin',
    //     ]);

    //     // Buat building & room
    //     $building = Building::factory()->create();
    //     $room = Room::factory()->create([
    //         'id_building' => $building->id,
    //         'room_name' => 'Ruang Lama',
    //         'room_type' => 'Kelas',
    //         'capacity' => 20,
    //         'description' => 'Ruangan lama untuk kuliah',
    //     ]);

    //     // Data yang akan diupdate
    //     $updateData = [
    //         'room_name' => 'Ruang Baru',
    //         'room_type' => 'Laboratorium',
    //         'capacity' => 35,
    //         'description' => 'Ruangan sudah direnovasi',
    //     ];

    //     // Kirim request PATCH ke endpoint update-room
    //     $response = $this->actingAs($admin)
    //         ->patch(route('update_room', $room->id), $updateData);

    //     // Pastikan redirect dan session flash sesuai
    //     $response->assertRedirect('/admin-data-ruang');
    //     $response->assertSessionHas('success', 'Ruangan berhasil diperbarui.');

    //     // Verifikasi bahwa data telah diperbarui di database
    //     $this->assertDatabaseHas('room', [
    //         'id' => $room->id,
    //         'room_name' => 'Ruang Baru',
    //         'room_type' => 'Laboratorium',
    //         'capacity' => 35,
    //         'description' => 'Ruangan sudah direnovasi',
    //     ]);
    // }