<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class MenuOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1', 'max:'.config('navigation.limits.roots') * config('navigation.limits.children')],
            'items.*.id' => ['required', 'integer', 'distinct', 'exists:menu_items,id'],
            'items.*.visible' => ['required', 'boolean'],
        ];
    }
}
