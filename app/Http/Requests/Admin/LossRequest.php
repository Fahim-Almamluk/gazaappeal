<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Enums\LossIcon;
use App\Http\Requests\Concerns\ValidatesTranslations;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class LossRequest extends FormRequest
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
            ...$this->translated('title', ['max:160']),
            'figure' => ['required', 'numeric', 'min:0', 'max:999999999999', 'decimal:0,2'],
            ...$this->translated('unit', ['max:40']),
            ...$this->translated('description', ['max:1000']),
            'icon' => ['required', Rule::enum(LossIcon::class)],
            'is_published' => ['boolean'],
            'sort_order' => ['required', 'integer', 'between:0,9999'],
        ];
    }
}
