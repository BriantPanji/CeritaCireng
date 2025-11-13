<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Item;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Delivery;
use App\Models\Attendance;
use App\Models\OtherExpense;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles first
        $this->call(RoleSeeder::class);
        
        // Seed outlets
        $this->call(OutletSeeder::class);
        
        // Create super admin user
        User::create([
            'display_name' => 'Super Admin',
            'username' => 'superadmin',
            'phone' => '081234567890',
            'password' => bcrypt('pass#123'),
            'role_id' => 1,
            'outlet_id' => 1,
            'status' => 'AKTIF',
        ]);
        
        // Create additional sample users for each role
        $roles = \App\Models\Role::all();
        $outlets = \App\Models\Outlet::all();
        
        foreach ($roles as $role) {
            User::factory()->count(2)->create([
                'role_id' => $role->id,
                'outlet_id' => $outlets->random()->id,
            ]);
        }
        
        // Seed items
        Item::factory()->count(20)->create();
        
        // Seed products
        Product::factory()->count(15)->create();
        
        // Seed product items (relationships between products and items)
        $products = Product::all();
        $items = Item::all();
        
        foreach ($products as $product) {
            // Each product has 2-5 items
            $productItems = $items->random(rand(2, 5));
            foreach ($productItems as $item) {
                \DB::table('product_items')->insertOrIgnore([
                    'id_product' => $product->id,
                    'id_item' => $item->id,
                    'quantity' => rand(1, 10),
                    'optional' => (bool)rand(0, 1),
                ]);
            }
        }
        
        // Seed inventory
        foreach ($items as $item) {
            Inventory::factory()->create([
                'id_item' => $item->id,
            ]);
        }
        
        // Seed outlet item settings
        foreach ($outlets as $outlet) {
            $outletItems = $items->random(rand(10, 15));
            foreach ($outletItems as $item) {
                \DB::table('outlet_item_settings')->insertOrIgnore([
                    'id_outlet' => $outlet->id,
                    'id_item' => $item->id,
                    'quantity' => rand(1, 100),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        // Seed deliveries
        $users = User::all();
        $inventarisUsers = $users->where('role_id', 2); // Inventaris role
        $kurirUsers = $users->where('role_id', 3); // Kurir role
        
        if ($inventarisUsers->count() > 0 && $kurirUsers->count() > 0) {
            foreach (range(1, 10) as $i) {
                $delivery = Delivery::factory()->create([
                    'id_inventaris' => $inventarisUsers->random()->id,
                    'id_kurir' => $kurirUsers->random()->id,
                    'id_outlet' => $outlets->random()->id,
                ]);
                
                // Add delivery items
                $deliveryItems = $items->random(rand(3, 7));
                foreach ($deliveryItems as $item) {
                    \DB::table('delivery_items')->insert([
                        'id_delivery' => $delivery->id,
                        'id_item' => $item->id,
                        'quantity' => rand(1, 50),
                    ]);
                }
            }
        }
        
        // Seed attendances for all users
        foreach ($users as $user) {
            Attendance::factory()->count(rand(5, 15))->create([
                'id_user' => $user->id,
            ]);
        }
        
        // Seed other expenses
        $staffUsers = $users->where('role_id', 4); // Staff role
        if ($staffUsers->count() > 0) {
            foreach (range(1, 10) as $i) {
                OtherExpense::factory()->create([
                    'id_staff' => $staffUsers->random()->id,
                ]);
            }
        }
    }
}
