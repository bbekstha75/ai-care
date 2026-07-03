<?php

namespace Database\Factories;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubscriptionPlan>
 */
class SubscriptionPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word().' Plan',
            'allowed_prompts' => ['general'],
            'max_generations_per_month' => fake()->numberBetween(10, 1000),
            'allow_file_transcription' => fake()->boolean(),
            'features' => ['Feature 1', 'Feature 2'],
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }
}
