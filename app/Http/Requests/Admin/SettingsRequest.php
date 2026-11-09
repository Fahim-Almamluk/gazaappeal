<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Support\ImageStore;
use Illuminate\Foundation\Http\FormRequest;

final class SettingsRequest extends FormRequest
{
    public const array LOGOS = ['logo_ar', 'logo_en', 'logo_mark'];

    public function authorize(): bool
    {
        return $this->user()->can('settings.update');
    }

    protected function prepareForValidation(): void
    {
        $this->merge(collect(self::LOGOS)->mapWithKeys(fn (string $logo): array => ["remove_{$logo}" => $this->boolean("remove_{$logo}")])->all());
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
            'logo_ar' => ImageStore::rules(),
            'logo_en' => ImageStore::rules(),
            'logo_mark' => ImageStore::rules(),
            'remove_logo_ar' => ['boolean'],
            'remove_logo_en' => ['boolean'],
            'remove_logo_mark' => ['boolean'],
            'social_facebook' => $social,
            'social_instagram' => $social,
            'social_x' => $social,
            'social_tiktok' => $social,
            'social_youtube' => $social,
            'social_telegram' => $social,
        ];
    }
}
