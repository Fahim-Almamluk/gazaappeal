@inject('settings', 'App\Support\SiteSettings')

@php
    $channels = array_filter([
        'phone' => ['value' => $settings->get('contact_phone'), 'href' => 'tel:'.preg_replace('/[^0-9+]/', '', (string) $settings->get('contact_phone')), 'ltr' => true],
        'message-circle' => ['value' => $settings->get('contact_whatsapp'), 'href' => 'https://wa.me/'.preg_replace('/\D/', '', (string) $settings->get('contact_whatsapp')), 'ltr' => true],
        'mail' => ['value' => $settings->get('contact_email'), 'href' => 'mailto:'.$settings->get('contact_email'), 'ltr' => true],
        'map-pin' => ['value' => $settings->localized('contact_address'), 'href' => null, 'ltr' => false],
    ], fn (array $channel): bool => filled($channel['value']));
@endphp

<x-layouts.site :title="__('site.contact.title')" :description="__('site.contact.subtitle')">
    <x-site.page-header :title="__('site.contact.title')" :subtitle="__('site.contact.subtitle')" />

    <x-site.form-shell :title="__('site.contact.aside_title')" :body="__('site.contact.aside_body')" class="mt-10">
        <x-slot:aside>
            <ul class="space-y-3">
                @foreach ($channels as $icon => $channel)
                    <li>
                        @if ($channel['href'])
                            <a href="{{ $channel['href'] }}" @if ($icon === 'message-circle') target="_blank" rel="noopener noreferrer" @endif class="flex items-center gap-3 rounded-2xl border border-line bg-surface p-3.5 transition hover:border-primary/40">
                                @include('site.partials.channel')
                            </a>
                        @else
                            <div class="flex items-center gap-3 rounded-2xl border border-line bg-surface p-3.5">
                                @include('site.partials.channel')
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>

            <p class="mt-8 flex items-start gap-2.5 text-xs leading-6 text-ink-muted">
                <x-ui.icon name="clock" :size="16" class="mt-0.5 shrink-0 text-primary" />
                {{ __('site.contact.response') }}
            </p>
        </x-slot:aside>

        <form method="POST" action="{{ route('contact.store') }}" class="space-y-8" x-data="submitGuard" x-on:submit="submit" novalidate>
            @csrf

            <div class="hidden" aria-hidden="true">
                <input type="text" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div>
                <h2 class="text-2xl font-extrabold text-ink">{{ __('site.contact.form_title') }}</h2>
                <p class="mt-1 text-sm text-ink-muted">{{ __('site.contact.form_hint') }}</p>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <x-ui.input name="name" icon="user-round" :label="__('site.fields.name')" :value="old('name')" autocomplete="name" maxlength="120" required />
                <x-ui.input name="organization" icon="building-2" :label="__('site.fields.organization')" :value="old('organization')" autocomplete="organization" maxlength="160" />
                <x-ui.input name="email" type="email" icon="mail" :label="__('site.fields.email')" :value="old('email')" autocomplete="email" dir="ltr" maxlength="190" required />
                <x-ui.input name="phone" type="tel" icon="phone" :label="__('site.fields.phone')" :value="old('phone')" autocomplete="tel" dir="ltr" placeholder="+970 59 000 0000" maxlength="20" />
            </div>

            <x-ui.textarea name="body" :label="__('site.fields.body')" :placeholder="__('site.contact.body_placeholder')" :value="old('body')" minlength="10" maxlength="3000" rows="6" required />

            <x-ui.button type="submit" size="xl" icon="send" class="w-full" x-bind:disabled="busy">{{ __('site.contact.submit') }}</x-ui.button>
        </form>
    </x-site.form-shell>
</x-layouts.site>
