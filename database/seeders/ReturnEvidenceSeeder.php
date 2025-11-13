<?php

namespace Database\Seeders;

use App\Models\ReturnEvidence;
use Illuminate_Database_Console_Seeds_WithoutModelEvents;
use Illuminate_Database_Seeder;

class ReturnEvidenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ReturnEvidence::factory()->count(10)->create();
    }
}
