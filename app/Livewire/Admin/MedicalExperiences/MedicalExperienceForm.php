<?php

namespace App\Livewire\Admin\MedicalExperiences;

use App\Models\MedicalExperience;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class MedicalExperienceForm extends Component
{
    public bool $showModal = false;

    public bool $isEditing = false;

    public ?MedicalExperience $medicalExperience = null;

    public string $name = '';

    public ?int $sort_order = null;

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $uniqueRule = Rule::unique('medical_experiences', 'name');

        if ($this->isEditing && $this->medicalExperience) {
            $uniqueRule->ignore($this->medicalExperience->id);
        }

        return [
            'name' => ['required', 'string', 'max:255', $uniqueRule],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    #[On('open-medical-experience-form')]
    public function open(?string $id = null): void
    {
        $this->resetValidation();

        if ($id) {
            $this->medicalExperience = MedicalExperience::findOrFail($id);
            $this->name = $this->medicalExperience->name;
            $this->sort_order = $this->medicalExperience->sort_order;
            $this->isEditing = true;
        } else {
            $this->reset('name', 'sort_order', 'medicalExperience');
            $this->isEditing = false;
        }

        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->isEditing && $this->medicalExperience) {
            $this->medicalExperience->update($validated);
            Flux::toast(text: __('Medical experience updated successfully.'), variant: 'success');
        } else {
            MedicalExperience::create($validated);
            Flux::toast(text: __('Medical experience created successfully.'), variant: 'success');
        }

        $this->showModal = false;

        $this->dispatch('medical-experience-saved');
    }

    public function render(): View
    {
        return view('livewire.admin.medical-experiences.medical-experience-form');
    }
}
