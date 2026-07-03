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

    public ?int $sortOrder = null;

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
            'sortOrder' => ['nullable', 'integer', 'min:0'],
        ];
    }

    #[On('open-medical-experience-form')]
    public function open(?string $id = null): void
    {
        $this->resetValidation();

        if ($id) {
            $this->medicalExperience = MedicalExperience::find($id);

            if (! $this->medicalExperience) {
                Flux::toast(text: __('Medical experience not found.'), variant: 'danger');

                return;
            }

            $this->name = $this->medicalExperience->name;
            $this->sortOrder = $this->medicalExperience->sort_order;
            $this->isEditing = true;
        } else {
            $this->reset('name', 'sortOrder', 'medicalExperience');
            $this->isEditing = false;
        }

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorize($this->isEditing ? 'medical_experiences update' : 'medical_experiences store');

        $validated = $this->validate();

        $data = [
            'name' => $validated['name'],
            'sort_order' => $validated['sortOrder'],
        ];

        if ($this->isEditing && $this->medicalExperience) {
            $this->medicalExperience->update($data);
            Flux::toast(text: __('Medical experience updated successfully.'), variant: 'success');
        } else {
            MedicalExperience::create($data);
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
