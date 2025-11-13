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
        $inventaris = User::whereHas('role', fn ($query) => $query->where('name', 'inventaris'))->inRandomOrder()->first() ?? User::factory()->create(['role_id' => \App\Models\Role::where('name', 'inventaris')->first()->id]);

        return [
            'id_return' => ReturnModel::inRandomOrder()->first()->id ?? ReturnModel::factory()->create()->id,
            'id_inventaris' => $inventaris->id,
            'notes' => fake()->optional(0.3)->sentence(),
            'confirmed_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
