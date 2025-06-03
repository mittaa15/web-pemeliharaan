<?php

namespace Database\Factories;

use App\Models\Building;
use Illuminate\Database\Eloquent\Factories\Factory;

class BuildingFactory extends Factory
{
    protected $model = Building::class;

    public function definition()
    {
        return [
            'building_name' => $this->faker->company(),
            'description' => $this->faker->sentence(),
        ];
    }
}
