<?php

use App\Livewire\Admin\CaseTemplates\CaseTemplateForm;
use App\Livewire\Admin\CaseTemplates\Index;
use App\Models\CaseTemplate;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('displays case templates index page', function () {
    $this->actingAs(asAdmin());
    $this->get(route('admin.case-templates.index'))->assertStatus(200);
});

it('denies access to case templates index for unauthorized users', function () {
    $this->actingAs(asUser());
    $this->get(route('admin.case-templates.index'))->assertStatus(403);
});

it('can create a case template', function () {
    $this->actingAs(asAdmin());

    Livewire::test(CaseTemplateForm::class)
        ->call('open')
        ->set('name', 'New Template')
        ->set('description', '<p>Test description</p>')
        ->set('is_active', true)
        ->call('save')
        ->assertDispatched('case-template-saved');

    $this->assertDatabaseHas('case_templates', [
        'name' => 'New Template',
        'is_active' => true,
    ]);
});

it('can update a case template', function () {
    $this->actingAs(asAdmin());
    $template = CaseTemplate::factory()->create();

    Livewire::test(CaseTemplateForm::class)
        ->call('open', $template->id)
        ->set('name', 'Updated Template')
        ->call('save')
        ->assertDispatched('case-template-saved');

    $this->assertDatabaseHas('case_templates', [
        'id' => $template->id,
        'name' => 'Updated Template',
    ]);
});

it('can delete a case template', function () {
    $this->actingAs(asAdmin());
    $template = CaseTemplate::factory()->create();

    Livewire::test(Index::class)
        ->call('confirmDelete', $template->id)
        ->call('delete');

    $this->assertDatabaseMissing('case_templates', [
        'id' => $template->id,
    ]);
});
