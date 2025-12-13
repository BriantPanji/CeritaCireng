<?php

namespace Database\Factories;

use App\Models\DailyOutletReportItem;
use App\Models\DailyOutletReport;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DailyOutletReportItem>
 */
class DailyOutletReportItemFactory extends Factory
{
    protected $model = DailyOutletReportItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $item = Item::inRandomOrder()->first();
        $initialStock = fake()->numberBetween(50, 200);
        $stockDelivered = fake()->numberBetween(0, 100);
        $qtySold = fake()->numberBetween(10, 150);
        $qtyDamaged = fake()->numberBetween(0, 10);
        $stockReturned = fake()->numberBetween(0, 20);
        $stockRemained = max(0, $initialStock + $stockDelivered - $qtySold - $qtyDamaged - $stockReturned);

        return [
            'id_outlet_report' => DailyOutletReport::factory(),
            'id_item' => $item?->id ?? Item::factory(),
            'item_name' => $item?->name ?? fake()->word(),
            'item_cost' => fake()->numberBetween(5000, 50000),
            'item_unit' => $item?->unit ?? 'pcs',
            'initial_stock' => $initialStock,
            'stock_delivered' => $stockDelivered,
            'stock_returned' => $stockReturned,
            'qty_damaged' => $qtyDamaged,
            'stock_remained' => $stockRemained,
            'qty_sold' => $qtySold,
            'total_expense' => $qtySold * fake()->numberBetween(5000, 50000),
        ];
    }

    /**
     * Create item for specific item ID (useful for items 1-5).
     */
    public function forItem(int $itemId): static
    {
        return $this->state(function (array $attributes) use ($itemId) {
            $item = Item::find($itemId);
            
            return [
                'id_item' => $itemId,
                'item_name' => $item?->name ?? "Item $itemId",
                'item_unit' => $item?->unit ?? 'pcs',
            ];
        });
    }

    /**
     * Create item with high sales.
     */
    public function highSales(): static
    {
        return $this->state(fn (array $attributes) => [
            'qty_sold' => fake()->numberBetween(100, 200),
            'initial_stock' => fake()->numberBetween(150, 300),
        ]);
    }

    /**
     * Create item with low sales.
     */
    public function lowSales(): static
    {
        return $this->state(fn (array $attributes) => [
            'qty_sold' => fake()->numberBetween(5, 30),
            'initial_stock' => fake()->numberBetween(50, 100),
        ]);
    }
}
