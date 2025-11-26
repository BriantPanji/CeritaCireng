<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Outlet;

class OutletClosedDaySeeder extends Seeder
{
    public function run(): void
    {
        // Contoh: Outlet ID 1 tutup di Selasa (2) dan Rabu (3)
        $outlet1 = Outlet::find(1);
        if ($outlet1) {
            $outlet1->closedDays()->attach([2, 3]);
        }

        // Contoh: Outlet ID 2 tutup di Minggu (7)
        $outlet2 = Outlet::find(2);
        if ($outlet2) {
            $outlet2->closedDays()->attach([7]);
        }
    }
}