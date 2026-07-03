<?php

use App\Livewire\Admin\Settings\Index;
use App\Models\Setting;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('displays settings on the index page', function () {
    $this->actingAs(asAdmin());
    $this->get(route('admin.settings.index'))->assertStatus(200);
});

it('allows super admin to access settings index', function () {
    $this->actingAs(asAdmin());
    $this->get(route('admin.settings.index'))->assertStatus(200);
});

it('denies access to settings index for users without permission', function () {
    $this->actingAs(asUser());
    $this->get(route('admin.settings.index'))->assertStatus(403);
});

it('admin can update settings', function () {
    $setting = Setting::factory()->create();

    $this->actingAs(asAdmin());
    Livewire::test(Index::class)
        ->set('is_model_locked', true)
        ->set('is_setting_locked', true)
        ->set('default_model', 'GPT-5')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('settings', [
        'id' => $setting->id,
        'is_model_locked' => 1,
        'is_setting_locked' => 1,
        'default_model' => 'GPT-5',
    ]);
});

it('creates setting row automatically if none exists when mounted', function () {
    $this->actingAs(asAdmin());
    
    $this->assertDatabaseCount('settings', 0);
    
    Livewire::test(Index::class);
    
    $this->assertDatabaseCount('settings', 1);
});
