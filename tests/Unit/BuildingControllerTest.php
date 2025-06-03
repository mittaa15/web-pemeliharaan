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
        // Data input valid
        $data = [
            'building_name' => 'Gedung Baru',
            'description' => 'Deskripsi gedung baru',
        ];

        // Panggil route POST create (sesuaikan nama route jika beda)
        $response = $this->post(route('create_building'), $data);

        // Cek redirect ke halaman yang benar dan session success ada
        $response->assertRedirect('/admin-data-gedung');
        $response->assertSessionHas('success', 'Data gedung berhasil ditambahkan.');

        // Cek data benar-benar tersimpan di database
        $this->assertDatabaseHas('building', [
            'building_name' => 'Gedung Baru',
            'description' => 'Deskripsi gedung baru',
        ]);
    }

    public function test_create_building_validation_fails_if_building_name_missing()
    {
        // Data tanpa building_name
        $data = [
            'description' => 'Deskripsi tanpa nama gedung',
        ];

        // Panggil route POST create
        $response = $this->post(route('create_building'), $data);

        // Pastikan validation error
        $response->assertSessionHasErrors('building_name');
    }

    // public function test_admin_can_update_building_successfully()
    // {

    //     // Buat user dengan role admin
    //     $admin = User::factory()->create(['role' => 'admin']);
    //     $this->actingAs($admin);

    //     // Buat data gedung awal
    //     $building = Building::factory()->create([
    //         'building_name' => 'Gedung Lama',
    //         'description' => 'Deskripsi lama',
    //     ]);

    //     // Data update yang valid
    //     $updateData = [
    //         'building_name' => 'Gedung Baru',
    //         'description' => 'Deskripsi baru',
    //     ];

    //     // Panggil route POST atau PUT untuk update (sesuaikan route jika berbeda)
    //     $response = $this->patch(route('update_building', $building->id), $updateData);

    //     // $building = $building->fresh();
    //     // dd($building);

    //     // Pastikan redirect ke halaman yang benar dan ada session success
    //     $response->assertRedirect('/admin-data-gedung');
    //     $response->assertSessionHas('success', 'Gedung berhasil diperbarui.');

    //     // Pastikan data di database sudah berubah sesuai update
    //     $this->assertDatabaseHas('building', [
    //         'id' => $building->id,
    //         'building_name' => 'Gedung Baru',
    //         'description' => 'Deskripsi baru',
    //     ]);
    // }

    // public function test_admin_can_update_building_successfully()
    // {
    //     // 1. Buat user admin
    //     $admin = User::factory()->create(['role' => 'admin']);
    //     $this->actingAs($admin);

    //     // 2. Buat data gedung awal
    //     $building = Building::factory()->create([
    //         'building_name' => 'Gedung Lama',
    //         'description' => 'Deskripsi lama',
    //     ]);

    //     // 3. Data update valid
    //     $updateData = [
    //         'building_name' => 'Gedung Baru',
    //         'description' => 'Deskripsi baru',
    //     ];

    //     // 4. Kirim request update
    //     $response = $this->patch(route('update_building', $building->id), $updateData);

    //     // 5. Cek response redirect dan session success
    //     $response->assertRedirect('/admin-data-gedung');
    //     $response->assertSessionHas('success', 'Gedung berhasil diperbarui.');

    //     // 6. Refresh dari database dan cek hasil akhir
    //     $updated = Building::find($building->id);
    //     $this->assertNotNull($updated, 'Data tidak ditemukan setelah update.');

    //     $this->assertEquals('Gedung Baru', $updated->building_name);
    //     $this->assertEquals('Deskripsi baru', $updated->description);

    //     // 7. Alternatif pakai assertDatabaseHas juga tetap bisa
    //     $this->assertDatabaseHas('building', [
    //         'id' => $building->id,
    //         'building_name' => 'Gedung Baru',
    //         'description' => 'Deskripsi baru',
    //     ]);
    // }

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
        // 1. Buat gedung
        $building = Building::factory()->create();

        // 2. Buat ruangan yang terhubung dengan gedung
        $room = Room::factory()->create([
            'id_building' => $building->id,
        ]);

        // 3. Buat fasilitas ruangan
        RoomFacility::factory()->create([
            'id_room' => $room->id,
        ]);

        // 4. Tambah entri ke tabel building_facility manual (jika tidak ada model)
        DB::table('building_facility')->insert([
            'id_building' => $building->id,
            'facility_name' => 'AC',
            'location' => 'Lantai 1',
            'description' => 'TESTING', // isi nilai sesuai struktur databasenya
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Kirim DELETE request ke route penghapusan
        $response = $this->delete(route('delete_building', $building->id));

        $response->dump();

        // 6. Cek redirect dan pesan sukses
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Gedung dan semua data terkait berhasil dihapus.');

        // 7. Cek semua data terhapus dari database
        $this->assertDatabaseMissing('building', [
            'id' => $building->id,
        ]);

        $this->assertDatabaseMissing('room', [
            'id' => $room->id,
        ]);

        $this->assertDatabaseMissing('room_facility', [
            'id_room' => $room->id,
        ]);

        $this->assertDatabaseMissing('building_facility', [
            'id_building' => $building->id,
        ]);
    }
}
