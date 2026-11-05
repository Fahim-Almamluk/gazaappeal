<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum DonationMethodType: int
{
    use HasLabel;

    private const string LABELS = 'enums.donation_method_type';

    case Link = 1;
    case BankTransfer = 2;

    public function icon(): string
    {
        return match ($this) {
            self::Link => 'external-link',
            self::BankTransfer => 'landmark',
        };
    }
}
