<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Support\HomeSections;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class HomeSectionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $keys = app(HomeSections::class)->keys();

        return [
            'items' => ['required', 'array', 'size:'.count($keys)],
            'items.*.id' => ['required', 'string', 'distinct', Rule::in($keys)],
            'items.*.visible' => ['required', 'boolean'],
        ];
    }
}
