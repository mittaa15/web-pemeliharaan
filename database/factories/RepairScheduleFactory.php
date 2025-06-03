<?php

namespace Database\Factories;

use App\Models\RepairSchedule;
use App\Models\RepairReport;
use Illuminate\Database\Eloquent\Factories\Factory;

class RepairScheduleFactory extends Factory
{
    protected $model = RepairSchedule::class;

    public function definition()
    {
        return [
            'id_report' => RepairReport::factory(),
            'technician_name' => $this->faker->name(),
            'repair_date' => $this->faker->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d'),
        ];
    }
}