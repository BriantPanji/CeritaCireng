<?php

namespace Database\Factories;

use App\Models\ReturnModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ReturnEvidenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_return' => ReturnModel::inRandomOrder()->first()->id ?? ReturnModel::factory()->create()->id,
            'url' => fake()->imageUrl(),
        ];
    }
}
