<?php

namespace Database\Seeders;

use App\Models\DeliveryMistake;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeliveryMistakeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DeliveryMistake::factory()->count(10)->create();
    }
}
