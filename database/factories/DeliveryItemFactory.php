<?php

namespace Database\Factories;

use App\Models\Delivery;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class DeliveryItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_delivery' => Delivery::factory(),
            'id_item' => Item::factory(),
            'quantity' => fake()->numberBetween(1, 50),
        ];
    }
}
