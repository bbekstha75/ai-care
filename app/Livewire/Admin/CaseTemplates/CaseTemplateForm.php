<?php

namespace App\Livewire\Admin\CaseTemplates;

use App\Models\CaseTemplate;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class CaseTemplateForm extends Component
{
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?CaseTemplate $model = null;

    public string $name = '';
    public ?string $description = null;
    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('case_templates', 'name')->ignore($this->model?->id),
            ],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    #[On('open-case-template-form')]
    public function open(?string $id = null): void
    {
        $this->resetValidation();
        $this->reset(['name', 'description', 'is_active']);

        if ($id) {
            $this->model = CaseTemplate::find($id);

            if (! $this->model) {
                \Flux::toast(__('Case Template not found.'), variant: 'danger');
                return;
            }

            $this->isEditing = true;
            $this->name = $this->model->name;
            $this->description = $this->model->description;
            $this->is_active = $this->model->is_active ?? true;
        } else {
            $this->isEditing = false;
            $this->model = null;
            $this->is_active = true;
        }

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorize($this->isEditing ? 'case_templates update' : 'case_templates store');

        $validated = $this->validate();

        if ($this->isEditing) {
            $this->model->update($validated);
            \Flux::toast(__('Case Template updated successfully.'));
        } else {
            CaseTemplate::create($validated);
            \Flux::toast(__('Case Template created successfully.'));
        }

        $this->showModal = false;
        $this->dispatch('case-template-saved');
    }

    public function render(): View
    {
        $this->authorize('case_templates list');

        return view('livewire.admin.case-templates.case-template-form');
    }
}
