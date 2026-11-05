<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns;

use Illuminate\Support\Str;

trait ValidatesTranslations
{
    /**
     * @param  list<mixed>  $rules
     * @return array<string, list<mixed>>
     */
    protected function translated(string $field, array $rules, bool $required = true): array
    {
        $locales = array_keys(config('appeal.locales'));
        $source = config('app.fallback_locale');

        return [
            $field => [$required ? 'required' : 'nullable', 'array:'.implode(',', $locales)],
            ...collect($locales)->mapWithKeys(fn (string $locale): array => [
                "{$field}.{$locale}" => [$required && $locale === $source ? 'required' : 'nullable', 'string', ...$rules],
            ])->all(),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $locales = array_keys(config('appeal.locales'));

        return collect(array_keys($this->rules()))
            ->filter(fn (string $key): bool => Str::contains($key, '.') && in_array(Str::afterLast($key, '.'), $locales, true))
            ->mapWithKeys(fn (string $key): array => [
                $key => __('validation.attributes.'.Str::beforeLast($key, '.')).' ('.__('ui.language.names.'.Str::afterLast($key, '.')).')',
            ])
            ->all();
    }
}
