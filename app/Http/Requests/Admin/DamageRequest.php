<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Enums\DamageCategory;
use App\Enums\Governorate;
use App\Http\Requests\Concerns\ValidatesTranslations;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class DamageRequest extends FormRequest
{
    use ValidatesTranslations;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'governorate' => ['required', Rule::enum(Governorate::class)],
            'category' => ['required', Rule::enum(DamageCategory::class)],
            ...$this->translated('item', ['max:120']),
            'amount' => ['required', 'integer', 'min:0', 'max:999999999999'],
            'sort_order' => ['required', 'integer', 'between:0,9999'],
        ];
    }
}
