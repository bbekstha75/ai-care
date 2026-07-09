<?php

namespace App\Livewire\CaseNotes;

use App\Models\CaseNote;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CaseNoteForm extends Component
{
    public ?CaseNote $caseNote = null;

    #[Validate('nullable|string')]
    public ?string $output = null;

    #[On('open-case-note-form')]
    public function open(?string $id = null): void
    {
        $this->resetErrorBag();
        $this->resetValidation();

        if ($id) {
            $this->caseNote = CaseNote::where('user_id', auth()->id())->findOrFail($id);
            $this->output = $this->caseNote->output;
        } else {
            $this->caseNote = new CaseNote;
            $this->output = null;
        }

        \Flux::modal('case-note-form')->show();
    }

    public function save(): void
    {
        $this->validate();

        $this->caseNote->user_id = auth()->id();
        $this->caseNote->output = $this->output;
        $this->caseNote->save();

        \Flux::modal('case-note-form')->close();
        \Flux::toast('Case note saved successfully.');

        $this->dispatch('case-note-saved');
        $this->redirect(route('case-notes.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.case-notes.case-note-form');
    }
}
