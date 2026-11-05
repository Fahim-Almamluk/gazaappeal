<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Enums\VolunteerStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class VolunteerReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(VolunteerStatus::class)],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
