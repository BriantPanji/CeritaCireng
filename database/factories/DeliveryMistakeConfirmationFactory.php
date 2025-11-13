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
        $inventaris = User::whereHas('role', fn ($query) => $query->where('name', 'inventaris'))->inRandomOrder()->first() ?? User::factory()->create(['role_id' => \App\Models\Role::where('name', 'inventaris')->first()->id]);

        return [
            'id_delivery_mistake' => DeliveryMistake::inRandomOrder()->first()->id ?? DeliveryMistake::factory()->create()->id,
            'id_inventaris' => $inventaris->id,
            'confirmed_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
