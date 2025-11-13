<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_user' => User::inRandomOrder()->first()->id ?? User::factory()->create()->id,
            'status' => fake()->randomElement(['HADIR', 'IZIN', 'SAKIT', 'ALPHA']),
            'attendance_date' => fake()->date(),
            'attendance_time' => fake()->time()
        ];
    }
}
