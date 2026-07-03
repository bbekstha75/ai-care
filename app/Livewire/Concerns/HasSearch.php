<?php

namespace App\Livewire\Concerns;

use Livewire\Attributes\Url;
use Livewire\WithPagination;

/**
 * Provides URL-synced search with automatic page reset.
 * Requires the component to also use WithPagination.
 *
 * @mixin WithPagination
 */
trait HasSearch
{
    #[Url]
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
}
