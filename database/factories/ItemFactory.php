<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'cost' => fake()->numberBetween(1000, 100000),
            'unit' => fake()->randomElement(['pcs', 'gr', 'ml', 'unit']),
            'type' => fake()->randomElement(['BAHAN_MENTAH', 'BAHAN_PENUNJANG', 'KEMASAN'])
        ];
    }
}
