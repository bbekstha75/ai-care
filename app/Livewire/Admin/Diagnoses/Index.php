<?php

namespace App\Livewire\Admin\Diagnoses;

use App\Livewire\Concerns\HasDeleteConfirmation;
use App\Livewire\Concerns\HasSearch;
use App\Models\Diagnosis;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Diagnoses')]
#[Layout('layouts.app')]
class Index extends Component
{
    use HasDeleteConfirmation, HasSearch, WithPagination;

    public function delete(): void
    {
        $this->authorize('diagnoses delete');

        $diagnosis = Diagnosis::find($this->deleteId);

        if (! $diagnosis) {
            Flux::toast(text: __('Diagnosis not found.'), variant: 'danger');
            $this->deleteId = null;

            return;
        }

        $diagnosis->delete();
        $this->deleteId = null;

        Flux::toast(text: __('Diagnosis deleted successfully.'), variant: 'success');
    }

    #[On('diagnosis-saved')]
    public function refreshList(): void {}

    public function render(): View
    {
        $diagnoses = Diagnosis::query()
            ->when($this->search, fn ($query) => $query->where('name', 'like', '%'.$this->search.'%'))
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.diagnoses.index', [
            'diagnoses' => $diagnoses,
        ]);
    }
}
