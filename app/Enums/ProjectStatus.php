<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum ProjectStatus: int
{
    use HasLabel;

    private const string LABELS = 'enums.project_status';

    case Planned = 1;
    case Active = 2;
    case Completed = 3;

    public function tone(): string
    {
        return match ($this) {
            self::Planned => 'neutral',
            self::Active => 'info',
            self::Completed => 'success',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Planned => 'clock',
            self::Active => 'zap',
            self::Completed => 'circle-check',
        };
    }
}
