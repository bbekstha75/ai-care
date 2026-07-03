<?php

namespace App\Livewire\Admin\CaseTemplates;

use App\Livewire\Concerns\HasDeleteConfirmation;
use App\Livewire\Concerns\HasSearch;
use App\Models\CaseTemplate;
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
        $this->authorize('case_templates delete');

        if ($this->deleteId) {
            CaseTemplate::destroy($this->deleteId);
            $this->cancelDelete();
            
            \Flux::toast(__('Case Template deleted successfully.'));
        }
    }

    #[On('case-template-saved')]
    public function refreshList(): void {}

    #[Layout('layouts.app')]
    public function render(): View
    {
        $this->authorize('case_templates list');

        $caseTemplates = CaseTemplate::query()
            ->when($this->search, fn ($query) => $query->where('name', 'like', '%'.$this->search.'%'))
            ->latest()
            ->paginate(10);

        return view('livewire.admin.case-templates.index', [
            'caseTemplates' => $caseTemplates,
        ]);
    }
}
