<?php

namespace Database\Seeders;

use App\Models\DeliveryConfirmation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeliveryConfirmationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DeliveryConfirmation::factory()->count(10)->create();
    }
}
