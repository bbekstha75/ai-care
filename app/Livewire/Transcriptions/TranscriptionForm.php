<?php

namespace App\Livewire\Transcriptions;

use App\Models\CaseNote;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class TranscriptionForm extends Component
{
    public ?CaseNote $transcription = null;

    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('nullable|string')]
    public ?string $output = null;

    #[On('open-transcription-form')]
    public function open(?string $id = null): void
    {
        $this->resetErrorBag();
        $this->resetValidation();

        if ($id) {
            $this->transcription = CaseNote::where('user_id', auth()->id())->findOrFail($id);
            $this->title = $this->transcription->title;
            $this->output = $this->transcription->output;
        } else {
            $this->transcription = new CaseNote;
            $this->title = '';
            $this->output = null;
        }

        \Flux::modal('transcription-form')->show();
    }

    public function save(): void
    {
        $this->validate();

        $this->transcription->user_id = auth()->id();
        $this->transcription->title = $this->title;
        $this->transcription->output = $this->output;
        $this->transcription->save();

        \Flux::modal('transcription-form')->close();
        \Flux::toast('Transcription saved successfully.');

        $this->dispatch('transcription-saved');
        $this->redirect(route('transcriptions.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.transcriptions.transcription-form');
    }
}
