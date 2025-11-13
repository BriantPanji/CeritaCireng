<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Delivery>
 */
class DeliveryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_inventaris' => User::factory(),
            'id_kurir' => User::factory(),
            'id_outlet' => \App\Models\Outlet::factory(),
            'status' => fake()->randomElement(['DITUGASKAN', 'DIKIRIM', 'SELESAI', 'DIBATALKAN']),
            'photo_evidence' => fake()->imageUrl(),
            'assigned_at' => fake()->dateTimeBetween('-2 weeks', 'now'),
            'delivered_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
