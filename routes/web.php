<?php

use App\Http\Controllers\Api\AssemblyAiTokenController;
use App\Livewire\CaseNotes\Index;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('case-notes', Index::class)->name('case-notes.index');
    Route::get('transcriptions', App\Livewire\Transcriptions\Index::class)->name('transcriptions.index');
    Route::get('ai-care', App\Livewire\AiCare\Index::class)->name('ai-care.index');
    Route::get('api/assemblyai/token', AssemblyAiTokenController::class)->name('api.assemblyai.token');
});

require __DIR__.'/settings.php';
require __DIR__.'/admin.php';
