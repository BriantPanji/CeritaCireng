<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $listUser = [
            [
                'display_name' => 'Sudut Corp',
                'username' => 'sudut',
                'phone' => '999999999999',
                'role_id' => 1,
                'outlet_id' => null,
                'status' => 'AKTIF',
                'password' => bcrypt('pass123'),
            ],
            [
                'display_name' => 'Admin',
                'username' => 'admin',
                'phone' => '999999999999',
                'role_id' => 2,
                'outlet_id' => null,
                'status' => 'AKTIF',
                'password' => bcrypt('cireng@lembut@123'),
            ],
            [
                'display_name' => 'Inventaris 1',
                'username' => 'inven1',
                'phone' => '081234567890',
                'role_id' => 3,
                'outlet_id' => null,
                'status' => 'AKTIF',
                'password' => bcrypt('inven1$cireng$321'),
            ],
            [
                'display_name' => 'Inventaris 2',
                'username' => 'inven2',
                'phone' => '081234567890',
                'role_id' => 3,
                'outlet_id' => null,
                'status' => 'AKTIF',
                'password' => bcrypt('inven2$cireng$321'),
            ],
            [
                'display_name' => 'Inventaris 3',
                'username' => 'inven3',
                'phone' => '081234567890',
                'role_id' => 3,
                'outlet_id' => null,
                'status' => 'AKTIF',
                'password' => bcrypt('inven3$cireng$321'),
            ],
            [
                'display_name' => 'Kurir 1',
                'username' => 'kurir1',
                'phone' => '081234567890',
                'role_id' => 4,
                'outlet_id' => null,
                'status' => 'AKTIF',
                'password' => bcrypt('kurir1%cireng%999'),
            ],
            [
                'display_name' => 'Kurir 2',
                'username' => 'kurir2',
                'phone' => '081234567890',
                'role_id' => 4,
                'outlet_id' => null,
                'status' => 'AKTIF',
                'password' => bcrypt('kurir2%cireng%999'),
            ],
            [
                'display_name' => 'Staff Setia Budi',
                'username' => 'setbud',
                'phone' => '081234567890',
                'role_id' => 5,
                'outlet_id' => 1,
                'status' => 'AKTIF',
                'password' => bcrypt('setbud#cireng123'),
            ],
            [
                'display_name' => 'Staff Pintu Jebol USU',
                'username' => 'usujebol',
                'phone' => '081234567890',
                'role_id' => 5,
                'outlet_id' => 2,
                'status' => 'AKTIF',
                'password' => bcrypt('cireng$jebol'),
            ],
            [
                'display_name' => 'Staff Sumber USU',
                'username' => 'sumber',
                'phone' => '081234567890',
                'role_id' => 5,
                'outlet_id' => 3,
                'status' => 'AKTIF',
                'password' => bcrypt('sumber@cerita123'),
            ],
            [
                'display_name' => 'Staff Sembada',
                'username' => 'sembada',
                'phone' => '081234567890',
                'role_id' => 5,
                'outlet_id' => 4,
                'status' => 'AKTIF',
                'password' => bcrypt('cerita$sembada123'),
            ],
            [
                'display_name' => 'Staff JCity Johor',
                'username' => 'jcity',
                'phone' => '081234567890',
                'role_id' => 5,
                'outlet_id' => 5,
                'status' => 'AKTIF',
                'password' => bcrypt('johor&city456'),
            ],
            [
                'display_name' => 'Staff STM (Kanal)',
                'username' => 'kanal',
                'phone' => '081234567890',
                'role_id' => 5,
                'outlet_id' => 6,
                'status' => 'AKTIF',
                'password' => bcrypt('kanal@cireng123'),
            ],
            [
                'display_name' => 'Staff Gaperta',
                'username' => 'gaperta',
                'phone' => '081234567890',
                'role_id' => 5,
                'outlet_id' => 7,
                'status' => 'AKTIF',
                'password' => bcrypt('gaperta#123'),
            ],
            [
                'display_name' => 'Staff Ayahanda (UNPRI)',
                'username' => 'unpri',
                'phone' => '081234567890',
                'role_id' => 5,
                'outlet_id' => 8,
                'status' => 'AKTIF',
                'password' => bcrypt('unpri@cireng456'),
            ],
            [
                'display_name' => 'Staff UNM Al Washliyah',
                'username' => 'washliyah',
                'phone' => '081234567890',
                'role_id' => 5,
                'outlet_id' => 9,
                'status' => 'AKTIF',
                'password' => bcrypt('washliyah#999'),
            ],
            [
                'display_name' => 'Staff UIN Tuntungan',
                'username' => 'uin',
                'phone' => '081234567890',
                'role_id' => 5,
                'outlet_id' => 10,
                'status' => 'AKTIF',
                'password' => bcrypt('uin@cireng456'),
            ],
        ];

        foreach ($listUser as $user) {
            User::create($user);
        }
    }
}
