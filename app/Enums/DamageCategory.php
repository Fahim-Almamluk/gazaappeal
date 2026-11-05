<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum DamageCategory: string
{
    use HasLabel;

    private const string LABELS = 'enums.damage_category';

    case Networks = 'networks';
    case Buildings = 'buildings';
    case Vehicles = 'vehicles';
    case Operations = 'operations';

    public function icon(): string
    {
        return match ($this) {
            self::Networks => 'utility-pole',
            self::Buildings => 'building-2',
            self::Vehicles => 'truck',
            self::Operations => 'wallet',
        };
    }
}
