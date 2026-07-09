<?php

namespace App\Livewire\CaseNotes;

use App\Livewire\Concerns\HasDeleteConfirmation;
use App\Models\CaseNote;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use HasDeleteConfirmation;
    use WithPagination;

    #[Url(history: true)]
    public string $tab = 'results'; // 'results' or 'input'

    #[Url(history: true)]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTab(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function caseNotes()
    {
        return CaseNote::query()
            ->where('user_id', auth()->id())
            ->whereNull('title') // Case notes don't have titles
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('input', 'like', "%{$this->search}%")
                        ->orWhere('output', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->paginate(10);
    }

    public function delete(): void
    {
        if (! $this->deleteId) {
            return;
        }

        CaseNote::query()
            ->where('user_id', auth()->id())
            ->where('id', $this->deleteId)
            ->delete();

        $this->deleteId = null;
        \Flux::toast('Case note deleted successfully.');
    }

    public function render(): View
    {
        return view('livewire.case-notes.index');
    }
}
