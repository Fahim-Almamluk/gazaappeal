<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class SettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('settings.update');
    }

    public function rules(): array
    {
        $phone = ['nullable', 'string', 'regex:/^\+?[0-9\s\-()]{7,20}$/'];
        $social = ['nullable', 'url:https', 'max:255'];

        return [
            'contact_phone' => $phone,
            'contact_whatsapp' => $phone,
            'contact_email' => ['nullable', 'email:rfc', 'max:190'],
            'contact_address' => ['nullable', 'string', 'max:190'],
            'contact_address_en' => ['nullable', 'string', 'max:190'],
            'hero_tagline' => ['required', 'string', 'max:60'],
            'hero_tagline_en' => ['nullable', 'string', 'max:60'],
            'hero_subtitle' => ['required', 'string', 'max:90'],
            'hero_subtitle_en' => ['nullable', 'string', 'max:90'],
            'impact_people' => ['required', 'numeric', 'min:0', 'max:1000', 'decimal:0,2'],
            'impact_duration' => ['required', 'integer', 'min:0', 'max:1000'],
            'social_facebook' => $social,
            'social_instagram' => $social,
            'social_x' => $social,
            'social_tiktok' => $social,
            'social_youtube' => $social,
            'social_telegram' => $social,
        ];
    }
}
