<?php

namespace Database\Factories;

use App\Models\MedicalExperience;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicalExperience>
 */
class MedicalExperienceFactory extends Factory
{
    protected $model = MedicalExperience::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
