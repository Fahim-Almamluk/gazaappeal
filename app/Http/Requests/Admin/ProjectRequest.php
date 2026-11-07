<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Enums\LookupType;
use App\Http\Requests\Concerns\ValidatesTranslations;
use App\Models\Lookup;
use App\Support\ImageStore;
use Illuminate\Foundation\Http\FormRequest;

final class ProjectRequest extends FormRequest
{
    use ValidatesTranslations;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_urgent' => $this->boolean('is_urgent'),
            'is_published' => $this->boolean('is_published'),
            'raised_amount' => $this->filled('raised_amount') ? $this->input('raised_amount') : 0,
        ]);
    }

    public function rules(): array
    {
        return [
            ...$this->translated('title', ['max:160']),
            ...$this->translated('summary', ['max:300']),
            ...$this->translated('body', ['max:20000']),
            'image' => ImageStore::rules(),
            'goal_amount' => ['nullable', 'numeric', 'gt:0', 'max:9999999999', 'decimal:0,2'],
            'raised_amount' => ['required', 'numeric', 'min:0', 'max:9999999999', 'decimal:0,2'],
            'status_id' => ['required', 'integer', Lookup::rule(LookupType::ProjectStatus, $this->route('project')?->status_id)],
            'is_urgent' => ['boolean'],
            'is_published' => ['boolean'],
            'sort_order' => ['required', 'integer', 'between:0,9999'],
        ];
    }
}
