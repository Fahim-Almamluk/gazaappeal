@props(['methods'])

@php
    $bank = $methods->first->isBankTransfer();
    $links = $methods->reject->isBankTransfer();
    $trust = ['shield-check' => __('site.giving.trust.official'), 'lock' => __('site.giving.trust.secure'), 'mail' => __('site.giving.trust.confirm')];
@endphp

@if ($methods->isNotEmpty())
    <section {{ $attributes->class('mx-auto w-full max-w-[1280px] px-5 sm:px-8') }}>
        <x-site.section-title class="reveal" :title="__('site.giving.title')" :subtitle="__('site.giving.subtitle')" />

        <div @class(['mt-12 grid items-center gap-12', 'lg:grid-cols-2' => $bank && $links->isNotEmpty()])>
            @if ($bank)
                <div class="reveal flex flex-col items-center">
                    <x-site.bank-card :method="$bank" />
                    <x-site.bank-copy :method="$bank" class="mt-4">
                        <x-ui.button variant="ghost" :href="route('donate')" icon="landmark">{{ __('site.giving.bank_details') }}</x-ui.button>
                    </x-site.bank-copy>
                </div>
            @endif

            @if ($links->isNotEmpty())
                <div class="reveal space-y-4">
                    <p class="text-lg font-extrabold text-ink">{{ __('site.giving.online') }}</p>

                    @foreach ($links as $method)
                        <a href="{{ $method->url }}" target="_blank" rel="noopener noreferrer" class="group flex items-center gap-4 rounded-2xl border border-line bg-surface p-4 shadow-elevated transition duration-200 hover:-translate-y-0.5 hover:border-primary/50 hover:shadow-overlay sm:p-5">
                            <span class="flex size-14 shrink-0 items-center justify-center rounded-2xl bg-linear-to-br from-brand-400 to-brand-700 text-white shadow-brand">
                                <x-ui.icon name="credit-card" :size="24" />
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block font-bold text-ink">{{ $method->name }}</span>
                                @if ($method->description)
                                    <span class="mt-0.5 line-clamp-2 block text-sm leading-6 text-ink-muted">{{ $method->description }}</span>
                                @endif
                            </span>
                            <span class="inline-flex h-11 shrink-0 items-center gap-1.5 rounded-xl bg-primary px-4 text-sm font-bold text-primary-fg shadow-brand transition group-hover:bg-primary-hover dark:shadow-none">
                                {{ __('site.nav.donate') }}
                                <x-ui.icon name="arrow-left" :size="16" class="transition group-hover:-translate-x-1 ltr:group-hover:translate-x-1" />
                            </span>
                        </a>
                    @endforeach

                    <ul class="grid gap-3 pt-2 sm:grid-cols-3">
                        @foreach ($trust as $icon => $text)
                            <li class="flex items-center gap-2.5 rounded-xl bg-surface-2 px-3 py-2.5 text-xs font-semibold text-ink-muted">
                                <span class="text-success"><x-ui.icon :name="$icon" :size="16" /></span>
                                {{ $text }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </section>
@endif
