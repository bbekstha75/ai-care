<?php

use App\Livewire\Admin\MedicalExperiences\Index;
use App\Livewire\Admin\MedicalExperiences\MedicalExperienceForm;
use App\Models\MedicalExperience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// --- Auth Guard ---

test('guests are redirected from medical experiences index', function () {
    $this->get(route('admin.medical-experiences.index'))
        ->assertRedirect(route('login'));
});

// --- Index ---

test('authenticated users can view medical experiences index', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('admin.medical-experiences.index'))
        ->assertOk();
});

test('index displays medical experiences', function () {
    $this->actingAs(User::factory()->create());

    $experience = MedicalExperience::factory()->create(['name' => 'Cardiology']);

    Livewire::test(Index::class)
        ->assertSee('Cardiology');
});

test('index supports search filtering', function () {
    $this->actingAs(User::factory()->create());

    MedicalExperience::factory()->create(['name' => 'Cardiology']);
    MedicalExperience::factory()->create(['name' => 'Dermatology']);

    Livewire::test(Index::class)
        ->set('search', 'Cardio')
        ->assertSee('Cardiology')
        ->assertDontSee('Dermatology');
});

test('index paginates results', function () {
    $this->actingAs(User::factory()->create());

    MedicalExperience::factory()->count(15)->create();

    Livewire::test(Index::class)
        ->assertViewHas('medicalExperiences', fn ($experiences) => $experiences->count() === 10);
});

// --- Create & Edit (MedicalExperienceForm) ---

test('user can create a medical experience via form', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(MedicalExperienceForm::class)
        ->call('open')
        ->set('name', 'Neurology')
        ->set('sort_order', 5)
        ->call('save')
        ->assertDispatched('medical-experience-saved');

    $this->assertDatabaseHas('medical_experiences', [
        'name' => 'Neurology',
        'sort_order' => 5,
    ]);
});

test('form validates required name', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(MedicalExperienceForm::class)
        ->call('open')
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

test('form validates unique name on create', function () {
    $this->actingAs(User::factory()->create());

    MedicalExperience::factory()->create(['name' => 'Cardiology']);

    Livewire::test(MedicalExperienceForm::class)
        ->call('open')
        ->set('name', 'Cardiology')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});

test('user can update a medical experience via form', function () {
    $this->actingAs(User::factory()->create());
    $experience = MedicalExperience::factory()->create(['name' => 'Old Name', 'sort_order' => 1]);

    Livewire::test(MedicalExperienceForm::class)
        ->call('open', $experience->id)
        ->set('name', 'New Name')
        ->set('sort_order', 10)
        ->call('save')
        ->assertDispatched('medical-experience-saved');

    $this->assertDatabaseHas('medical_experiences', [
        'id' => $experience->id,
        'name' => 'New Name',
        'sort_order' => 10,
    ]);
});

test('form validates unique name ignoring current record on edit', function () {
    $this->actingAs(User::factory()->create());

    $experience = MedicalExperience::factory()->create(['name' => 'Cardiology']);
    MedicalExperience::factory()->create(['name' => 'Dermatology']);

    Livewire::test(MedicalExperienceForm::class)
        ->call('open', $experience->id)
        ->set('name', 'Dermatology')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});

test('form allows keeping the same name on edit', function () {
    $this->actingAs(User::factory()->create());
    $experience = MedicalExperience::factory()->create(['name' => 'Cardiology']);

    Livewire::test(MedicalExperienceForm::class)
        ->call('open', $experience->id)
        ->set('name', 'Cardiology')
        ->set('sort_order', 99)
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('medical-experience-saved');
});

// --- Delete ---

test('user can delete a medical experience', function () {
    $this->actingAs(User::factory()->create());
    $experience = MedicalExperience::factory()->create();

    Livewire::test(Index::class)
        ->call('confirmDelete', $experience->id)
        ->call('delete');

    $this->assertDatabaseMissing('medical_experiences', [
        'id' => $experience->id,
    ]);
});
