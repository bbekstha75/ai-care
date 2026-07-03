<?php

namespace Database\Factories;

use App\Models\CaseTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CaseTemplate>
 */
class CaseTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'description' => '<p>' . $this->faker->paragraph() . '</p>',
            'is_active' => $this->faker->boolean(80),
        ];
    }
}
