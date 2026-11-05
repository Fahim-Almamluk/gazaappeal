<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\ValidatesTranslations;
use App\Support\ImageStore;
use Illuminate\Foundation\Http\FormRequest;

final class GalleryItemRequest extends FormRequest
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
            ...$this->translated('caption', ['max:160']),
            'image' => ImageStore::rules(required: $this->isMethod('post')),
            'is_published' => ['boolean'],
            'sort_order' => ['required', 'integer', 'between:0,9999'],
        ];
    }
}
