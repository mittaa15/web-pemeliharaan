<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\Building;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition()
    {
        return [
            'id_building' => Building::factory(), // relasi dengan building, gunakan factory Building
            'room_name' => $this->faker->word(),
            'room_type' => $this->faker->randomElement(['classroom', 'laboratory', 'office', 'meeting_room']),
            'capacity' => $this->faker->numberBetween(10, 100),
            'description' => $this->faker->sentence(),
        ];
    }
}