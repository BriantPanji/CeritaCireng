<?php

namespace Database\Factories;

use App\Models\DailyOutletReport;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DailyOutletReport>
 */
class DailyOutletReportFactory extends Factory
{
    protected $model = DailyOutletReport::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $outlet = Outlet::inRandomOrder()->first();
        $staff = User::where('role_id', 5)->inRandomOrder()->first(); // role_id 5 = staff

        return [
            'id_outlet' => $outlet?->id ?? Outlet::factory(),
            'id_staff' => $staff?->id ?? User::factory()->state(['role_id' => 5]),
            'report_date' => fake()->dateTimeBetween('-60 days', 'now'),
            'report_time' => fake()->time(),
            'is_validated' => fake()->boolean(80), // 80% validated
            'notes' => fake()->optional(0.3)->sentence(),
            'created_by_name' => $staff?->display_name ?? fake()->name(),
            'outlet_name' => $outlet?->name ?? fake()->company(),
        ];
    }

    /**
     * Indicate that the report is validated.
     */
    public function validated(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_validated' => true,
        ]);
    }

    /**
     * Indicate that the report is not validated.
     */
    public function unvalidated(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_validated' => false,
        ]);
    }
}
