<?php

declare(strict_types=1);

namespace App\Http\Requests\Site;

use App\Enums\LookupType;
use App\Models\Lookup;
use Illuminate\Foundation\Http\FormRequest;

final class VolunteerRequest extends FormRequest
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
            'phone' => ['required', 'string', 'regex:/^\+?[0-9\s\-()]{7,20}$/'],
            'country' => ['required', 'string', 'max:80'],
            'expertise_id' => ['required', 'integer', Lookup::rule(LookupType::VolunteerExpertise)],
            'experience_years' => ['required', 'integer', 'between:0,60'],
            'message' => ['required', 'string', 'min:20', 'max:3000'],
            'website' => ['prohibited'],
        ];
    }
}
