<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GenerationHistory extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function promptTemplate()
    {
        return $this->belongsTo(PromptTemplate::class);
    }
}
