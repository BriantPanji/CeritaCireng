<?php

namespace Database\Factories;

use App\Models\ReturnModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReturnConfirmation>
 */
class ReturnConfirmationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_return' => ReturnModel::factory(),
            'id_inventaris' => User::factory(),
            'notes' => fake()->optional(0.3)->sentence(),
            'confirmed_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
