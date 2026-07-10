<?php

namespace App\Ai\Actions;

use App\Ai\Agents\ClinicalNoteAgent;
use App\Ai\Prompts\PromptBuilder;
use App\Models\PromptTemplate;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class GenerateClinicalNote
{
    public function __construct(
        protected PromptBuilder $promptBuilder
    )
    {
    }

    public function execute(PromptTemplate $template, array $data)
    {
        // Build the prompt
        $prompt = $this->promptBuilder->build($data);

        $user = Auth::user();

        // Fetch the default model from settings
        $model = Setting::where('key', 'default_model')->value('value');

        // Initialize the Agent
        $agent = new ClinicalNoteAgent($template);

        return $agent
            ->forUser($user)
            ->stream(
                prompt: $prompt,
                model: $model
            );
    }
}
