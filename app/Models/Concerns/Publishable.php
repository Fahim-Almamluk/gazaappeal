<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait Publishable
{
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('is_published', true);
    }

    #[Scope]
    protected function visibility(Builder $query, ?string $visibility): void
    {
        match ($visibility) {
            'published' => $query->where('is_published', true),
            'hidden' => $query->where('is_published', false),
            default => null,
        };
    }
}
