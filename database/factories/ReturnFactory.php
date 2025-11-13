<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ReturnFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_staff' => User::inRandomOrder()->first()->id,
            'id_deliverer' => fake()->optional(0.45)->randomElement(User::pluck('id')->toArray()),
            'notes' => fake()->optional(0.3)->sentence(),
            'returned_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
