<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RoomFacility;
use App\Models\Room;
use App\Models\Building;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;


class RoomFacilityControllerTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    public function test_index_displays_room_facilities_and_rooms()
    {
        // Buat user dengan role admin supaya lolos middleware auth.admin
        $admin = User::factory()->create([
            'role' => 'admin', // pastikan role-nya 'admin' sesuai middleware
        ]);

        // Buat data Building, Room, dan RoomFacility
        $building = Building::factory()->create([
            'building_name' => 'Gedung A',
        ]);

        $room = Room::factory()->create([
            'room_name' => 'Ruang 101',
            'id_building' => $building->id,
        ]);

        $facility = RoomFacility::factory()->create([
            'id_room' => $room->id,
            // atribut lain sesuai factory
        ]);

        // Acting as admin supaya lolos middleware auth dan auth.admin
        $response = $this->actingAs($admin)
            ->get('/admin-data-fasilitas-ruang');

        $response->assertStatus(200);
        $response->assertViewIs('admin.dataFasilitasRuangAdmin');

        $response->assertViewHas('rooms', function ($rooms) use ($room) {
            return $rooms->contains('id', $room->id)
                && $rooms->firstWhere('id', $room->id)->room_name === 'Ruang 101';
        });

        $response->assertViewHas('facilities', function ($facilities) use ($facility) {
            return $facilities->contains('id', $facility->id)
                && $facilities->firstWhere('id', $facility->id)->id_room === $facility->id_room;
        });
    }

    public function test_create_room_facility_success()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $room = Room::factory()->create();
        $facility = RoomFacility::factory()->create([
            'id_room' => $room->id,
            'facility_name' => 'AC Lama',
            'number_units' => 2,
            'description' => 'AC lama',
        ]);

        // Kirim data kosong yang membuat validasi gagal
        $dataInvalid = [
            'facility_name' => '',  // required field kosong
            'number_units' => 'abc', // harus integer
            'description' => 123,    // harus string
        ];

        $response = $this->actingAs($admin)
            ->from("/admin-data-fasilitas-ruang") // halaman asal
            ->patch("/update-facility-room/{$facility->id}", $dataInvalid);

        // Pastikan redirect kembali ke halaman asal
        $response->assertRedirect('/admin-data-fasilitas-ruang');

        // Pastikan ada error di session untuk facility_name, number_units, description
        $response->assertSessionHasErrors(['facility_name', 'number_units', 'description']);
    }

    public function test_create_room_facility_validation_error()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Data invalid: facility_name kosong dan id_room tidak ada di db
        $data = [
            'id_room' => 9999, // id_room yang tidak ada
            'facility_name' => '', // kosong
        ];

        $response = $this->actingAs($admin)
            ->post('/create-room-facility', $data);

        // Pastikan redirect kembali ke form karena error
        $response->assertSessionHasErrors(['id_room', 'facility_name']);
    }

    public function test_update_room_facility_success()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $room = Room::factory()->create();
        $facility = RoomFacility::factory()->create([
            'id_room' => $room->id,
            'facility_name' => 'AC Lama',
            'number_units' => 2,
            'description' => 'AC lama',
        ]);

        // Kirim data kosong yang membuat validasi gagal
        $dataInvalid = [
            'facility_name' => '',  // required field kosong
            'number_units' => 'abc', // harus integer
            'description' => 123,    // harus string
        ];

        $response = $this->actingAs($admin)
            ->from("/admin-data-fasilitas-ruang") // halaman asal
            ->patch("/update-facility-room/{$facility->id}", $dataInvalid);

        // Pastikan redirect kembali ke halaman asal
        $response->assertRedirect('/admin-data-fasilitas-ruang');

        // Pastikan ada error di session untuk facility_name, number_units, description
        $response->assertSessionHasErrors(['facility_name', 'number_units', 'description']);
    }


    public function test_update_room_facility_validation_error()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $room = Room::factory()->create();
        $facility = RoomFacility::factory()->create([
            'id_room' => $room->id,
            'facility_name' => 'AC Lama',
            'number_units' => 2,
            'description' => 'AC lama',
        ]);

        // Kirim data kosong yang membuat validasi gagal
        $dataInvalid = [
            'facility_name' => '',  // required field kosong
            'number_units' => 'abc', // harus integer
            'description' => 123,    // harus string
        ];

        $response = $this->actingAs($admin)
            ->from("/admin-data-fasilitas-ruang") // halaman asal
            ->patch("/update-facility-room/{$facility->id}", $dataInvalid);

        // Pastikan redirect kembali ke halaman asal
        $response->assertRedirect('/admin-data-fasilitas-ruang');

        // Pastikan ada error di session untuk facility_name, number_units, description
        $response->assertSessionHasErrors(['facility_name', 'number_units', 'description']);
    }

    public function test_destroy_room_facility_success()
    {
        // Buat user dengan role admin supaya lolos middleware auth.admin
        $admin = User::factory()->create([
            'role' => 'admin', // pastikan role-nya 'admin' sesuai middleware
        ]);

        // Buat data Building, Room, dan RoomFacility
        $building = Building::factory()->create([
            'building_name' => 'Gedung A',
        ]);

        $room = Room::factory()->create([
            'room_name' => 'Ruang 101',
            'id_building' => $building->id,
        ]);

        $facility = RoomFacility::factory()->create([
            'id_room' => $room->id,
            // atribut lain sesuai factory
        ]);

        // Acting as admin supaya lolos middleware auth dan auth.admin
        $response = $this->actingAs($admin)
            ->get('/admin-data-fasilitas-ruang');

        $response->assertStatus(200);
        $response->assertViewIs('admin.dataFasilitasRuangAdmin');

        $response->assertViewHas('rooms', function ($rooms) use ($room) {
            return $rooms->contains('id', $room->id)
                && $rooms->firstWhere('id', $room->id)->room_name === 'Ruang 101';
        });

        $response->assertViewHas('facilities', function ($facilities) use ($facility) {
            return $facilities->contains('id', $facility->id)
                && $facilities->firstWhere('id', $facility->id)->id_room === $facility->id_room;
        });
    }
}
