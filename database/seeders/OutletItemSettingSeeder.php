<?php

namespace Database\Seeders;

use App\Models\OutletItemSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OutletItemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OutletItemSetting::factory()->count(10)->create();
    }
}
