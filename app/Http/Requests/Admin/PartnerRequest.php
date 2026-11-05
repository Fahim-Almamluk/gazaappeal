<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\ValidatesTranslations;
use App\Support\ImageStore;
use Illuminate\Foundation\Http\FormRequest;

final class PartnerRequest extends FormRequest
{
    use ValidatesTranslations;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_published' => $this->boolean('is_published')]);
    }

    public function rules(): array
    {
        return [
            ...$this->translated('name', ['max:120']),
            'logo' => ImageStore::rules(),
            'website' => ['nullable', 'url:https', 'max:255'],
            'is_published' => ['boolean'],
            'sort_order' => ['required', 'integer', 'between:0,9999'],
        ];
    }
}
