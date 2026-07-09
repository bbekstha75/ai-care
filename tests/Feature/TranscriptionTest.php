<?php

use App\Livewire\Transcriptions\Index;
use App\Livewire\Transcriptions\TranscriptionForm;
use App\Models\CaseNote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('renders the transcriptions index page', function () {
    $this->actingAs($this->user)
        ->get(route('transcriptions.index'))
        ->assertOk()
        ->assertSeeLivewire(Index::class);
});

it('shows only transcriptions with titles', function () {
    CaseNote::factory()->create(['user_id' => $this->user->id, 'title' => null]);
    CaseNote::factory()->withTitle()->create(['user_id' => $this->user->id]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->assertCount('transcriptions', 1);
});

it('can create a transcription', function () {
    Livewire::actingAs($this->user)
        ->test(TranscriptionForm::class)
        ->call('open')
        ->set('title', 'Test Transcription')
        ->set('output', '<p>Test Output</p>')
        ->call('save')
        ->assertDispatched('transcription-saved');

    $this->assertDatabaseHas('case_notes', [
        'user_id' => $this->user->id,
        'title' => 'Test Transcription',
        'output' => '<p>Test Output</p>',
    ]);
});

it('can update a transcription', function () {
    $note = CaseNote::factory()->withTitle()->create(['user_id' => $this->user->id, 'title' => 'Old Title']);

    Livewire::actingAs($this->user)
        ->test(TranscriptionForm::class)
        ->call('open', $note->id)
        ->set('title', 'New Title')
        ->call('save');

    $this->assertDatabaseHas('case_notes', [
        'id' => $note->id,
        'title' => 'New Title',
    ]);
});

it('can delete a transcription', function () {
    $note = CaseNote::factory()->withTitle()->create(['user_id' => $this->user->id]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('confirmDelete', $note->id)
        ->call('delete');

    $this->assertDatabaseMissing('case_notes', [
        'id' => $note->id,
    ]);
});
