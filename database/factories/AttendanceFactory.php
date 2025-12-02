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
        $attendanceDate = fake()->dateTimeBetween('-1 month', 'now');

        return [
            'status' => fake()->randomElement(['HADIR', 'IZIN', 'SAKIT', 'ABSEN']),
            'attendance_date' => $attendanceDate,
            'attendance_time' => fake()->dateTimeBetween(
                $attendanceDate->format('Y-m-d') . ' 07:00',
                $attendanceDate->format('Y-m-d') . ' 10:00'
            )
        ];
    }
}
