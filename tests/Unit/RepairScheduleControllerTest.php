<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\RepairReport;
use App\Models\Technician;
use App\Models\User;
use App\models\Building;
use App\Models\Room;
use App\Models\Complaint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;


class RepairScheduleControllerTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    public function test_create_repair_schedule_success()
    {
        Notification::fake();

        // Buat data user dan laporan perbaikan
        $user = User::factory()->create();
        $repairReport = RepairReport::factory()->create(['status' => 'Pending']);

        $data = [
            'id_report' => $repairReport->id,
            'id_user' => $user->id,
            'repair_date' => now()->addDays(3)->toDateString(),
        ];

        // Disable middleware supaya tidak kena CSRF token (opsional)
        $response = $this->withoutMiddleware()->post(route('update-schedule'), $data);

        // Pastikan redirect dan session success ada
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Jadwal perbaikan berhasil disimpan dan status diperbarui.');

        // Pastikan data RepairSchedule tersimpan
        $this->assertDatabaseHas('repair_schedule', [
            'id_report' => $repairReport->id,
            'repair_date' => $data['repair_date'],
        ]);

        // Pastikan status laporan perbaikan berubah menjadi "Dijadwalkan"
        $this->assertDatabaseHas('repair_report', [
            'id' => $repairReport->id,
            'status' => 'Dijadwalkan',
        ]);

        // Pastikan data RepairHistory tersimpan dengan status "Dijadwalkan"
        $this->assertDatabaseHas('repair_history', [
            'id_report' => $repairReport->id,
            'status' => 'Dijadwalkan',
        ]);
    }

    public function test_create_repair_schedule_validation_fails()
    {
        // Kirim request tanpa data yang lengkap, harus gagal validasi
        $response = $this->post(route('update-status-sarpras'), []);

        $response->assertSessionHasErrors(['id_report', 'id_user', 'status']);
    }

    public function test_store_repair_schedule_success()
    {
        Notification::fake();

        // Buat data user dan laporan perbaikan
        $user = User::factory()->create();
        $repairReport = RepairReport::factory()->create(['status' => 'Pending']);

        $data = [
            'id_report' => $repairReport->id,
            'id_user' => $user->id,
            'repair_date' => now()->addDays(3)->toDateString(),
        ];

        // Disable middleware supaya tidak kena CSRF token (opsional)
        $response = $this->withoutMiddleware()->post(route('update-schedule'), $data);

        // Pastikan redirect dan session success ada
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Jadwal perbaikan berhasil disimpan dan status diperbarui.');

        // Pastikan data RepairSchedule tersimpan
        $this->assertDatabaseHas('repair_schedule', [
            'id_report' => $repairReport->id,
            'repair_date' => $data['repair_date'],
        ]);

        // Pastikan status laporan perbaikan berubah menjadi "Dijadwalkan"
        $this->assertDatabaseHas('repair_report', [
            'id' => $repairReport->id,
            'status' => 'Dijadwalkan',
        ]);

        // Pastikan data RepairHistory tersimpan dengan status "Dijadwalkan"
        $this->assertDatabaseHas('repair_history', [
            'id_report' => $repairReport->id,
            'status' => 'Dijadwalkan',
        ]);
    }

    public function test_store_repair_schedule_validation_fails()
    {
        // Request tanpa data harus gagal validasi
        $response = $this->withoutMiddleware()->post(route('update-schedule'), []);

        // Pastikan error validasi untuk id_report, id_user, dan repair_date
        $response->assertSessionHasErrors(['id_report', 'id_user', 'repair_date']);
    }

    public function test_upload_perbaikan_success()
    {
        // Buat data teknisi awal
        $technician = Technician::factory()->create([
            'name' => 'Teknisi Lama',
            'email' => 'lama@example.com',
            'phone_number' => '08123456789',
        ]);

        // Data update valid
        $data = [
            'name' => 'Teknisi Baru',
            'email' => 'baru@example.com',
            'phone_number' => '08987654321',
        ];

        // Kirim request PATCH ke route update-technician
        $response = $this->patch(route('update-technician', ['id' => $technician->id]), $data);

        // Pastikan redirect balik dengan session success
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Data teknisi berhasil diperbarui.');

        // Pastikan data teknisi berubah di database
        $this->assertDatabaseHas('technicians', [
            'id' => $technician->id,
            'name' => 'Teknisi Baru',
            'email' => 'baru@example.com',
            'phone_number' => '08987654321',
        ]);
    }

    public function test_upload_perbaikan_validation_fails()
    {
        $response = $this->post(route('upload-perbaikan-sarpras'), []);

        $response->assertSessionHasErrors([
            'id_report',
            'repair_photo',
            'repair_description',
        ]);
    }
    public function create_technician_success()
    {
        $data = [
            'name' => 'Teknisi Sukses',
            'email' => 'teknisi@example.com',
            'phone_number' => '081234567890',
        ];

        // Kirim request POST ke route yang memanggil createTechnician
        // Ganti 'technician.store' dengan route yang sesuai
        $response = $this->post(route('technician.store'), $data);

        // Pastikan redirect kembali dengan session success
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Teknisi berhasil ditambahkan.');

        // Pastikan data tersimpan di database
        $this->assertDatabaseHas('technicians', [
            'email' => 'teknisi@example.com',
            'name' => 'Teknisi Sukses',
            'phone_number' => '081234567890',
        ]);
    }

    /** @test */
    public function create_technician_validation_fails()
    {
        // Kirim data kosong yang harus gagal validasi
        $response = $this->post(route('create-technician'), []);

        // Pastikan session memiliki error validasi
        $response->assertSessionHasErrors(['name', 'email', 'phone_number']);
    }

    /** @test */
    public function create_technician_email_must_be_unique()
    {
        // Buat technician dengan email tertentu
        Technician::factory()->create(['email' => 'email@unique.com']);

        // Kirim data dengan email yang sama
        $data = [
            'name' => 'Teknisi Baru',
            'email' => 'email@unique.com',
            'phone_number' => '081234567891',
        ];

        $response = $this->post(route('create-technician'), $data);

        // Pastikan validasi gagal karena email sudah dipakai
        $response->assertSessionHasErrors(['email']);
    }

    public function test_update_technician_success()
    {
        // Buat data teknisi awal
        $technician = Technician::factory()->create([
            'name' => 'Teknisi Lama',
            'email' => 'lama@example.com',
            'phone_number' => '08123456789',
        ]);

        // Data update valid
        $data = [
            'name' => 'Teknisi Baru',
            'email' => 'baru@example.com',
            'phone_number' => '08987654321',
        ];

        // Kirim request PATCH ke route update-technician
        $response = $this->patch(route('update-technician', ['id' => $technician->id]), $data);

        // Pastikan redirect balik dengan session success
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Data teknisi berhasil diperbarui.');

        // Pastikan data teknisi berubah di database
        $this->assertDatabaseHas('technicians', [
            'id' => $technician->id,
            'name' => 'Teknisi Baru',
            'email' => 'baru@example.com',
            'phone_number' => '08987654321',
        ]);
    }

    public function test_update_technician_email_must_be_unique()
    {
        // Buat dua teknisi
        $technician1 = Technician::factory()->create([
            'email' => 'unique1@example.com',
        ]);
        $technician2 = Technician::factory()->create([
            'email' => 'unique2@example.com',
        ]);

        // Coba update teknisi2 dengan email yang sudah dipakai teknisi1
        $data = [
            'name' => 'Teknisi Dua',
            'email' => 'unique1@example.com', // email sudah dipakai
            'phone_number' => '08123456789',
        ];

        $response = $this->patch(route('update-technician', ['id' => $technician2->id]), $data);

        // Pastikan validasi gagal untuk field email
        $response->assertSessionHasErrors('email');
    }

    public function test_destroy_technician_success()
    {
        // Buat data teknisi dummy
        $technician = Technician::factory()->create();

        // Kirim request DELETE ke route delete-data-teknisi
        $response = $this->delete(route('delete-data-teknisi', ['id' => $technician->id]));

        // Pastikan redirect dan pesan session sesuai
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Teknisi berhasil dihapus.');

        // Pastikan data teknisi sudah tidak ada di database
        $this->assertDatabaseMissing('technicians', [
            'id' => $technician->id,
        ]);
    }

    public function test_destroy_technician_not_found()
    {
        // Kirim request DELETE dengan ID yang tidak ada
        $response = $this->delete(route('delete-data-teknisi', ['id' => 999]));

        // Pastikan response 404
        $response->assertNotFound();
    }

    public function test_sarpras_daftar_keluhan_view_success()
    {
        $this->withoutExceptionHandling();

        // Buat user dengan role sarpras dan login sebagai user tersebut
        $user = User::factory()->create([
            'role' => 'sarpras',
        ]);
        $this->actingAs($user);

        // Buat building dan room
        $building = Building::factory()->create();
        $room = Room::factory()->create(['id_building' => $building->id]);

        // Buat repair report dan complaint
        $report = RepairReport::factory()->create([
            'id_user' => $user->id,
            'id_building' => $building->id,
            'id_room' => $room->id,
            'status' => 'Diproses',
        ]);

        $complaint = Complaint::factory()->create([
            'id_user' => $user->id,
            'id_report' => $report->id,
        ]);

        // Kirim request GET ke route
        $response = $this->get('/sarpras-daftar-keluhan');

        // Pastikan respons OK
        $response->assertStatus(200);

        // Pastikan menggunakan view yang benar
        $response->assertViewIs('sarpras.sarprasDaftarKeluhan');

        // Pastikan data Complaints tersedia di view
        $response->assertViewHas('Complaints', function ($complaints) use ($complaint) {
            return $complaints->contains($complaint);
        });
    }
}
