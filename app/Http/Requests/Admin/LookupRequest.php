<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Enums\LookupType;
use App\Http\Requests\Concerns\ValidatesTranslations;
use App\Models\Lookup;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class LookupRequest extends FormRequest
{
    use ValidatesTranslations;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'icon' => $this->filled('icon') ? $this->input('icon') : null,
            'tone' => $this->filled('tone') ? $this->input('tone') : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'type' => [$this->route('lookup') instanceof Lookup ? 'prohibited' : 'required', Rule::enum(LookupType::class)],
            ...$this->translated('name', ['max:120']),
            'icon' => ['nullable', Rule::in(Lookup::ICONS)],
            'tone' => ['nullable', Rule::in(Lookup::TONES)],
            'is_active' => ['boolean'],
            'sort_order' => ['required', 'integer', 'between:0,9999'],
        ];
    }
}
