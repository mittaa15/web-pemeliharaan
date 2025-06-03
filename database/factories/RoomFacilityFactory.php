<?php

namespace Database\Factories;

use App\Models\RoomFacility;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFacilityFactory extends Factory
{
    protected $model = RoomFacility::class;

    public function definition()
    {
        return [
            'id_room' => Room::factory(), // membuat room baru sekaligus, pastikan Room punya factory
            'facility_name' => $this->faker->word(),
            'number_units' => $this->faker->numberBetween(1, 20),
            'description' => $this->faker->sentence(),
        ];
    }
}