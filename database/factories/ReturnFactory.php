<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\ReturnModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReturnModel>
 */
class ReturnFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ReturnModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_staff' => User::factory(),
            'id_deliverer' => fake()->optional(0.1)->passthrough(User::factory()),
            'notes' => fake()->optional(0.3)->sentence(),
            'returned_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
