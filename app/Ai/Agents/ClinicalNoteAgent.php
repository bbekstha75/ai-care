<?php

namespace App\Ai\Agents;

use App\Models\PromptTemplate;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Stringable;

class ClinicalNoteAgent implements Agent, Conversational, HasTools
{
    use Promptable, RemembersConversations;

    public function __construct(
        protected PromptTemplate $template
    )
    {
    }

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        $instructions = $this->template->system_prompt ?? "You are a helpful clinical assistant.";

        $instructions .= "\n\nDEFAULT FORMATTING RULES:\n";
        $instructions .= "- Always use Markdown format for your output.\n";
        $instructions .= "- Use proper headers (##, ###) for sections.\n";
        $instructions .= "- CRITICAL: Always insert a blank line (double return) after every heading, before writing the answer/content.\n";
        $instructions .= "- Use bullet points or numbered lists where appropriate for readability.\n";
        $instructions .= "- Use bold and italic text to emphasize important clinical information.\n";
        $instructions .= "- CRITICAL: Ensure proper spacing (empty lines) between different sections and paragraphs for the best visual experience.\n";

        if (!empty($this->template->styling_instructions)) {
            $instructions .= "\n\nADDITIONAL STYLING INSTRUCTIONS:\n" . $this->template->styling_instructions;
        }

        return $instructions;
    }

    /**
     * Get the list of messages comprising the conversation so far.
     *
     * @return Message[]
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [];
    }
}
