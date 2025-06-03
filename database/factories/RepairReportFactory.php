<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\RepairReport;
use App\Models\BuildingFacility;
use App\Models\RoomFacility;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class RepairReportFactory extends Factory
{
    protected $model = RepairReport::class;
    public function definition()
    {
        return [
            'id_user' => User::factory(),
            'id_facility_building' => BuildingFacility::factory(), // ✅ Tambahkan ini
            'id_facility_room' => RoomFacility::factory(),  // <-- tambahkan ini
            'status' => 'Diproses',
            'location_type' => $this->faker->randomElement(['indoor', 'outdoor']),
            'damage_description' => $this->faker->sentence,
            'damage_photo' => 'photos/sample.jpg',
            'damage_impact' => $this->faker->randomElement(['Ringan', 'Sedang', 'Berat']),
            'damage_point' => $this->faker->numberBetween(1, 10),
        ];
    }
}
