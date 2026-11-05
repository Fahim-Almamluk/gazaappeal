<x-layouts.auth :title="__('auth.login.title')">
    <div class="flex flex-col items-center text-center">
        <h1><a href="{{ route('home') }}" class="inline-block"><x-brand.logo class="h-16" /></a></h1>
        <p class="mt-3 text-sm text-ink-muted">{{ __('auth.login.subheading') }}</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5" x-data="submitGuard" x-on:submit="submit" novalidate>
        @csrf

        <div class="hidden" aria-hidden="true">
            <input type="text" name="website" tabindex="-1" autocomplete="off">
        </div>

        <x-ui.input
            name="email"
            type="email"
            icon="at-sign"
            dir="ltr"
            :label="__('auth.fields.email')"
            :placeholder="__('auth.login.email_placeholder')"
            :value="old('email')"
            autocomplete="username"
            required
            autofocus
        />

        <x-ui.password
            name="password"
            :label="__('auth.fields.password')"
            :placeholder="__('auth.login.password_placeholder')"
            required
        />

        <label class="flex w-fit cursor-pointer items-center gap-2.5 text-sm text-ink-muted select-none">
            <input type="checkbox" name="remember" value="1" class="size-4 rounded border-line-strong accent-brand-600" @checked(old('remember'))>
            {{ __('auth.fields.remember') }}
        </label>

        <x-ui.button type="submit" size="lg" class="w-full" x-bind:disabled="busy">
            <span x-show="idle">{{ __('auth.login.submit') }}</span>
            <span x-show="busy" x-cloak class="inline-flex items-center gap-2">
                <span class="size-4 animate-spin rounded-full border-2 border-current/30 border-t-current"></span>
                {{ __('auth.login.submitting') }}
            </span>
        </x-ui.button>

        <div class="text-center" x-data="dropdown">
            <button type="button" x-on:click="toggle" x-bind:aria-expanded="open" class="text-sm font-medium text-ink-muted transition hover:text-primary">
                {{ __('auth.login.forgot') }}
            </button>
            <p x-show="open" x-cloak x-transition class="mt-2 text-[13px] leading-5 text-ink-subtle">{{ __('auth.login.forgot_hint') }}</p>
        </div>
    </form>
</x-layouts.auth>
