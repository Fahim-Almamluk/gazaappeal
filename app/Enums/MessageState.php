<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum MessageState: string
{
    use HasLabel;

    private const string LABELS = 'enums.message_state';

    case Unread = 'unread';
    case Read = 'read';
    case Archived = 'archived';

    public function tone(): string
    {
        return match ($this) {
            self::Unread => 'info',
            self::Read => 'neutral',
            self::Archived => 'warning',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Unread => 'mail',
            self::Read => 'mail-open',
            self::Archived => 'archive',
        };
    }
}
