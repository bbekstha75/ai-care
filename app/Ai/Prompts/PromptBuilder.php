<?php

namespace App\Ai\Prompts;

class PromptBuilder
{
    /**
     * Build a prompt from the given template and input data.
     *
     * @param array<string, mixed> $data
     */
    public function build(array $data): string
    {
        $prompt = "--- CLINICAL DATA ---\n";

        if (!empty($data['patient_summary'])) {
            $prompt .= "\nPATIENT SUMMARY:\n" . $data['patient_summary'] . "\n";
        }

        if (!empty($data['tag_history'])) {
            $prompt .= "\nTAG HISTORY:\n" . $data['tag_history'] . "\n";
        }

        if (!empty($data['input'])) {
            $prompt .= "\nMAIN INPUT:\n" . $data['input'] . "\n";
        }

        return $prompt;
    }
}
