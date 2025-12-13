<?php

namespace Database\Seeders;

use App\Models\Outlet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OutletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $listOutlet = [
            [
                'name' => 'Setia Budi',
                'location' => 'Jl. Setia Budi, Tj. Sari',
                'status' => 'AKTIF',
            ],
            [
                'name' => 'Pintu Jebol USU',
                'location' => 'Jl. Abdul Hakim No. 8-4, PB Selayang I, Kec. Medan Selayang',
                'status' => 'AKTIF',
            ],
            [
                'name' => 'Sumber USU',
                'location' => 'Jl. USU, Padang Bulan, Kec. Medan Baru',
                'status' => 'AKTIF',
            ],
            [
                'name' => 'Sembada',
                'location' => 'Jl. Sembada No. 54-52, PB Bulan Selayang II, Kec. Medan Selayang',
                'status' => 'AKTIF',
            ],
            [
                'name' => 'JCity Johor',
                'location' => 'Jl. Karya Bakti No. 43b, RW.00, Pangkalan Masyhur, Kec. Medan Johor',
                'status' => 'AKTIF',
            ],
            [
                'name' => 'STM (Kanal)',
                'location' => 'Jl. STM No. 156-149, Suka Maju, Kec. Medan Johor',
                'status' => 'AKTIF',
            ],
            [
                'name' => 'Gaperta',
                'location' => 'Tj. Gusta, Kec. Medan Helvetia',
                'status' => 'AKTIF',
            ],
            [
                'name' => 'Ayahanda (UNPRI)',
                'location' => 'Jl. Ayahanda No. 49d, Sei Putih Bar., Kec. Medan Petisah',
                'status' => 'AKTIF',
            ],
            [
                'name' => 'UNM Al Washliyah',
                'location' => 'Jl. Tidak Tahu',
                'status' => 'AKTIF',
            ],
            [
                'name' => 'UIN Tuntungan',
                'location' => 'Kp. Tengah, Kec. Pancur Batu',
                'status' => 'AKTIF',
            ]
        ];

        // Outlet::factory()->count(10)->create();
        foreach ($listOutlet as $outlet) {
            Outlet::create($outlet);
        }
    }
}
