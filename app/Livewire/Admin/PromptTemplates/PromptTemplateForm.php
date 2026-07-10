<?php

namespace App\Livewire\Admin\PromptTemplates;

use App\Models\PromptTemplate;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class PromptTemplateForm extends Component
{
    public bool $showModal = false;

    public bool $isEditing = false;

    public ?PromptTemplate $model = null;

    public string $title = '';

    public ?string $description = '';

    public string $systemPrompt = '';

    public ?string $stylingInstructions = '';

    public bool $status = true;

    #[On('create-prompt-template')]
    public function create(): void
    {
        $this->authorize('prompt_templates store');
        $this->resetForm();
        $this->showModal = true;
    }

    #[On('edit-prompt-template')]
    public function edit(int $id): void
    {
        $this->authorize('prompt_templates update');
        $this->resetForm();

        $this->model = PromptTemplate::find($id);

        if (! $this->model) {
            \Flux::toast(__('Prompt Template not found.'), variant: 'danger');

            return;
        }

        $this->isEditing = true;
        $this->title = $this->model->title;
        $this->description = $this->model->description;
        $this->systemPrompt = $this->model->system_prompt;
        $this->stylingInstructions = $this->model->styling_instructions;
        $this->status = $this->model->status;

        $this->showModal = true;
    }

    public function save(): void
    {
        $permission = $this->isEditing ? 'prompt_templates update' : 'prompt_templates store';
        $this->authorize($permission);

        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'systemPrompt' => 'required|string',
            'stylingInstructions' => 'nullable|string',
            'status' => 'boolean',
        ]);

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'system_prompt' => $this->systemPrompt,
            'styling_instructions' => $this->stylingInstructions,
            'status' => $this->status,
        ];

        if ($this->isEditing && $this->model) {
            $this->model->update($data);
            \Flux::toast(__('Prompt Template updated successfully.'));
        } else {
            PromptTemplate::create($data);
            \Flux::toast(__('Prompt Template created successfully.'));
        }

        $this->dispatch('prompt-template-saved');
        $this->showModal = false;
    }

    private function resetForm(): void
    {
        $this->reset([
            'isEditing',
            'model',
            'title',
            'description',
            'systemPrompt',
            'stylingInstructions',
        ]);
        $this->status = true;
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.admin.prompt-templates.prompt-template-form');
    }
}
