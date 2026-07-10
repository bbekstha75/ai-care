<?php

namespace App\Livewire\AiCare;

use App\Ai\Actions\GenerateClinicalNote;
use App\Models\CaseNote;
use App\Models\CaseTemplate;
use App\Models\PromptTemplate;
use Illuminate\Contracts\View\View;
use Laravel\Ai\Streaming\Events\TextDelta;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
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

    // Consolidated case state
    public array $cases = [
        1 => ['result' => '', 'feedback' => null, 'comment' => '', 'isGenerating' => false],
        2 => ['result' => '', 'feedback' => null, 'comment' => '', 'isGenerating' => false],
    ];

    public function startConsultation(): void
    {
        // TODO: integrate with AI streaming API
        \Flux::toast(__('Consultation started — recording audio...'));
    }

    public function resetForm(): void
    {
        $this->reset(); // Resets all public properties to their initial state
        \Flux::toast(__('Form reset successfully.'));
    }

    public function generateCase(int $caseNumber, GenerateClinicalNote $action): void
    {
        if (!$this->validatePrerequisites()) {
            return;
        }

        $template = PromptTemplate::find($this->clinicalStyle);

        if (!$template) {
            \Flux::toast(__('Selected clinical style not found.'), variant: 'danger');

            return;
        }

        $this->cases[$caseNumber]['isGenerating'] = true;

        $data = [
            'input' => $this->mainInput,
            'patient_summary' => $this->patientSummary,
            'tag_history' => $this->tagHistory,
            'case_number' => $caseNumber,
        ];

        try {
            $stream = $action->execute($template, $data);
            $streamTarget = "cases.{$caseNumber}.result";

            foreach ($stream as $event) {
                if ($event instanceof TextDelta) {
                    $this->cases[$caseNumber]['result'] .= $event->delta;

                    $this->stream(
                        content: $event->delta,
                        to: $streamTarget,
                    );
                }
            }
        } catch (\Exception $e) {
            \Flux::toast(__('Error during generation: ') . $e->getMessage(), variant: 'danger');
        }

        $this->cases[$caseNumber]['isGenerating'] = false;
    }

    public function saveScribe(): void
    {
        if (blank($this->mainInput)) {
            \Flux::toast(__('Nothing to save.'), variant: 'danger');

            return;
        }

        CaseNote::create([
            'user_id' => auth()->id(),
            'title' => 'Scribe — ' . now()->format('d M Y H:i'),
            'input' => $this->mainInput,
            'output' => null,
        ]);

        \Flux::toast(__('Scribe saved successfully.'));
    }

    #[On('transcription-complete')]
    public function transcriptionCompleted($data): void
    {
        $this->mainInput = $data['transcript'];
    }

    public function saveVisitCasenote(int $caseNumber): void
    {
        $output = $this->cases[$caseNumber]['result'] ?? null;

        if (blank($output)) {
            \Flux::toast(__('No result to save for Case ') . $caseNumber . '.', variant: 'danger');

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
        $this->dispatch('copy-to-clipboard', text: $this->cases[$caseNumber]['result']);
    }

    public function submitFeedback(int $caseNumber, ?string $vote): void
    {
        $this->cases[$caseNumber]['feedback'] = $vote;
    }

    public function saveFeedbackComment(int $caseNumber): void
    {
        $case = $this->cases[$caseNumber];

        if (blank($case['feedback'])) {
            \Flux::toast(__('Please select a feedback option first.'), variant: 'danger');

            return;
        }

        // TODO: persist feedback to a model/table

        $this->cases[$caseNumber]['comment'] = ''; // Clear just the comment
        \Flux::toast(__('Feedback saved. Thank you!'));
    }

    private function validatePrerequisites(): bool
    {
        if (blank($this->mainInput)) {
            \Flux::toast(__('Please enter patient summary or clinical text first.'), variant: 'danger');

            return false;
        }

        if (blank($this->clinicalStyle)) {
            \Flux::toast(__('Please select a clinical style (prompt) first.'), variant: 'danger');

            return false;
        }

        return true;
    }

    #[Computed]
    public function templateOptions(): array
    {
        return CaseTemplate::query()
            ->where('is_active', true)
            ->pluck('name', 'id')
            ->map(fn(string $name, string $id) => ['value' => $id, 'label' => $name])
            ->values()
            ->toArray();
    }

    #[Computed]
    public function styleOptions(): array
    {
        return PromptTemplate::query()
            ->where('status', true)
            ->pluck('title', 'id')
            ->map(fn(string $name, string $id) => ['value' => $id, 'label' => $name])
            ->values()
            ->toArray();
    }

    public function render(): View
    {
        return view('livewire.ai-care.index');
    }
}
