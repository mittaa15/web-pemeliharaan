<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\User;
use App\Models\Complaint;
use App\Models\RepairReport;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ComplaintControllerTest extends TestCase
{

    use RefreshDatabase;

    public function test_create_complaint_success()
    {
        $this->withoutMiddleware();
        $user = User::factory()->create();
        $repairReport = RepairReport::factory()->create();

        $data = [
            'report_id' => $repairReport->id,
            'keluhan' => 'Keluhan ini valid dan cukup panjang',
        ];

        $response = $this->actingAs($user)->post(route('create-keluhan'), $data);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Keluhan berhasil dikirim.');

        $this->assertDatabaseHas('complaint', [
            'id_report' => $repairReport->id,
            'id_user' => $user->id,
            'complaint_description' => $data['keluhan'],
        ]);
    }

    public function test_create_complaint_validation_error()
    {
        $this->withoutMiddleware();

        $user = User::factory()->create();

        $data = [
            'report_id' => 999999, // ID laporan yang tidak ada
            'keluhan' => 'abc',    // Terlalu pendek
        ];

        $response = $this->actingAs($user)->post(route('create-keluhan'), $data);

        $response->assertSessionHasErrors(['report_id', 'keluhan']);
    }

    public function test_create_complaint_requires_authentication()
    {
        $this->withoutMiddleware();
        $user = User::factory()->create();
        $repairReport = RepairReport::factory()->create();

        $data = [
            'report_id' => $repairReport->id,
            'keluhan' => 'Keluhan ini valid dan cukup panjang',
        ];

        $response = $this->actingAs($user)->post(route('create-keluhan'), $data);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Keluhan berhasil dikirim.');

        $this->assertDatabaseHas('complaint', [
            'id_report' => $repairReport->id,
            'id_user' => $user->id,
            'complaint_description' => $data['keluhan'],
        ]);
    }
}
