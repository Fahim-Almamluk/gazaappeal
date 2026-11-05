<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum VolunteerExpertise: int
{
    use HasLabel;

    private const string LABELS = 'enums.volunteer_expertise';

    case PowerInfrastructure = 1;
    case ReliefProjects = 2;
    case Reconstruction = 3;
    case RenewableEnergy = 4;
    case Other = 5;

    public function icon(): string
    {
        return match ($this) {
            self::PowerInfrastructure => 'utility-pole',
            self::ReliefProjects => 'heart-handshake',
            self::Reconstruction => 'wrench',
            self::RenewableEnergy => 'solar-panel',
            self::Other => 'lightbulb',
        };
    }
}
