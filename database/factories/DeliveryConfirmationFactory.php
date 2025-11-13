<?php

namespace Database\Factories;

use App\Models\Delivery;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeliveryConfirmation>
 */
class DeliveryConfirmationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_delivery' => Delivery::inRandomOrder()->first()->id,
            'id_staff' => User::inRandomOrder()->first()->id,
            'notes' => fake()->optional(0.3)->sentence(),
            'received_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
