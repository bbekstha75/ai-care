<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SubscriptionPlan::create([
            'name' => 'Free',
            'allowed_prompts' => ['general', 'diagnosis'],
            'max_generations_per_month' => 50,
            'allow_file_transcription' => false,
            'features' => ['Basic AI Assistance', 'Community Support'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        SubscriptionPlan::create([
            'name' => 'Pro',
            'allowed_prompts' => ['general', 'diagnosis', 'treatment_plan', 'prescription'],
            'max_generations_per_month' => 500,
            'allow_file_transcription' => true,
            'features' => ['Advanced AI Assistance', 'Priority Support', 'File Transcription', 'Custom Templates'],
            'is_active' => true,
            'sort_order' => 2,
        ]);
    }
}
