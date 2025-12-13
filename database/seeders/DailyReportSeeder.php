<?php

namespace Database\Seeders;

use App\Models\DailyOutletReport;
use App\Models\DailyOutletReportItem;
use App\Models\User;
use App\Models\Outlet;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DailyReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get staff users and outlets
        $staffUsers = User::where('role_id', 5)->get();
        $outlets = Outlet::all();
        $items = \App\Models\Item::all();

        if ($staffUsers->isEmpty()) {
            $this->command->warn('⚠️  No staff users found. Skipping daily report seeding.');
            return;
        }

        if ($outlets->isEmpty()) {
            $this->command->warn('⚠️  No outlets found. Skipping daily report seeding.');
            return;
        }

        if ($items->count() < 5) {
            $this->command->warn('⚠️  Less than 5 items found. Please seed items first.');
            return;
        }

        $this->command->info('🔄 Generating daily reports for the last 60 days...');

        // Generate reports for last 60 days
        $startDate = Carbon::now()->subDays(60);
        $endDate = Carbon::now();

        $reportCount = 0;
        $itemCount = 0;

        // Loop through each day
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            // Create 1-3 reports per day (random outlets)
            $numReports = min(rand(1, 3), $outlets->count()); // Don't exceed available outlets
            
            // Track which outlets we've used today to avoid duplicates
            $usedOutlets = [];
            
            for ($i = 0; $i < $numReports; $i++) {
                // Get a random outlet that hasn't been used today
                $availableOutlets = $outlets->filter(function($outlet) use ($usedOutlets) {
                    return !in_array($outlet->id, $usedOutlets);
                });
                
                if ($availableOutlets->isEmpty()) {
                    break; // No more outlets available for today
                }
                
                $outlet = $availableOutlets->random();
                $usedOutlets[] = $outlet->id; // Mark as used
                
                $staff = $staffUsers->random();

                // Create the report
                $report = DailyOutletReport::create([
                    'id_outlet' => $outlet->id,
                    'id_staff' => $staff->id,
                    'report_date' => $date->toDateString(),
                    'report_time' => Carbon::createFromTime(rand(8, 18), rand(0, 59))->format('H:i:s'),
                    'is_validated' => rand(0, 100) < 80, // 80% validated
                    'notes' => rand(0, 100) < 30 ? fake()->sentence() : null,
                    'created_by_name' => $staff->display_name,
                    'outlet_name' => $outlet->name,
                ]);

                $reportCount++;

                // Get items 1-5 that actually exist
                $targetItems = \App\Models\Item::whereIn('id', [1, 2, 3, 4, 5])->get();
                
                // Create report items for items 1-5 (for chart testing)
                foreach ($targetItems as $item) {
                    $initialStock = rand(50, 200);
                    $stockDelivered = rand(0, 100);
                    $qtySold = rand(10, 150);
                    $qtyDamaged = rand(0, 10);
                    $stockReturned = rand(0, 20);
                    $stockRemained = max(0, $initialStock + $stockDelivered - $qtySold - $qtyDamaged - $stockReturned);

                    DailyOutletReportItem::factory()
                        ->forItem($item->id)
                        ->create([
                            'id_outlet_report' => $report->id,
                            'initial_stock' => $initialStock,
                            'stock_delivered' => $stockDelivered,
                            'stock_returned' => $stockReturned,
                            'qty_damaged' => $qtyDamaged,
                            'stock_remained' => $stockRemained,
                            'qty_sold' => $qtySold,
                        ]);

                    $itemCount++;
                }

                // Optionally add 2-3 more random items (items 6+, to avoid duplicates)
                $extraItems = rand(2, 3);
                $otherItems = \App\Models\Item::where('id', '>', 5)->inRandomOrder()->limit($extraItems)->get();
                
                foreach ($otherItems as $extraItem) {
                    DailyOutletReportItem::factory()
                        ->create([
                            'id_outlet_report' => $report->id,
                            'id_item' => $extraItem->id,
                            'item_name' => $extraItem->name,
                            'item_unit' => $extraItem->unit,
                        ]);
                        
                    $itemCount++;
                }
            }
        }

        $this->command->info("✅ Created $reportCount daily reports");
        $this->command->info("✅ Created $itemCount report items");
        $this->command->info('🎉 Daily report seeding completed!');
    }
}
