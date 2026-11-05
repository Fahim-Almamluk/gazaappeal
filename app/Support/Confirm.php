<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\HtmlString;

final class Confirm
{
    public static function attributes(string $tone, string $key, array $replace = []): HtmlString
    {
        $attributes = [
            'data-confirm' => '1',
            'data-confirm-tone' => $tone,
            'data-confirm-title' => __("ui.confirm.{$key}.title", $replace),
            'data-confirm-text' => __("ui.confirm.{$key}.text", $replace),
            'data-confirm-ok' => __("ui.confirm.{$key}.ok", $replace),
            'data-confirm-cancel' => __('ui.confirm.cancel'),
        ];

        return new HtmlString(
            collect($attributes)
                ->map(fn (string $value, string $name): string => $name.'="'.e($value).'"')
                ->implode(' '),
        );
    }
}
