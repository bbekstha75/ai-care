<?php

use App\Livewire\Admin\Diagnoses;
use App\Livewire\Admin\MedicalExperiences;
use App\Livewire\Admin\Roles;
use App\Livewire\Admin\Settings\Index;
use App\Livewire\Admin\CaseTemplates;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:super_admin'])->prefix('admin')->group(function () {
    Route::get('diagnoses', Diagnoses\Index::class)->name('admin.diagnoses.index');
    Route::get('medical-experiences', MedicalExperiences\Index::class)->name('admin.medical-experiences.index');
    Route::get('roles', Roles\Index::class)->name('admin.roles.index');
    Route::get('settings', Index::class)->name('admin.settings.index');
    Route::get('case-templates', CaseTemplates\Index::class)->name('admin.case-templates.index');
});
