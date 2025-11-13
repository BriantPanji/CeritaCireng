<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        
        
        $this->call(RoleSeeder::class);
        $this->call(OutletSeeder::class);
        User::create([
            'display_name' => 'Super Admin',
            'username' => 'superadmin',
            'phone' => '081234567890',
            'password' => bcrypt('pass#123'),
            'role_id' => 1,
            'outlet_id' => 1,
            'status' => 'AKTIF',
        ]);
    }
}
