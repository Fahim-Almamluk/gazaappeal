<?php

namespace Database\Seeders\Concerns;

use Illuminate\Database\Eloquent\Model;

trait SeedsTranslations
{
    /**
     * @template TModel of Model
     *
     * @param  class-string<TModel>  $model
     * @param  array{0: string, 1: array<string, string>}  $key
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $match
     * @return TModel
     */
    private function seedTranslated(string $model, array $key, array $attributes, array $match = [], bool $overwrite = false): Model
    {
        [$field, $translations] = $key;

        $record = $model::query()->where($match)->whereTranslation($field, $translations['ar'])->first() ?? new $model($match);
        $attributes = [$field => $translations, ...$attributes];

        foreach ($attributes as $name => $value) {
            if (! $record->exists) {
                $record->{$name} = $value;
            } elseif ($record->isTranslatable($name) && is_array($value)) {
                $record->{$name} = [...$value, ...$record->translations($name)];
            } elseif ($overwrite) {
                $record->{$name} = $value;
            }
        }

        $record->save();

        return $record;
    }
}
