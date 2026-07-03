<?php

namespace App\Livewire\Admin\Diagnoses;

use App\Models\Diagnosis;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class DiagnosisForm extends Component
{
    public bool $showModal = false;

    public bool $isEditing = false;

    public ?Diagnosis $diagnosis = null;

    public string $name = '';

    public ?string $symptoms = null;

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $uniqueRule = Rule::unique('diagnoses', 'name');

        if ($this->isEditing && $this->diagnosis) {
            $uniqueRule->ignore($this->diagnosis->id);
        }

        return [
            'name' => ['required', 'string', 'max:255', $uniqueRule],
            'symptoms' => ['nullable', 'string'],
        ];
    }

    #[On('open-diagnosis-form')]
    public function open(?string $id = null): void
    {
        $this->resetValidation();

        if ($id) {
            $this->diagnosis = Diagnosis::find($id);

            if (! $this->diagnosis) {
                Flux::toast(text: __('Diagnosis not found.'), variant: 'danger');

                return;
            }

            $this->name = $this->diagnosis->name;
            $this->symptoms = $this->diagnosis->symptoms;
            $this->isEditing = true;
        } else {
            $this->reset('name', 'symptoms', 'diagnosis');
            $this->isEditing = false;
        }

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorize($this->isEditing ? 'diagnoses update' : 'diagnoses store');

        $validated = $this->validate();

        if ($this->isEditing && $this->diagnosis) {
            $this->diagnosis->update($validated);
            Flux::toast(text: __('Diagnosis updated successfully.'), variant: 'success');
        } else {
            Diagnosis::create($validated);
            Flux::toast(text: __('Diagnosis created successfully.'), variant: 'success');
        }

        $this->showModal = false;

        $this->dispatch('diagnosis-saved');
    }

    public function render(): View
    {
        return view('livewire.admin.diagnoses.diagnosis-form');
    }
}
