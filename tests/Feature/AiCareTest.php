<?php

use App\Livewire\AiCare\Index;
use App\Models\CaseTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('renders the ai care page', function () {
    $this->actingAs($this->user)
        ->get(route('ai-care.index'))
        ->assertOk()
        ->assertSeeLivewire(Index::class);
});

it('requires authentication', function () {
    $this->get(route('ai-care.index'))
        ->assertRedirect(route('login'));
});

it('displays the header description', function () {
    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->assertSee('Docspe AICare')
        ->assertSee('AI co-pilot');
});

it('can reset the form', function () {
    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->set('mainInput', 'Some patient summary')
        ->set('tagHistory', 'history_physical')
        ->call('resetForm')
        ->assertSet('mainInput', '')
        ->assertSet('tagHistory', '');
});

it('does not generate a case without input', function () {
    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->assertSet('mainInput', '')
        ->call('generateCase')
        ->assertSet('caseOneResult', '');
});

it('does not generate a second case without input', function () {
    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->assertSet('mainInput', '')
        ->call('generateSecondCase')
        ->assertSet('caseTwoResult', '');
});

it('can save a scribe with input', function () {
    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->set('mainInput', 'Patient presented with chest pain')
        ->call('saveScribe');

    $this->assertDatabaseHas('case_notes', [
        'user_id' => $this->user->id,
        'input' => 'Patient presented with chest pain',
    ]);
});

it('does not save a scribe without input', function () {
    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('saveScribe');

    $this->assertDatabaseCount('case_notes', 0);
});

it('can save a visit casenote for case 1', function () {
    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->set('mainInput', 'Patient input text')
        ->set('caseOneResult', 'Generated case result')
        ->call('saveVisitCasenote', 1);

    $this->assertDatabaseHas('case_notes', [
        'user_id' => $this->user->id,
        'input' => 'Patient input text',
        'output' => 'Generated case result',
    ]);
});

it('loads active case templates into the template dropdown', function () {
    CaseTemplate::factory()->create(['name' => 'SOAP Template', 'is_active' => true]);
    CaseTemplate::factory()->create(['name' => 'Inactive Template', 'is_active' => false]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->assertSee('SOAP Template')
        ->assertDontSee('Inactive Template');
});

it('can submit accuracy feedback', function () {
    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('submitFeedback', 1, 'up')
        ->assertSet('caseOneFeedback', 'up')
        ->call('submitFeedback', 1, 'down')
        ->assertSet('caseOneFeedback', 'down');
});

it('can save a feedback comment after voting', function () {
    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('submitFeedback', 1, 'up')
        ->set('caseOneComment', 'Very accurate diagnosis')
        ->call('saveFeedbackComment', 1)
        ->assertSet('caseOneComment', '');
});

it('resets feedback fields on form reset', function () {
    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('submitFeedback', 1, 'up')
        ->set('caseOneComment', 'Some comment')
        ->call('resetForm')
        ->assertSet('caseOneFeedback', null)
        ->assertSet('caseOneComment', '');
});
