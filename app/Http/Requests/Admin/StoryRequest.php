<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\ValidatesTranslations;
use App\Support\ImageStore;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoryRequest extends FormRequest
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
            'project_id' => ['nullable', 'integer', Rule::exists('projects', 'id')->withoutTrashed()],
            ...$this->translated('title', ['max:160']),
            ...$this->translated('summary', ['max:300']),
            ...$this->translated('body', ['max:20000']),
            'image' => ImageStore::rules(),
            'is_published' => ['boolean'],
        ];
    }
}
