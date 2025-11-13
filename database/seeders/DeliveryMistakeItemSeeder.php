<?php

namespace Database\Seeders;

use App\Models\DeliveryMistakeItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeliveryMistakeItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DeliveryMistakeItem::factory()->count(10)->create();
    }
}
