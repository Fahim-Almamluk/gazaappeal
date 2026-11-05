<?php

declare(strict_types=1);

namespace App\Support;

final class Money
{
    private const array SCALES = [1_000_000_000 => 'B', 1_000_000 => 'M', 1_000 => 'K'];

    public static function short(int $amount): string
    {
        foreach (self::SCALES as $size => $suffix) {
            if ($amount >= $size) {
                return '$'.rtrim(rtrim(number_format($amount / $size, 1), '0'), '.').$suffix;
            }
        }

        return '$'.number_format($amount);
    }

    public static function full(float|int|string|null $amount): string
    {
        return __('ui.money', ['amount' => number_format((float) $amount)]);
    }
}
