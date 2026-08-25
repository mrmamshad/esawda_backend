<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'badge' => 'popular',
            'monthly_price' => fake()->numberBetween(100, 1000),
            'annual_price' => fake()->numberBetween(1000, 10000),
            'lifetime_price' => fake()->numberBetween(5000, 50000),
            'recommended' => 'no',
            'settings' => json_encode([
                'ads_limit' => fake()->numberBetween(5, 100),
                'featured_ads' => fake()->numberBetween(1, 10),
                'duration_days' => 30,
            ]),
            'taxes_ids' => null,
            'status' => 1,
            'date' => now(),
        ];
    }
}
