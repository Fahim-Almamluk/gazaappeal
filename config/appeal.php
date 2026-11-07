<?php

return [
    'admin' => [
        'name' => env('ADMIN_NAME'),
        'email' => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
    ],

    'locales' => [
        'ar' => ['dir' => 'rtl', 'og' => 'ar_AR', 'short' => 'ع'],
        'en' => ['dir' => 'ltr', 'og' => 'en_US', 'short' => 'EN'],
    ],

    'per_page' => 15,

    'uploads' => [
        'disk' => 'public',
        'max_kilobytes' => 4096,
        'mimes' => ['jpg', 'jpeg', 'png', 'webp'],
    ],

    'settings' => [
        'contact_phone' => '(+970) 8 2837521',
        'contact_whatsapp' => null,
        'contact_email' => null,
        'contact_address' => 'فلسطين، غزة، شارع عمر المختار',
        'contact_address_en' => 'Omar Al-Mukhtar Street, Gaza, Palestine',
        'hero_tagline' => 'بعطائك يعود النور',
        'hero_tagline_en' => 'Together, we bring back the light',
        'hero_subtitle' => 'مساهمتك اليوم تعيد الماء والدواء والدفء إلى أهل غزة.',
        'hero_subtitle_en' => 'What you give today brings water, care and warmth back to families in Gaza.',
        'impact_people' => '2.5',
        'impact_duration' => '12',
        'logo_ar' => null,
        'logo_en' => null,
        'logo_mark' => null,
        'social_facebook' => null,
        'social_instagram' => null,
        'social_x' => null,
        'social_tiktok' => null,
        'social_youtube' => null,
        'social_telegram' => null,
    ],
];
