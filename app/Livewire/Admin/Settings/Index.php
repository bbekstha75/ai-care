<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Index extends Component
{
    public bool $is_model_locked = false;
    public bool $is_setting_locked = false;
    public ?string $default_model = null;
    
    public ?Setting $setting = null;

    public function mount(): void
    {
        $this->setting = Setting::firstOrCreate([], [
            'is_model_locked' => false,
            'is_setting_locked' => false,
            'default_model' => 'GPT-3.5-TURBO',
        ]);
        
        $this->is_model_locked = $this->setting->is_model_locked;
        $this->is_setting_locked = $this->setting->is_setting_locked;
        $this->default_model = $this->setting->default_model;
    }

    protected function rules(): array
    {
        return [
            'is_model_locked' => ['boolean'],
            'is_setting_locked' => ['boolean'],
            'default_model' => ['nullable', 'string'],
        ];
    }

    public function save(): void
    {
        $this->authorize('settings update');

        $validated = $this->validate();

        $this->setting->update($validated);
        
        \Flux::toast(__('Settings updated successfully.'));
    }

    public function render(): View
    {
        $this->authorize('settings list');

        return view('livewire.admin.settings.index');
    }
}
