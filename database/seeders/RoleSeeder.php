<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $listRoles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'db_connection' => 'mysql_admin'
            ],
            [
                'name' => 'inventaris',
                'display_name' => 'Gudang',
                'db_connection' => 'mysql_inventaris'
            ],
            [
                'name' => 'kurir',
                'display_name' => 'Pengantar',
                'db_connection' => 'mysql_kurir'
            ],
            [
                'name' => 'staff',
                'display_name' => 'Staff',
                'db_connection' => 'mysql_staff'
            ],
            [
                'name' => 'guest',
                'display_name' => 'Tamu',
                'db_connection' => 'mysql_guest'
            ]
        ];

        foreach ($listRoles as $roleData) {
            Role::create($roleData);
        }

    }
}
