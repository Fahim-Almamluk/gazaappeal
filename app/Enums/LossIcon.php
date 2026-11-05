<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum LossIcon: string
{
    use HasLabel;

    private const string LABELS = 'enums.loss_icon';

    case Power = 'zap-off';
    case Network = 'utility-pole';
    case Cables = 'cable';
    case Stations = 'factory';
    case Buildings = 'building-2';
    case Vehicles = 'truck';
    case People = 'users';
    case Money = 'banknote';
}
