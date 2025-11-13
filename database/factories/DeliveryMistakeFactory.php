<?php

namespace Database\Factories;

use App\Models\Delivery;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeliveryMistake>
 */
class DeliveryMistakeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_delivery' => Delivery::inRandomOrder()->first()->id ?? Delivery::factory()->create()->id,
            'id_staff' => User::inRandomOrder()->first()->id ?? User::factory()->create()->id,
            'photo_url' => fake()->imageUrl(),
            'notes' => fake()->optional(0.3)->sentence(),
            'reported_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
