<?php

namespace Database\Factories;

use App\Models\BuildingFacility;
use App\Models\Building;
use Illuminate\Database\Eloquent\Factories\Factory;

class BuildingFacilityFactory extends Factory
{
    protected $model = BuildingFacility::class;

    public function definition(): array
    {
        return [
            'id_building' => Building::factory(), // pastikan BuildingFactory ada
            'facility_name' => $this->faker->words(2, true),
            'location' => $this->faker->word(),
            'description' => $this->faker->sentence(),
        ];
    }
}