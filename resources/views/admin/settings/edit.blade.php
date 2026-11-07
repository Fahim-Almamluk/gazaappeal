@php
    $value = fn (string $key): ?string => old($key, $settings[$key] ?? null);
    $socials = ['facebook', 'instagram', 'x', 'tiktok', 'youtube', 'telegram'];
    $logos = [
        'logo_ar' => asset('images/brand/logo-ar.webp'),
        'logo_en' => asset('images/brand/logo-en.webp'),
        'logo_mark' => asset('images/brand/mark.png'),
    ];
@endphp

<x-layouts.admin :title="__('admin.settings.title')">
    <div class="mx-auto max-w-6xl space-y-6">
        <x-ui.page-header icon="settings" :title="__('admin.settings.title')" :subtitle="__('admin.settings.subtitle')" />

        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-6" novalidate>
            @csrf
            @method('PUT')

            <x-ui.form-section step="1" :title="__('admin.settings.sections.brand')" :description="__('admin.settings.sections.brand_hint')">
                @foreach ($logos as $field => $default)
                    @php($custom = $settings[$field] ?? null)
                    <div class="space-y-3 sm:col-span-2">
                        <x-ui.image-input :name="$field" :label="__('validation.attributes.'.$field)" :hint="__('admin.settings.logo_hints.'.$field)" :current="$custom ? asset('storage/'.$custom) : $default" :contain="true" />
                        @if ($custom)
                            <label class="flex w-fit cursor-pointer items-center gap-2 text-sm text-ink-muted">
                                <input type="checkbox" name="remove_{{ $field }}" value="1" class="size-4 rounded border-line-strong accent-[var(--primary)]" @checked(old('remove_'.$field))>
                                {{ __('admin.settings.restore_logo') }}
                            </label>
                        @endif
                    </div>
                @endforeach
            </x-ui.form-section>

            <x-ui.form-section step="2" :title="__('admin.settings.sections.hero')" :description="__('admin.settings.sections.hero_hint')">
                <div class="sm:col-span-2">
                    <x-ui.translatable name="hero_tagline" :names="['ar' => 'hero_tagline', 'en' => 'hero_tagline_en']" :label="__('validation.attributes.hero_tagline')" :hint="__('admin.settings.hero_tagline_hint')" :value="['ar' => $settings['hero_tagline'] ?? null, 'en' => $settings['hero_tagline_en'] ?? null]" :maxlength="60" required />
                </div>
                <div class="sm:col-span-2">
                    <x-ui.translatable name="hero_subtitle" :names="['ar' => 'hero_subtitle', 'en' => 'hero_subtitle_en']" :label="__('validation.attributes.hero_subtitle')" :hint="__('admin.settings.hero_subtitle_hint')" :value="['ar' => $settings['hero_subtitle'] ?? null, 'en' => $settings['hero_subtitle_en'] ?? null]" :maxlength="90" required />
                </div>
            </x-ui.form-section>

            <x-ui.form-section step="3" :title="__('admin.settings.sections.contact')" :description="__('admin.settings.sections.contact_hint')">
                <x-ui.input name="contact_phone" type="tel" dir="ltr" icon="phone" :label="__('validation.attributes.contact_phone')" :value="$value('contact_phone')" maxlength="20" />
                <x-ui.input name="contact_whatsapp" type="tel" dir="ltr" icon="message-circle" :label="__('validation.attributes.contact_whatsapp')" :value="$value('contact_whatsapp')" maxlength="20" />
                <x-ui.input name="contact_email" type="email" dir="ltr" icon="mail" :label="__('validation.attributes.contact_email')" :value="$value('contact_email')" maxlength="190" />
                <div class="sm:col-span-2">
                    <x-ui.translatable name="contact_address" :names="['ar' => 'contact_address', 'en' => 'contact_address_en']" :label="__('validation.attributes.contact_address')" :value="['ar' => $settings['contact_address'] ?? null, 'en' => $settings['contact_address_en'] ?? null]" :maxlength="190" />
                </div>
            </x-ui.form-section>

            <x-ui.form-section step="4" :title="__('admin.settings.sections.impact')" :description="__('admin.settings.sections.impact_hint')">
                <x-ui.input name="impact_people" type="number" min="0" step="0.1" dir="ltr" icon="users" :label="__('validation.attributes.impact_people')" :hint="__('admin.settings.impact_people_hint')" :value="$value('impact_people')" required />
                <x-ui.input name="impact_duration" type="number" min="0" dir="ltr" icon="zap-off" :label="__('validation.attributes.impact_duration')" :hint="__('admin.settings.impact_duration_hint')" :value="$value('impact_duration')" required />
            </x-ui.form-section>

            <x-ui.form-section step="5" :title="__('admin.settings.sections.social')" :description="__('admin.settings.sections.social_hint')">
                @foreach ($socials as $network)
                    <x-ui.input :name="'social_'.$network" type="url" dir="ltr" placeholder="https://" :label="__('site.social.'.$network)" :value="$value('social_'.$network)" maxlength="255" />
                @endforeach
            </x-ui.form-section>

            <x-ui.form-actions :hint="__('admin.settings.hint')">
                <x-ui.button type="submit" icon="check">{{ __('ui.actions.save_changes') }}</x-ui.button>
            </x-ui.form-actions>
        </form>
    </div>
</x-layouts.admin>
