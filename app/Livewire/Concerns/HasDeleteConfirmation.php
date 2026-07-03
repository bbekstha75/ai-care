<?php

namespace App\Livewire\Concerns;

/**
 * Provides delete confirmation flow for Admin CRUD Index components.
 * The implementing component must define a delete() method with the
 * actual deletion logic.
 */
trait HasDeleteConfirmation
{
    public ?string $deleteId = null;

    public function confirmDelete(string $id): void
    {
        $this->deleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->deleteId = null;
    }
}
