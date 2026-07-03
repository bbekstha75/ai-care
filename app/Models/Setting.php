<?php

namespace App\Models;

use Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /** @use HasFactory<SettingFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'is_model_locked',
        'is_setting_locked',
        'default_model',
    ];

    protected function casts(): array
    {
        return [
            'is_model_locked' => 'boolean',
            'is_setting_locked' => 'boolean',
        ];
    }
}
