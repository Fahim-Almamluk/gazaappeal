@php
    $steps = ['apply' => 'send', 'review' => 'clipboard-check', 'contact' => 'phone'];
    $selected = (int) old('expertise_id');
@endphp

<x-layouts.site :title="__('site.volunteer.title')" :description="__('site.volunteer.intro')">
    <x-site.page-header :title="__('site.volunteer.title')" :subtitle="__('site.volunteer.subtitle')" />

    <x-site.form-shell :title="__('site.volunteer.why_title')" :body="__('site.volunteer.intro')" class="mt-10">
        <x-slot:aside>
            <ol class="space-y-5">
                @foreach ($steps as $key => $icon)
                    <li class="relative flex gap-4">
                        @unless ($loop->last)
                            <span class="absolute top-11 bottom-[-1.25rem] start-5 w-px bg-line-strong" aria-hidden="true"></span>
                        @endunless
                        <span class="relative flex size-10 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary-soft-ink">
                            <x-ui.icon :name="$icon" :size="18" />
                        </span>
                        <div class="pt-0.5">
                            <p class="text-xs font-semibold text-ink-subtle">{{ __('site.volunteer.step', ['number' => $loop->iteration]) }}</p>
                            <p class="font-bold text-ink">{{ __("site.volunteer.steps.{$key}.title") }}</p>
                            <p class="mt-0.5 text-sm leading-6 text-ink-muted">{{ __("site.volunteer.steps.{$key}.body") }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>

            <p class="mt-10 flex items-start gap-2.5 rounded-2xl border border-line bg-surface p-4 text-xs leading-6 text-ink-muted">
                <x-ui.icon name="shield-check" :size="16" class="mt-0.5 shrink-0 text-success" />
                {{ __('site.volunteer.privacy') }}
            </p>
        </x-slot:aside>

        <form method="POST" action="{{ route('volunteer.store') }}" class="space-y-8" x-data="submitGuard" x-on:submit="submit" novalidate>
            @csrf

            <div class="hidden" aria-hidden="true">
                <input type="text" name="website" tabindex="-1" autocomplete="off">
            </div>

            <fieldset class="space-y-5">
                <legend class="mb-4 flex items-center gap-2.5 text-lg font-extrabold text-ink">
                    <span class="tabular flex size-7 items-center justify-center rounded-lg bg-primary-soft text-sm text-primary-soft-ink">1</span>
                    {{ __('site.volunteer.sections.personal') }}
                </legend>
                <x-ui.input name="name" icon="user-round" :label="__('site.fields.name')" :value="old('name')" autocomplete="name" maxlength="120" required />
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-ui.input name="email" type="email" icon="mail" :label="__('site.fields.email')" :value="old('email')" autocomplete="email" dir="ltr" maxlength="190" required />
                    <x-ui.input name="phone" type="tel" icon="phone" :label="__('site.fields.phone')" :value="old('phone')" autocomplete="tel" dir="ltr" placeholder="+970 59 000 0000" maxlength="20" required />
                    <x-ui.input name="country" icon="globe" :label="__('site.fields.country')" :value="old('country')" autocomplete="country-name" maxlength="80" required />
                    <x-ui.input name="experience_years" type="number" min="0" max="60" icon="award" :label="__('site.fields.experience_years')" :value="old('experience_years')" required />
                </div>
            </fieldset>

            <fieldset class="space-y-4">
                <legend class="mb-4 flex items-center gap-2.5 text-lg font-extrabold text-ink">
                    <span class="tabular flex size-7 items-center justify-center rounded-lg bg-primary-soft text-sm text-primary-soft-ink">2</span>
                    {{ __('site.fields.expertise') }}<span class="text-danger" aria-hidden="true">*</span>
                </legend>
                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach ($expertises as $expertise)
                        <x-ui.choice-card name="expertise_id" :value="$expertise->id" :label="$expertise->name" :icon="$expertise->displayIcon()" :checked="$selected === $expertise->id" />
                    @endforeach
                </div>
                @error('expertise_id')
                    <p class="flex items-center gap-1.5 text-[13px] font-medium text-danger" role="alert"><x-ui.icon name="circle-alert" :size="15" />{{ $message }}</p>
                @enderror
            </fieldset>

            <fieldset>
                <legend class="mb-4 flex items-center gap-2.5 text-lg font-extrabold text-ink">
                    <span class="tabular flex size-7 items-center justify-center rounded-lg bg-primary-soft text-sm text-primary-soft-ink">3</span>
                    {{ __('site.volunteer.sections.experience') }}
                </legend>
                <x-ui.textarea name="message" :label="__('site.fields.volunteer_message')" :placeholder="__('site.volunteer.message_placeholder')" :value="old('message')" minlength="20" maxlength="3000" rows="5" required />
            </fieldset>

            <x-ui.button type="submit" size="xl" icon="send" class="w-full" x-bind:disabled="busy">{{ __('site.volunteer.submit') }}</x-ui.button>
        </form>
    </x-site.form-shell>
</x-layouts.site>
