<?php

namespace Database\Factories;

use App\Models\DeliveryMistake;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeliveryMistakeConfirmation>
 */
class DeliveryMistakeConfirmationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_delivery_mistake' => DeliveryMistake::factory(),
            'id_inventaris' => User::factory(),
            'confirmed_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
