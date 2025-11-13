<?php

namespace Database\Factories;

use App\Models\DeliveryMistake;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class DeliveryMistakeItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_delivery_mistake' => DeliveryMistake::inRandomOrder()->first()->id,
            'id_item' => Item::inRandomOrder()->first()->id,
            'quantity' => fake()->numberBetween(1, 50),
        ];
    }
}
