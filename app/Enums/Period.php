<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasLabel;
use Illuminate\Support\Carbon;

enum Period: int
{
    use HasLabel;

    private const string LABELS = 'enums.period';

    case Week = 7;
    case Month = 30;
    case Quarter = 90;

    public function since(): Carbon
    {
        return now()->subDays($this->value)->startOfDay();
    }
}
