<?php

namespace Database\Factories;

use App\Models\RepairTechnicians;
use App\Models\RepairReport;
use App\Models\Technician;
use Illuminate\Database\Eloquent\Factories\Factory;

class RepairTechniciansFactory extends Factory
{
    protected $model = RepairTechnicians::class;

    public function definition()
    {
        return [
            'id_report' => RepairReport::factory(),
            'id_technisian' => Technician::factory(),
            'description_work' => $this->faker->sentence(),
        ];
    }
}