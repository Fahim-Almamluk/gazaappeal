<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

final class ListSort
{
    /**
     * @param  list<string>  $columns
     */
    public static function apply(Builder $query, Request $request, array $columns): Builder
    {
        $column = $request->query('sort');

        if (! is_string($column) || ! in_array($column, $columns, true)) {
            return $query;
        }

        return $query
            ->reorder($column, $request->query('dir') === 'asc' ? 'asc' : 'desc')
            ->orderByDesc('id');
    }
}
