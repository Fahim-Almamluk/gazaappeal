<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum LookupType: string
{
    use HasLabel;

    private const string LABELS = 'enums.lookup_type';

    case ProjectStatus = 'project_status';
    case DamageCategory = 'damage_category';
    case VolunteerExpertise = 'volunteer_expertise';

    public function icon(): string
    {
        return match ($this) {
            self::ProjectStatus => 'solar-panel',
            self::DamageCategory => 'map-pin',
            self::VolunteerExpertise => 'user-plus',
        };
    }

    public function usage(): string
    {
        return match ($this) {
            self::ProjectStatus => 'projects',
            self::DamageCategory => 'damages',
            self::VolunteerExpertise => 'volunteers',
        };
    }
}
