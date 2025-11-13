<?php

namespace Database\Seeders;

use App\Models\Attendance;
use Illuminate_Database_Console_Seeds_WithoutModelEvents;
use Illuminate_Database_Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Attendance::factory()->count(10)->create();
    }
}
