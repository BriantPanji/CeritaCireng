<?php

namespace Database\Seeders;

use App\Models\ReturnModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReturnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ReturnModel::factory()->count(10)->create();
    }
}
