<?php

namespace tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\RoomFacility;
use App\Models\Room;
use App\Models\Building;
use App\Models\BuildingFacility;
use App\Models\RepairReport;
use App\Models\Technician;
use App\Models\Complaint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class RepairReportControllerTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    /** @test */
    public function index_shows_form_with_room_facilities()
    {
        $user = User::factory()->create();
        RoomFacility::factory()->count(3)->create();

        $response = $this->actingAs($user)->get(route('repair-report.index'));

        $response->assertStatus(200);
        $response->assertViewHas('roomFacilitys');
    }

    /** @test */
    public function create_stores_repair_report_and_redirects()
    {
        $this->withoutExceptionHandling();
        $this->withoutMiddleware();

        Storage::fake('public'); // fake disk 'public' supaya file tidak benar-benar disimpan

        $user = User::factory()->create();
        $buildingFacility = BuildingFacility::factory()->create();
        $roomFacility = RoomFacility::factory()->create();

        // Buat satu file upload fake
        $file = UploadedFile::fake()->image('damage.jpg');

        $postData = [
            'id_user' => $user->id,
            'id_facility_building' => $buildingFacility->id,
            'id_facility_room' => $roomFacility->id,
            'location_type' => 'Indoor',
            'damage_description' => 'Kerusakan AC',
            'damage_impact' => 'Keselamatan pengguna',
            'room_name' => 'Ruang Pimpinan',
            'building_name' => 'Auditorium',
            'damage_photo' => $file,  // pakai file yang sama
            'action' => 'dashboard',
        ];

        $response = $this->actingAs($user)
            ->post(route('create-laporan'), $postData);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('success', 'Laporan berhasil ditambahkan.');

        $this->assertDatabaseHas('repair_report', [
            'id_user' => $user->id,
            'damage_description' => 'Kerusakan AC',
            'status' => 'Diproses',
            'damage_impact' => 'Keselamatan pengguna',
        ]);

        // Cek file benar-benar disimpan di folder 'kerusakan' pada disk 'public'
        Storage::disk('public')->assertExists('kerusakan/' . $file->hashName());
    }

    public function test_update_repair_report_validation_error()
    {
        $user = User::factory()->create(['role' => 'sarpras']);
        $this->actingAs($user);

        $report = RepairReport::factory()->create();

        // Kirim request tanpa damage_impact dan damage_description (invalid)
        $response = $this->put(route('laporan-update-sarpras', $report->id), [
            'damage_impact' => '',
            'damage_description' => '',
        ]);

        // Pastikan ada error validasi untuk damage_impact dan damage_description
        $response->assertSessionHasErrors(['damage_impact']);
    }


    public function test_destroy_report_deletes_all_related_data()
    {
        Storage::fake('local');

        // Buat user sarpras dan login
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        // Simpan file palsu untuk damage_photo
        $fakePhoto = UploadedFile::fake()->image('kerusakan.jpg');
        $photoPath = $fakePhoto->store('damage_photos');

        // Buat report dengan file foto
        $report = RepairReport::factory()->create([
            'damage_photo' => $photoPath,
        ]);

        // Buat teknisi dummy untuk relasi repairTechnicians
        $technician = Technician::factory()->create();

        // Tambahkan relasi: histories, technicians, complaints, schedules
        $report->histories()->create([
            'status' => 'diproses',
            'complete_date' => now(),  // pastikan ada nilai karena wajib
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $report->repairTechnicians()->create([
            'id_technisian' => $technician->id,  // pastikan kolom sesuai database
            'id_report' => $report->id,           // biasanya otomatis dari relasi, tapi bisa disertakan
            'created_at' => now(),
            'updated_at' => now(),
            'description_work' => 'Perbaikan sistem listrik',
        ]);
        $report->complaints()->create([
            'complaint_text' => 'Masih rusak',
            'id_user' => $user->id, // isi dengan user yang valid
            'complaint_description' => 'Masih ada kerusakan di bagian X',  // wajib diisi
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $report->schedules()->create([
            'schedule_date' => now()->addDay(),
            'repair_date' => now()->addDay(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Jalankan route destroy
        $response = $this->delete(route('delete-report', $report->id));

        // Assert redirect dan pesan sukses
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Laporan berhasil dihapus.');

        // Pastikan semua data terkait terhapus
        $this->assertDatabaseMissing('repair_report', ['id' => $report->id]);
        $this->assertDatabaseMissing('repair_history', ['id_report' => $report->id]);
        $this->assertDatabaseMissing('repair_technicians', ['id_report' => $report->id]);
        $this->assertDatabaseMissing('complaint', ['id_report' => $report->id]);
        $this->assertDatabaseMissing('repair_schedule', ['id_report' => $report->id]);

        // Pastikan file foto juga dihapus
        Storage::disk('local')->assertMissing($photoPath);
    }


    // public function test_update_admin_repair_report_success()
    // {
    //     // Buat user dan login
    //     $user = User::factory()->create(['role' => 'sarpras']);
    //     $this->actingAs($user);

    //     // Buat data room dan building
    //     $room = Room::factory()->create(['room_name' => 'Ruang Pimpinan']); // special room
    //     $building = Building::factory()->create(['building_name' => 'Gedung Biasa']);

    //     // Buat laporan awal
    //     $report = RepairReport::factory()->create([
    //         'id_room' => $room->id,
    //         'id_building' => $building->id,
    //         'damage_description' => 'Deskripsi lama',
    //         'damage_point' => 0,
    //     ]);

    //     // Kirim request update (pastikan route dan parameter sesuai dengan controller)
    //     $response = $this->followingRedirects()->put(route('laporan-update-sarpras', ['repairReport' => $report->id]), [
    //         'damage_impact' => 'Keselamatan pengguna',
    //     ]);

    //     $response->assertSee('Laporan berhasil diperbarui.');


    //     // Pastikan redirect dan session success
    //     $response->assertRedirect();
    //     $response->assertSessionHas('success', 'Laporan berhasil diperbarui.');

    //     // Refresh data dari database
    //     $report->refresh();

    //     // Cek bahwa damage_impact diperbarui
    //     $this->assertEquals('Keselamatan pengguna', $report->damage_impact);

    //     // Cek bahwa damage_point dihitung dengan benar (75 + 40 = 115)
    //     $this->assertEquals(115, $report->damage_point);

    //     // Pastikan damage_description tidak berubah
    // }

    public function test_update_repair_report_success()
    {
        // Buat user dan login
        $user = User::factory()->create(['role' => 'sarpras']);
        $this->actingAs($user);

        // Buat RepairReport dan relasi room dan building dummy (gunakan factory relasi jika ada)
        $room = Room::factory()->create(['room_name' => 'Ruang Pimpinan']); // special room
        $building = Building::factory()->create(['building_name' => 'Gedung Biasa']);
        $report = RepairReport::factory()->create([
            'id_room' => $room->id,
            'id_building' => $building->id,
            'damage_description' => 'Deskripsi lama',
            'damage_point' => 0,
        ]);

        // Kirim request update
        $response = $this->put(route('laporan.update', $report->id), [
            'damage_impact' => 'Keselamatan pengguna',
            'damage_description' => 'Kerusakan pada kabel listrik',
        ]);

        // Pastikan redirect dan session success
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Laporan berhasil diperbarui.');

        // Refresh data dari DB
        $report->refresh();

        // Cek data terupdate dengan benar
        $this->assertEquals('Kerusakan pada kabel listrik', $report->damage_description);

        // Karena room termasuk specialRooms, damagePoint = 75 + 40 = 115
        $this->assertEquals(115, $report->damage_point);
    }

    public function test_edit_cancels_report_and_creates_history()
    {
        $user = User::factory()->create(['role' => 'sarpras']);
        $this->actingAs($user);

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

        $report = RepairReport::factory()->create(['status' => 'Pending']);

        $response = $this->post(route('cancel-report'), [
            'id_report' => $report->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Laporan berhasil dibatalkan.');

        $report->refresh();
        $this->assertEquals('Dibatalkan', $report->status);

        $this->assertDatabaseHas('repair_history', [
            'id_report' => $report->id,
            'status' => 'Dibatalkan',
        ]);
    }


    public function test_edit_fails_with_invalid_id_report()
    {
        $user = User::factory()->create(['role' => 'sarpras']);
        $this->actingAs($user);

        // Request dengan id_report yang tidak ada
        $response = $this->followingRedirects()->post(route('cancel-report'), [
            'id_report' => 999999,
        ]);
        $response->assertSessionHasErrors('id_report');
    }
}
