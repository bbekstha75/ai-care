<?php

namespace App\Livewire\Transcriptions;

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
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function transcriptions()
    {
        return CaseNote::query()
            ->where('user_id', auth()->id())
            ->whereNotNull('title') // Transcriptions have titles
            ->when($this->search, function ($query) {
                $query->where('title', 'like', "%{$this->search}%")
                    ->orWhere('input', 'like', "%{$this->search}%");
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
        \Flux::toast('Transcription deleted successfully.');
    }

    public function render(): View
    {
        return view('livewire.transcriptions.index');
    }
}
