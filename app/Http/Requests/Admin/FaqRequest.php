<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\ValidatesTranslations;
use Illuminate\Foundation\Http\FormRequest;

final class FaqRequest extends FormRequest
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
            ...$this->translated('question', ['max:255']),
            ...$this->translated('answer', ['max:5000']),
            'is_published' => ['boolean'],
            'sort_order' => ['required', 'integer', 'between:0,9999'],
        ];
    }
}
