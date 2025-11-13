<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Outlet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class OutletItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_outlet' => Outlet::factory(),
            'id_item' => Item::factory(),
            'quantity' => fake()->numberBetween(1, 100),
        ];
    }
}
