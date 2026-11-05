@php
    use App\Support\QrCode;

    $links = $methods->reject->isBankTransfer();
    $banks = $methods->filter->isBankTransfer();
    $trust = ['shield-check' => __('site.giving.trust.official'), 'lock' => __('site.giving.trust.secure'), 'mail' => __('site.giving.trust.confirm')];
@endphp

<x-layouts.site :title="__('site.donate.title')" :description="__('site.giving.subtitle')">
    <x-site.page-header :title="__('site.donate.title')" :subtitle="__('site.donate.subtitle')" />

    @if ($methods->isEmpty())
        <section class="mx-auto mt-10 w-full max-w-[1280px] px-5 sm:px-8">
            <x-ui.card>
                <x-ui.empty-state icon="hand-coins" :title="__('site.donate.empty')" />
            </x-ui.card>
        </section>
    @endif

    @if ($banks->isNotEmpty())
        <section class="mx-auto mt-10 w-full max-w-[1280px] px-5 sm:px-8" x-data="tabs" data-open="{{ $banks->first()->id }}">
            @if ($banks->count() > 1)
                <div role="tablist" aria-label="{{ __('site.donate.banks') }}" class="reveal mx-auto mb-10 flex w-fit max-w-full gap-1 overflow-x-auto rounded-2xl border border-line bg-surface p-1.5 shadow-elevated">
                    @foreach ($banks as $bank)
                        <button
                            type="button"
                            role="tab"
                            data-tab="{{ $bank->id }}"
                            aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                            x-on:click="select"
                            class="inline-flex h-11 shrink-0 items-center gap-2 rounded-xl px-4 text-sm font-bold whitespace-nowrap text-ink-muted transition hover:text-ink aria-selected:bg-primary aria-selected:text-primary-fg"
                        >
                            <x-ui.icon name="landmark" :size="16" />
                            {{ $bank->bankDetail('bank_name') ?: $bank->name }}
                        </button>
                    @endforeach
                </div>
            @endif

            @foreach ($banks as $bank)
                <div data-tab-panel="{{ $bank->id }}" role="{{ $banks->count() > 1 ? 'tabpanel' : 'region' }}" @unless ($loop->first) hidden @endunless class="reveal grid items-center gap-12 lg:grid-cols-[minmax(0,460px)_minmax(0,1fr)]">
                    <div class="flex flex-col items-center">
                        <x-site.bank-card :method="$bank" />
                        <x-site.bank-copy :method="$bank" class="mt-4" />
                    </div>

                    <div class="space-y-5">
                        <div>
                            <p class="text-sm font-bold text-primary">{{ __('site.donate.bank_eyebrow') }}</p>
                            <h2 class="mt-1 text-[26px] leading-tight font-extrabold text-ink sm:text-[30px]">{{ $bank->name }}</h2>
                            @if ($bank->description)
                                <p class="mt-2 text-ink-muted">{{ $bank->description }}</p>
                            @endif
                        </div>

                        <x-site.bank-details :method="$bank" />

                        <x-ui.alert tone="info" :title="__('site.donate.note_title')">{{ __('site.donate.note') }}</x-ui.alert>
                    </div>
                </div>
            @endforeach
        </section>
    @endif
                    </dl>

                    <x-ui.alert tone="info" :title="__('site.donate.note_title')">{{ __('site.donate.note') }}</x-ui.alert>
                </div>
            </div>
        </section>
    @endforeach

    @if ($links->isNotEmpty())
        <section class="mx-auto mt-24 w-full max-w-[1280px] px-5 sm:px-8">
            <div class="reveal mx-auto max-w-2xl text-center">
                <p class="text-sm font-bold text-primary">{{ __('site.donate.online_eyebrow') }}</p>
                <h2 class="mt-2 text-[26px] leading-tight font-extrabold text-ink sm:text-[32px]">{{ __('site.giving.online') }}</h2>
                <p class="mt-2 text-ink-muted">{{ __('site.donate.online_hint') }}</p>
            </div>

            <div class="reveal-stagger mt-10 grid gap-5 md:grid-cols-2">
                @foreach ($links as $method)
                    <article class="reveal group relative flex flex-col overflow-hidden rounded-[24px] border border-line bg-surface p-6 shadow-elevated transition duration-200 hover:border-primary/40 hover:shadow-overlay sm:flex-row sm:items-center sm:gap-6">
                        <div class="pointer-events-none absolute -top-20 -start-20 size-52 rounded-full bg-primary/10 blur-3xl"></div>

                        <div class="relative min-w-0 flex-1">
                            <span class="flex size-12 items-center justify-center rounded-2xl bg-linear-to-br from-brand-400 to-brand-700 text-white shadow-brand">
                                <x-ui.icon name="credit-card" :size="22" />
                            </span>
                            <h3 class="mt-4 text-lg font-extrabold text-ink">{{ $method->name }}</h3>
                            @if ($method->description)
                                <p class="mt-1 text-sm leading-6 text-ink-muted">{{ $method->description }}</p>
                            @endif
                            <a href="{{ $method->url }}" target="_blank" rel="noopener noreferrer" class="mt-5 inline-flex h-11 items-center gap-2 rounded-xl bg-primary px-5 text-sm font-bold text-primary-fg shadow-brand transition hover:bg-primary-hover dark:shadow-none">
                                {{ __('site.nav.donate') }}
                                <x-ui.icon name="external-link" :size="16" />
                            </a>
                        </div>

                        <figure class="relative mt-6 flex shrink-0 flex-col items-center sm:mt-0">
                            <div class="size-36 rounded-2xl border border-line bg-white p-2.5 shadow-sm transition duration-300 group-hover:scale-105 [&>svg]:size-full">{{ QrCode::svg($method->url, 200) }}</div>
                            <figcaption class="mt-2 inline-flex items-center gap-1.5 text-xs font-semibold text-ink-subtle">
                                <x-ui.icon name="smartphone" :size="14" />
                                {{ __('site.giving.qr.scan') }}
                            </figcaption>
                        </figure>
                    </article>
                @endforeach
            </div>

            <ul class="reveal mx-auto mt-8 grid max-w-3xl gap-3 sm:grid-cols-3">
                @foreach ($trust as $icon => $text)
                    <li class="flex items-center justify-center gap-2.5 rounded-xl bg-surface-2 px-3 py-2.5 text-xs font-semibold text-ink-muted">
                        <span class="text-success"><x-ui.icon :name="$icon" :size="16" /></span>
                        {{ $text }}
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</x-layouts.site>
