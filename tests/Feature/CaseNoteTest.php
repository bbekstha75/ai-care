<?php

use App\Livewire\CaseNotes\CaseNoteForm;
use App\Livewire\CaseNotes\Index;
use App\Models\CaseNote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('renders the case notes index page', function () {
    $this->actingAs($this->user)
        ->get(route('case-notes.index'))
        ->assertOk()
        ->assertSeeLivewire(Index::class);
});

it('shows only case notes without titles', function () {
    CaseNote::factory()->create(['user_id' => $this->user->id, 'title' => null]);
    CaseNote::factory()->withTitle()->create(['user_id' => $this->user->id]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->assertCount('caseNotes', 1);
});

it('can switch tabs', function () {
    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->set('tab', 'results')
        ->assertSee('Case Results')
        ->set('tab', 'input')
        ->assertSee('Case Input');
});

it('can create a case note output', function () {
    Livewire::actingAs($this->user)
        ->test(CaseNoteForm::class)
        ->call('open')
        ->set('output', '<p>Test Output</p>')
        ->call('save')
        ->assertDispatched('case-note-saved');

    $this->assertDatabaseHas('case_notes', [
        'user_id' => $this->user->id,
        'output' => '<p>Test Output</p>',
        'title' => null,
    ]);
});

it('can update a case note output', function () {
    $note = CaseNote::factory()->create(['user_id' => $this->user->id, 'output' => 'Old Output']);

    Livewire::actingAs($this->user)
        ->test(CaseNoteForm::class)
        ->call('open', $note->id)
        ->set('output', 'New Output')
        ->call('save');

    $this->assertDatabaseHas('case_notes', [
        'id' => $note->id,
        'output' => 'New Output',
    ]);
});

it('can delete a case note', function () {
    $note = CaseNote::factory()->create(['user_id' => $this->user->id]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('confirmDelete', $note->id)
        ->call('delete');

    $this->assertDatabaseMissing('case_notes', [
        'id' => $note->id,
    ]);
});
