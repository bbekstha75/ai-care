<?php

use App\Livewire\Admin\Diagnoses\DiagnosisForm;
use App\Livewire\Admin\Diagnoses\Index;
use App\Models\Diagnosis;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// --- Auth Guard ---

test('guests are redirected from diagnoses index', function () {
    $this->get(route('admin.diagnoses.index'))
        ->assertRedirect(route('login'));
});

test('non-admin users cannot access diagnoses index', function () {
    asUser();

    $this->get(route('admin.diagnoses.index'))
        ->assertForbidden();
});

// --- Index ---

test('authenticated admins can view diagnoses index', function () {
    asAdmin();

    $this->get(route('admin.diagnoses.index'))
        ->assertOk();
});

test('index displays diagnoses', function () {
    asAdmin();

    Diagnosis::factory()->create(['name' => 'Hypertension']);

    Livewire::test(Index::class)
        ->assertSee('Hypertension');
});

test('index supports search filtering', function () {
    asAdmin();

    Diagnosis::factory()->create(['name' => 'Hypertension']);
    Diagnosis::factory()->create(['name' => 'Diabetes']);

    Livewire::test(Index::class)
        ->set('search', 'Hyper')
        ->assertSee('Hypertension')
        ->assertDontSee('Diabetes');
});

test('index paginates results', function () {
    asAdmin();

    Diagnosis::factory()->count(15)->create();

    Livewire::test(Index::class)
        ->assertViewHas('diagnoses', fn ($diagnoses) => $diagnoses->count() === 10);
});

// --- Create & Edit (DiagnosisForm) ---

test('admin can create a diagnosis via form', function () {
    asAdmin();

    Livewire::test(DiagnosisForm::class)
        ->call('open')
        ->set('name', 'Asthma')
        ->set('symptoms', 'Shortness of breath')
        ->call('save')
        ->assertDispatched('diagnosis-saved');

    $this->assertDatabaseHas('diagnoses', [
        'name' => 'Asthma',
        'symptoms' => 'Shortness of breath',
    ]);
});

test('non-admin cannot save a diagnosis', function () {
    asUser();

    Livewire::test(DiagnosisForm::class)
        ->call('open')
        ->set('name', 'Asthma')
        ->call('save')
        ->assertForbidden();
});

test('form validates required name', function () {
    asAdmin();

    Livewire::test(DiagnosisForm::class)
        ->call('open')
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

test('form validates unique name on create', function () {
    asAdmin();

    Diagnosis::factory()->create(['name' => 'Hypertension']);

    Livewire::test(DiagnosisForm::class)
        ->call('open')
        ->set('name', 'Hypertension')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});

test('admin can update a diagnosis via form', function () {
    asAdmin();

    $diagnosis = Diagnosis::factory()->create(['name' => 'Old Name', 'symptoms' => 'Old symptoms']);

    Livewire::test(DiagnosisForm::class)
        ->call('open', $diagnosis->id)
        ->set('name', 'New Name')
        ->set('symptoms', 'New symptoms')
        ->call('save')
        ->assertDispatched('diagnosis-saved');

    $this->assertDatabaseHas('diagnoses', [
        'id' => $diagnosis->id,
        'name' => 'New Name',
        'symptoms' => 'New symptoms',
    ]);
});

test('form validates unique name ignoring current record on edit', function () {
    asAdmin();

    $diagnosis = Diagnosis::factory()->create(['name' => 'Hypertension']);
    Diagnosis::factory()->create(['name' => 'Diabetes']);

    Livewire::test(DiagnosisForm::class)
        ->call('open', $diagnosis->id)
        ->set('name', 'Diabetes')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});

test('form allows keeping the same name on edit', function () {
    asAdmin();

    $diagnosis = Diagnosis::factory()->create(['name' => 'Hypertension']);

    Livewire::test(DiagnosisForm::class)
        ->call('open', $diagnosis->id)
        ->set('name', 'Hypertension')
        ->set('symptoms', 'Updated symptoms')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('diagnosis-saved');
});

// --- Delete ---

test('admin can delete a diagnosis', function () {
    asAdmin();

    $diagnosis = Diagnosis::factory()->create();

    Livewire::test(Index::class)
        ->call('confirmDelete', $diagnosis->id)
        ->call('delete');

    $this->assertDatabaseMissing('diagnoses', [
        'id' => $diagnosis->id,
    ]);
});

test('non-admin cannot delete a diagnosis', function () {
    asUser();

    $diagnosis = Diagnosis::factory()->create();

    Livewire::test(Index::class)
        ->call('confirmDelete', $diagnosis->id)
        ->call('delete')
        ->assertForbidden();
});
