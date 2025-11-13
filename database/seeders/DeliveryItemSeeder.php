<?php

namespace Database\Seeders;

use App\Models\DeliveryItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeliveryItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DeliveryItem::factory()->count(10)->create();
    }
}
