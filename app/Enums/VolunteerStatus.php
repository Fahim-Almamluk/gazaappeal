<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum VolunteerStatus: int
{
    use HasLabel;

    private const string LABELS = 'enums.volunteer_status';

    case New = 1;
    case Contacted = 2;
    case Accepted = 3;
    case Declined = 4;

    public function tone(): string
    {
        return match ($this) {
            self::New => 'info',
            self::Contacted => 'warning',
            self::Accepted => 'success',
            self::Declined => 'neutral',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::New => 'sparkles',
            self::Contacted => 'phone',
            self::Accepted => 'circle-check',
            self::Declined => 'ban',
        };
    }
}
