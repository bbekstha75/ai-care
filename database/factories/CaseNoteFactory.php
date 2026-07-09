<?php

namespace Database\Factories;

use App\Enums\CaseNoteStatus;
use App\Models\CaseNote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CaseNote>
 */
class CaseNoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => null, // Default to null (Case Note)
            'input' => $this->faker->paragraphs(3, true),
            'output' => '<p>'.$this->faker->paragraph().'</p><ul><li>'.$this->faker->word().'</li><li>'.$this->faker->word().'</li></ul>',
            'status' => CaseNoteStatus::Draft,
            'highlight_color' => null,
            'notes' => null,
        ];
    }

    public function withTitle(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => $this->faker->sentence(),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CaseNoteStatus::Draft,
        ]);
    }

    public function saved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CaseNoteStatus::Saved,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CaseNoteStatus::Archived,
        ]);
    }
}
