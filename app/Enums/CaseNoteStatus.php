<?php

namespace App\Enums;

enum CaseNoteStatus: string
{
    case Draft = 'draft';
    case Saved = 'saved';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Saved => 'Saved',
            self::Archived => 'Archived',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'zinc',
            self::Saved => 'green',
            self::Archived => 'amber',
        };
    }
}
