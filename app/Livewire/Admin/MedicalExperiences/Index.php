<?php

namespace App\Livewire\Admin\MedicalExperiences;

use App\Livewire\Concerns\HasDeleteConfirmation;
use App\Livewire\Concerns\HasSearch;
use App\Models\MedicalExperience;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Medical Experiences')]
#[Layout('layouts.app')]
class Index extends Component
{
    use HasDeleteConfirmation, HasSearch, WithPagination;

    public function delete(): void
    {
        $this->authorize('medical_experiences delete');

        $medicalExperience = MedicalExperience::find($this->deleteId);

        if (! $medicalExperience) {
            Flux::toast(text: __('Medical experience not found.'), variant: 'danger');
            $this->deleteId = null;

            return;
        }

        $medicalExperience->delete();
        $this->deleteId = null;

        Flux::toast(text: __('Medical experience deleted successfully.'), variant: 'success');
    }

    #[On('medical-experience-saved')]
    public function refreshList(): void {}

    public function render(): View
    {
        $medicalExperiences = MedicalExperience::query()
            ->when($this->search, fn ($query) => $query->where('name', 'like', '%'.$this->search.'%'))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.medical-experiences.index', [
            'medicalExperiences' => $medicalExperiences,
        ]);
    }
}
