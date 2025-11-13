<?php

namespace Database\Seeders;

use App\Models\DeliveryMistakeConfirmation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeliveryMistakeConfirmationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DeliveryMistakeConfirmation::factory()->count(10)->create();
    }
}
