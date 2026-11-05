<?php

declare(strict_types=1);

namespace App\Http\Requests\Site;

use Illuminate\Foundation\Http\FormRequest;

final class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'email' => ['required', 'string', 'email:rfc', 'max:190'],
            'phone' => ['nullable', 'string', 'regex:/^\+?[0-9\s\-()]{7,20}$/'],
            'organization' => ['nullable', 'string', 'max:160'],
            'body' => ['required', 'string', 'min:10', 'max:3000'],
            'website' => ['prohibited'],
        ];
    }
}
