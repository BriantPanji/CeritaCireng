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
        $this->call(RoleSeeder::class);
        $this->call(OutletSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ItemSeeder::class);
        $this->call(OutletItemSettingSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(ProductItemSeeder::class);
        $this->call(DeliverySeeder::class);
        $this->call(DeliveryItemSeeder::class);
        $this->call(DeliveryConfirmationSeeder::class);
        $this->call(DeliveryMistakeSeeder::class);
        $this->call(DeliveryMistakeItemSeeder::class);
        $this->call(DeliveryMistakeConfirmationSeeder::class);
        $this->call(ReturnSeeder::class);
        $this->call(ReturnItemSeeder::class);
        $this->call(ReturnEvidenceSeeder::class);
        $this->call(ReturnConfirmationSeeder::class);
        $this->call(OtherExpenseSeeder::class);
        $this->call(AttendanceSeeder::class);
    }
}
