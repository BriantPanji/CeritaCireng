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
        $inventaris = User::whereHas('role', fn ($query) => $query->where('name', 'inventaris'))->inRandomOrder()->first() ?? User::factory()->create(['role_id' => \App\Models\Role::where('name', 'inventaris')->first()->id]);
        $kurir = User::whereHas('role', fn ($query) => $query->where('name', 'kurir'))->inRandomOrder()->first() ?? User::factory()->create(['role_id' => \App\Models\Role::where('name', 'kurir')->first()->id]);

        return [
            'id_inventaris' => $inventaris->id,
            'id_kurir' => $kurir->id,
            'id_outlet' => \App\Models\Outlet::inRandomOrder()->first()->id ?? \App\Models\Outlet::factory()->create()->id,
            'status' => fake()->randomElement(['DITUGASKAN', 'DIANTAR', 'SELESAI', 'DIBATALKAN']),
            'photo_evidence' => fake()->imageUrl(),
            'assigned_at' => fake()->dateTimeBetween('-2 weeks', 'now'),
            'delivered_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
