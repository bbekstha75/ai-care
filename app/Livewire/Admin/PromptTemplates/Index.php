<?php

namespace App\Livewire\Admin\PromptTemplates;

use App\Livewire\Concerns\HasDeleteConfirmation;
use App\Livewire\Concerns\HasSearch;
use App\Models\PromptTemplate;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use HasDeleteConfirmation, HasSearch, WithPagination;

    public function delete(): void
    {
        $this->authorize('prompt_templates delete');

        if ($this->deleteId) {
            PromptTemplate::destroy($this->deleteId);
            $this->cancelDelete();

            \Flux::toast(__('Prompt Template deleted successfully.'));
        }
    }

    #[On('prompt-template-saved')]
    public function refreshList(): void {}

    #[Layout('layouts.app')]
    public function render(): View
    {
        $promptTemplates = PromptTemplate::query()
            ->when($this->search, fn ($query) => $query->where('title', 'like', '%'.$this->search.'%'))
            ->latest()
            ->paginate(10);

        return view('livewire.admin.prompt-templates.index', [
            'promptTemplates' => $promptTemplates,
        ]);
    }
}
