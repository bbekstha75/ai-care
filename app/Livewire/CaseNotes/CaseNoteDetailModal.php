<?php

namespace App\Livewire\CaseNotes;

use App\Models\CaseNote;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class CaseNoteDetailModal extends Component
{
    public ?CaseNote $caseNote = null;

    public string $type = 'input'; // 'input' or 'output'

    #[On('open-case-note-detail')]
    public function open(string $id, string $type = 'input'): void
    {
        $this->caseNote = CaseNote::where('user_id', auth()->id())->findOrFail($id);
        $this->type = $type;

        \Flux::modal('case-note-detail')->show();
    }

    public function render(): View
    {
        return view('livewire.case-notes.case-note-detail-modal');
    }
}
