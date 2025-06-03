<?php

namespace Database\Factories;

use App\Models\Complaint;
use App\Models\User;
use App\Models\RepairReport;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComplaintFactory extends Factory
{
    protected $model = Complaint::class;

    public function definition()
    {
        return [
            // id_report harus merujuk ke RepairReport yang valid, jadi kita buat atau ambil dulu
            'id_report' => RepairReport::factory(), // ini akan membuat RepairReport baru secara otomatis

            // id_user juga harus valid, bisa buat User baru juga otomatis
            'id_user' => User::factory(),

            'complaint_description' => $this->faker->sentence(10),
        ];
    }
}