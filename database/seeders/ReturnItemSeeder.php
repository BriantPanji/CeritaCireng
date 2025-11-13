<?php

namespace Database\Seeders;

use App\Models\ReturnItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReturnItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ReturnItem::factory()->count(10)->create();
    }
}
