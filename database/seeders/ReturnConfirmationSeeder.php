<?php

namespace Database\Seeders;

use App\Models\ReturnConfirmation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReturnConfirmationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ReturnConfirmation::factory()->count(10)->create();
    }
}
