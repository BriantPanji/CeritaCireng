<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OtherExpense>
 */
class OtherExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_staff' => User::factory(),
            'category' => fake()->randomElement(['GAS', 'GALON', 'LAINNYA']),
            'description' => fake()->sentence(),
            'cost' => fake()->numberBetween(10000, 500000),
        ];
    }
}
