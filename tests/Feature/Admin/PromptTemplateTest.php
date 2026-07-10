<?php

use App\Livewire\Admin\PromptTemplates\Index;
use App\Livewire\Admin\PromptTemplates\PromptTemplateForm;
use App\Models\PromptTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

uses(RefreshDatabase::class);

it('redirects guests to login', function () {
    $this->get(route('admin.prompt-templates.index'))->assertRedirect(route('login'));
});

it('forbids non-admins from accessing index', function () {
    asUser();
    $this->get(route('admin.prompt-templates.index'))->assertForbidden();
});

it('displays prompt templates on index page', function () {
    asAdmin();
    $template = PromptTemplate::create([
        'title' => 'Test Prompt',
        'system_prompt' => 'Be helpful',
    ]);

    Livewire::test(Index::class)
        ->assertSee('Test Prompt');
});

it('filters prompt templates via search', function () {
    asAdmin();
    PromptTemplate::create(['title' => 'Match Prompt', 'system_prompt' => 'Sys1']);
    PromptTemplate::create(['title' => 'Hidden Prompt', 'system_prompt' => 'Sys2']);

    Livewire::test(Index::class)
        ->set('search', 'Match')
        ->assertSee('Match Prompt')
        ->assertDontSee('Hidden Prompt');
});

it('can create a prompt template', function () {
    asAdmin();

    Livewire::test(PromptTemplateForm::class)
        ->call('create')
        ->set('title', 'New Prompt')
        ->set('description', 'A new prompt')
        ->set('systemPrompt', 'You are an AI')
        ->set('stylingInstructions', 'Use markdown')
        ->set('status', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('prompt-template-saved');

    assertDatabaseHas('prompt_templates', [
        'title' => 'New Prompt',
        'system_prompt' => 'You are an AI',
    ]);
});

it('requires title and system prompt', function () {
    asAdmin();

    Livewire::test(PromptTemplateForm::class)
        ->call('create')
        ->call('save')
        ->assertHasErrors(['title' => 'required', 'systemPrompt' => 'required']);
});

it('can update a prompt template', function () {
    asAdmin();
    $template = PromptTemplate::create([
        'title' => 'Old Title',
        'system_prompt' => 'Old Prompt',
    ]);

    Livewire::test(PromptTemplateForm::class)
        ->call('edit', $template->id)
        ->set('title', 'Updated Title')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('prompt-template-saved');

    assertDatabaseHas('prompt_templates', [
        'id' => $template->id,
        'title' => 'Updated Title',
    ]);
});

it('can delete a prompt template', function () {
    asAdmin();
    $template = PromptTemplate::create([
        'title' => 'To be deleted',
        'system_prompt' => 'Delete me',
    ]);

    Livewire::test(Index::class)
        ->call('confirmDelete', $template->id)
        ->call('delete');

    assertDatabaseMissing('prompt_templates', [
        'id' => $template->id,
    ]);
});

it('forbids non-admins from saving prompt templates', function () {
    asUser();

    Livewire::test(PromptTemplateForm::class)
        ->call('save')
        ->assertForbidden();
});

it('forbids non-admins from deleting prompt templates', function () {
    asUser();
    $template = PromptTemplate::factory()->create();

    Livewire::test(Index::class)
        ->call('confirmDelete', $template->id)
        ->call('delete')
        ->assertForbidden();
});
