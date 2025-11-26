<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DaySeeder extends Seeder
{
    public function run(): void
    {
        $days = [
            ['name' => 'Senin', 'day_number' => 1],
            ['name' => 'Selasa', 'day_number' => 2],
            ['name' => 'Rabu', 'day_number' => 3],
            ['name' => 'Kamis', 'day_number' => 4],
            ['name' => 'Jumat', 'day_number' => 5],
            ['name' => 'Sabtu', 'day_number' => 6],
            ['name' => 'Minggu', 'day_number' => 7],
        ];

        DB::table('days')->insert($days);
    }
}