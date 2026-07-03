<?php

use App\Livewire\Admin\MedicalExperiences\Index;
use App\Livewire\Admin\MedicalExperiences\MedicalExperienceForm;
use App\Models\MedicalExperience;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// --- Auth Guard ---

test('guests are redirected from medical experiences index', function () {
    $this->get(route('admin.medical-experiences.index'))
        ->assertRedirect(route('login'));
});

test('non-admin users cannot access medical experiences index', function () {
    asUser();

    $this->get(route('admin.medical-experiences.index'))
        ->assertForbidden();
});

// --- Index ---

test('authenticated admins can view medical experiences index', function () {
    asAdmin();

    $this->get(route('admin.medical-experiences.index'))
        ->assertOk();
});

test('index displays medical experiences', function () {
    asAdmin();

    MedicalExperience::factory()->create(['name' => 'Cardiology']);

    Livewire::test(Index::class)
        ->assertSee('Cardiology');
});

test('index supports search filtering', function () {
    asAdmin();

    MedicalExperience::factory()->create(['name' => 'Cardiology']);
    MedicalExperience::factory()->create(['name' => 'Dermatology']);

    Livewire::test(Index::class)
        ->set('search', 'Cardio')
        ->assertSee('Cardiology')
        ->assertDontSee('Dermatology');
});

test('index paginates results', function () {
    asAdmin();

    MedicalExperience::factory()->count(15)->create();

    Livewire::test(Index::class)
        ->assertViewHas('medicalExperiences', fn ($experiences) => $experiences->count() === 10);
});

// --- Create & Edit (MedicalExperienceForm) ---

test('admin can create a medical experience via form', function () {
    asAdmin();

    Livewire::test(MedicalExperienceForm::class)
        ->call('open')
        ->set('name', 'Neurology')
        ->set('sortOrder', 5)
        ->call('save')
        ->assertDispatched('medical-experience-saved');

    $this->assertDatabaseHas('medical_experiences', [
        'name' => 'Neurology',
        'sort_order' => 5,
    ]);
});

test('non-admin cannot save a medical experience', function () {
    asUser();

    Livewire::test(MedicalExperienceForm::class)
        ->call('open')
        ->set('name', 'Neurology')
        ->call('save')
        ->assertForbidden();
});

test('form validates required name', function () {
    asAdmin();

    Livewire::test(MedicalExperienceForm::class)
        ->call('open')
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

test('form validates unique name on create', function () {
    asAdmin();

    MedicalExperience::factory()->create(['name' => 'Cardiology']);

    Livewire::test(MedicalExperienceForm::class)
        ->call('open')
        ->set('name', 'Cardiology')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});

test('admin can update a medical experience via form', function () {
    asAdmin();

    $experience = MedicalExperience::factory()->create(['name' => 'Old Name', 'sort_order' => 1]);

    Livewire::test(MedicalExperienceForm::class)
        ->call('open', $experience->id)
        ->set('name', 'New Name')
        ->set('sortOrder', 10)
        ->call('save')
        ->assertDispatched('medical-experience-saved');

    $this->assertDatabaseHas('medical_experiences', [
        'id' => $experience->id,
        'name' => 'New Name',
        'sort_order' => 10,
    ]);
});

test('form validates unique name ignoring current record on edit', function () {
    asAdmin();

    $experience = MedicalExperience::factory()->create(['name' => 'Cardiology']);
    MedicalExperience::factory()->create(['name' => 'Dermatology']);

    Livewire::test(MedicalExperienceForm::class)
        ->call('open', $experience->id)
        ->set('name', 'Dermatology')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});

test('form allows keeping the same name on edit', function () {
    asAdmin();

    $experience = MedicalExperience::factory()->create(['name' => 'Cardiology']);

    Livewire::test(MedicalExperienceForm::class)
        ->call('open', $experience->id)
        ->set('name', 'Cardiology')
        ->set('sortOrder', 99)
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('medical-experience-saved');
});

// --- Delete ---

test('admin can delete a medical experience', function () {
    asAdmin();

    $experience = MedicalExperience::factory()->create();

    Livewire::test(Index::class)
        ->call('confirmDelete', $experience->id)
        ->call('delete');

    $this->assertDatabaseMissing('medical_experiences', [
        'id' => $experience->id,
    ]);
});

test('non-admin cannot delete a medical experience', function () {
    asUser();

    $experience = MedicalExperience::factory()->create();

    Livewire::test(Index::class)
        ->call('confirmDelete', $experience->id)
        ->call('delete')
        ->assertForbidden();
});
