<?php

namespace App\Livewire\AiCare;

use App\Models\CaseNote;
use App\Models\CaseTemplate;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    public string $tagHistory = '';

    public string $patientSummary = '';

    public bool $speakerDiarization = false;

    public string $clinicalStyle = '';

    public ?string $selectedTemplate = null;

    public ?string $selectedTranscription = null;

    public string $mainInput = '';

    public string $caseOneResult = '';

    public string $caseTwoResult = '';

    public ?string $caseOneFeedback = null;

    public ?string $caseTwoFeedback = null;

    public string $caseOneComment = '';

    public string $caseTwoComment = '';

    public bool $isGenerating = false;

    public bool $isGeneratingSecond = false;

    public function startConsultation(): void
    {
        // TODO: integrate with AI streaming API
        \Flux::toast(__('Consultation started — recording audio...'));
    }

    public function resetForm(): void
    {
        $this->reset([
            'tagHistory',
            'patientSummary',
            'speakerDiarization',
            'clinicalStyle',
            'selectedTemplate',
            'selectedTranscription',
            'mainInput',
            'caseOneResult',
            'caseTwoResult',
            'caseOneFeedback',
            'caseTwoFeedback',
            'caseOneComment',
            'caseTwoComment',
        ]);

        \Flux::toast(__('Form reset successfully.'));
    }

    public function generateCase(): void
    {
        if (blank($this->mainInput)) {
            \Flux::toast(__('Please enter patient summary or clinical text first.'), variant: 'danger');

            return;
        }

        $this->isGenerating = true;

        // TODO: call AI service for case generation
        $this->caseOneResult = '';
        $this->isGenerating = false;

        \Flux::toast(__('Case generation started...'));
    }

    public function generateSecondCase(): void
    {
        if (blank($this->mainInput)) {
            \Flux::toast(__('Please enter patient summary or clinical text first.'), variant: 'danger');

            return;
        }

        $this->isGeneratingSecond = true;

        // TODO: call AI service for second case generation
        $this->caseTwoResult = '';
        $this->isGeneratingSecond = false;

        \Flux::toast(__('Second case generation started...'));
    }

    public function saveScribe(): void
    {
        if (blank($this->mainInput)) {
            \Flux::toast(__('Nothing to save.'), variant: 'danger');

            return;
        }

        CaseNote::create([
            'user_id' => auth()->id(),
            'title' => 'Scribe — '.now()->format('d M Y H:i'),
            'input' => $this->mainInput,
            'output' => null,
        ]);

        \Flux::toast(__('Scribe saved successfully.'));
    }

    public function saveVisitCasenote(int $caseNumber): void
    {
        $output = $caseNumber === 1 ? $this->caseOneResult : $this->caseTwoResult;

        if (blank($output)) {
            \Flux::toast(__('No result to save.'), variant: 'danger');

            return;
        }

        CaseNote::create([
            'user_id' => auth()->id(),
            'input' => $this->mainInput,
            'output' => $output,
        ]);

        \Flux::toast(__('Visit casenote saved successfully.'));
    }

    public function copyToClipboard(int $caseNumber): void
    {
        $this->dispatch('copy-to-clipboard', text: $caseNumber === 1 ? $this->caseOneResult : $this->caseTwoResult);
    }

    public function submitFeedback(int $caseNumber, ?string $vote): void
    {
        if ($caseNumber === 1) {
            $this->caseOneFeedback = $vote;
        } else {
            $this->caseTwoFeedback = $vote;
        }
    }

    public function saveFeedbackComment(int $caseNumber): void
    {
        $feedback = $caseNumber === 1 ? $this->caseOneFeedback : $this->caseTwoFeedback;
        $comment = $caseNumber === 1 ? $this->caseOneComment : $this->caseTwoComment;

        if (blank($feedback)) {
            \Flux::toast(__('Please select a feedback option first.'), variant: 'danger');

            return;
        }

        // TODO: persist feedback to a model/table

        if ($caseNumber === 1) {
            $this->caseOneComment = '';
        } else {
            $this->caseTwoComment = '';
        }

        \Flux::toast(__('Feedback saved. Thank you!'));
    }

    /** @return array<int, array{value: string, label: string}> */
    public function getTemplateOptions(): array
    {
        return CaseTemplate::query()
            ->where('is_active', true)
            ->pluck('name', 'id')
            ->map(fn (string $name, string $id) => ['value' => $id, 'label' => $name])
            ->values()
            ->toArray();
    }

    public function render(): View
    {
        return view('livewire.ai-care.index', [
            'templateOptions' => $this->getTemplateOptions(),
        ]);
    }
}
