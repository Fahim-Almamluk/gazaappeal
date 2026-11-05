<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Support\PageContent;
use Illuminate\Foundation\Http\FormRequest;

final class PageContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('pages.update');
    }

    public function rules(): array
    {
        $pages = app(PageContent::class);
        $rules = [];

        foreach ($pages->fields() as $name => $field) {
            foreach ($pages->locales() as $locale) {
                $rules[$pages->column($name, $locale)] = ['nullable', 'string', 'max:'.$field['max']];
            }
        }

        return $rules;
    }

    public function attributes(): array
    {
        $pages = app(PageContent::class);
        $attributes = [];

        foreach ($pages->fields() as $name => $field) {
            foreach ($pages->locales() as $locale) {
                $attributes[$pages->column($name, $locale)] = $pages->label($field);
            }
        }

        return $attributes;
    }
}
