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
        // Buat user admin
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Buat room untuk relasi id_room
        $room = Room::factory()->create();

        // Data input valid
        $data = [
            'id_room' => $room->id,
            'facility_name' => 'AC Split',
            'number_units' => 5,
            'description' => 'Pendingin ruangan di ruang kelas',
        ];

        // Akses route create sebagai admin login
        $response = $this->actingAs($admin)
            ->post('/create-room-facility', $data);

        // Pastikan redirect ke route yang benar
        $response->assertRedirect('/admin-data-fasilitas-ruang');

        // Pastikan session ada flash message success
        $response->assertSessionHas('success', 'Ruangan berhasil ditambahkan.');

        // Pastikan data tersimpan di database
        $this->assertDatabaseHas('room_facility', [
            'id_room' => $room->id,
            'facility_name' => 'AC Split',
            'number_units' => 5,
            'description' => 'Pendingin ruangan di ruang kelas',
        ]);
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

    // public function test_update_room_facility_success()
    // {
    //     // Buat user dengan role admin
    //     $admin = User::factory()->create([
    //         'role' => 'admin',
    //     ]);

    //     // Buat fasilitas ruang awal
    //     $facility = RoomFacility::factory()->create([
    //         'facility_name' => 'AC Lama',
    //         'number_units' => 2,
    //         'description' => 'AC lama yang perlu diperbarui',
    //     ]);

    //     // Data update
    //     $dataUpdate = [
    //         'facility_name' => 'AC Baru',
    //         'number_units' => 3,
    //         'description' => 'AC baru yang lebih efisien',
    //     ];

    //     // Kirim request update
    //     $response = $this->actingAs($admin)
    //         ->patch(route('update_room_facility', ['roomFacility' => $facility->id]), $dataUpdate);

    //     // sesuaikan dengan route-mu

    //     // Cek redirect dan session flash
    //     $response->assertRedirect('/admin-data-fasilitas-ruang');
    //     $response->assertSessionHas('success', 'Fasilitas Gedung berhasil diperbarui.');

    //     // Cek database setelah update
    //     $this->assertDatabaseHas('room_facility', [
    //         'id' => $facility->id,
    //         'facility_name' => 'AC Baru',
    //         'number_units' => 3,
    //         'description' => 'AC baru yang lebih efisien',
    //     ]);
    // }



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

    // public function test_destroy_room_facility_success()
    // {
    //     // Buat admin
    //     $admin = User::factory()->create(['role' => 'admin']);

    //     // Buat fasilitas
    //     $roomFacility = RoomFacility::factory()->create();

    //     // Kirim request DELETE
    //     $response = $this->actingAs($admin)
    //         ->delete(route('delete_room_facility', $roomFacility->id));

    //     // Cek redirect dan flash message
    //     $response->assertRedirect();
    //     $response->assertSessionHas('success', 'Fasilitas ruangan berhasil dihapus.');

    //     // Cek data terhapus
    //     $this->assertDatabaseMissing('room_facility', [
    //         'id' => $roomFacility->id,
    //     ]);
    // }
}