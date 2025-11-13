<?php

namespace Database\Seeders;

use App\Models\OtherExpense;
use Illuminate_Database_Console_Seeds_WithoutModelEvents;
use Illuminate_Database_Seeder;

class OtherExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OtherExpense::factory()->count(10)->create();
    }
}
