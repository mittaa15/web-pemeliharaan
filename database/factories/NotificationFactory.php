<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition()
    {
        return [
            'id_user' => User::factory(), // buat user baru otomatis
            'isRead' => $this->faker->boolean(20), // 20% kemungkinan sudah dibaca
            'title' => $this->faker->sentence(6, true),
            'description' => $this->faker->paragraph(2, true),
        ];
    }
}