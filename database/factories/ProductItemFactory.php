<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ProductItemFactory extends Factory
{
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_product' => Product::inRandomOrder()->first()->id ?? Product::factory()->create()->id,
            'id_item' => Item::inRandomOrder()->first()->id ?? Item::factory()->create()->id,
            'quantity' => fake()->numberBetween(1, 10),
            'optional' => fake()->boolean(40)
        ];
    }
}
