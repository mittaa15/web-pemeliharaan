<?php

namespace Database\Factories;

use App\Models\RepairHistory;
use App\Models\RepairReport;
use Illuminate\Database\Eloquent\Factories\Factory;

class RepairHistoryFactory extends Factory
{
    protected $model = RepairHistory::class;

    public function definition()
    {
        $status = $this->faker->randomElement([
            'diproses',
            'ditolak',
            'dijadwalkan',
            'dalam proses pengerjaan',
            'pengecekan akhir',
            'selesai'
        ]);

        return [
            'id_report' => RepairReport::factory(),
            'status' => $status,
            'complete_date' => $status === 'selesai'
                ? $this->faker->dateTimeBetween('-1 month', 'now')
                : now(), // pastikan bukan null
            'repair_notes' => $this->faker->optional()->sentence(10, true),
            'damage_photo' => $this->faker->optional()->imageUrl(640, 480, 'technics', true),
        ];
    }
}