<?php

namespace Database\Seeders\Concerns;

use App\Enums\LookupType;
use App\Models\Lookup;

trait SeedsLookups
{
    private function lookup(LookupType $type, string $name): int
    {
        return Lookup::query()->ofType($type)->whereTranslation('name', $name)->value('id')
            ?? Lookup::query()->ofType($type)->ordered()->value('id');
    }
}
