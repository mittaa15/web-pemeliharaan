<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    // Tentukan model yang factory ini buat
    protected $model = \App\Models\User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'), // default password: password
            'role' => 'user',                      // default role user
            'isVerified' => $this->faker->boolean(80), // 80% chance sudah verified
            'verification_token' => Str::random(32),   // token random
        ];
    }

    // State untuk user yang sudah verified (optional)
    public function verified()
    {
        return $this->state(function (array $attributes) {
            return [
                'isVerified' => true,
                'verification_token' => null,
            ];
        });
    }

    // State untuk user yang belum verified (optional)
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'isVerified' => false,
                'verification_token' => Str::random(32),
            ];
        });
    }
}