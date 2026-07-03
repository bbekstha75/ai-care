<?php

namespace App\Livewire\Admin\MedicalExperiences;

use App\Models\MedicalExperience;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Medical Experiences')]
#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public ?string $deleteId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(string $id): void
    {
        $this->deleteId = $id;
    }

    public function delete(): void
    {
        if ($this->deleteId) {
            $medicalExperience = MedicalExperience::findOrFail($this->deleteId);
            $medicalExperience->delete();
            $this->deleteId = null;

            Flux::toast(variant: 'success', text: __('Medical experience deleted successfully.'));
        }
    }

    #[On('medical-experience-saved')]
    public function refreshList(): void
    {
        // This empty method simply triggers a re-render
    }

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
