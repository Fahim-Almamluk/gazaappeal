<?php

declare(strict_types=1);

namespace App\Enums\Concerns;

trait HasLabel
{
    public function label(): string
    {
        return __(self::LABELS.'.'.$this->name);
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case): array => [$case->value => $case->label()])
            ->all();
    }
}
