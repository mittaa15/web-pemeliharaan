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
            'Diproses',
            'Ditolak',
            'Dijadwalkan',
            'Dalam proses pengerjaan',
            'Pengecekan akhir',
            'Selesai'
        ]);

        return [
            'id_report' => RepairReport::factory(),
            'status' => $status,
            'complete_date' => $status === 'Selesai'
                ? $this->faker->dateTimeBetween('-1 month', 'now')
                : now(),
            'repair_notes' => $this->faker->sentence(10),
            'damage_photo' => $this->faker->imageUrl(640, 480, 'technics', true),
        ];
    }

    public function dalamProsesPengerjaan()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Dalam proses pengerjaan',
                'complete_date' => now(),
            ];
        });
    }
}
