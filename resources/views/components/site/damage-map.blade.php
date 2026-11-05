@props(['damages'])

@php
    use App\Enums\DamageCategory;
    use App\Enums\Governorate;
    use App\Support\Money;

    $governorates = collect(Governorate::cases())->mapWithKeys(function (Governorate $governorate) use ($damages): array {
        $items = $damages->where('governorate', $governorate);

        return [$governorate->value => [
            'case' => $governorate,
            'total' => $items->sum('amount'),
            'categories' => collect(DamageCategory::cases())->mapWithKeys(fn (DamageCategory $category): array => [
                $category->value => $items->where('category', $category)->values(),
            ]),
        ]];
    });

    $peak = max(1, $governorates->max('total'));
    $selected = $governorates->sortByDesc('total')->keys()->first();
    $heat = fn (int $total): string => match (true) {
        $total === 0 => 'fill-surface-2',
        $total / $peak >= 0.75 => 'fill-primary/45',
        $total / $peak >= 0.4 => 'fill-primary/30',
        default => 'fill-primary/15',
    };
@endphp

<section {{ $attributes->class('mx-auto w-full max-w-[1280px] px-5 sm:px-8') }}>
    <div x-data="damageMap" data-governorate="{{ $selected }}" data-category="{{ DamageCategory::Networks->value }}" class="reveal grid overflow-hidden rounded-[32px] border border-line bg-surface shadow-elevated lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
        <div class="relative isolate flex flex-col border-b border-line p-6 sm:p-9 lg:border-e lg:border-b-0">
            <div class="hero-dots pointer-events-none absolute inset-0 -z-10 opacity-70"></div>

            <div class="flex flex-wrap items-start justify-between gap-4 sm:flex-nowrap">
                <div class="min-w-0 flex-1">
                    <span class="inline-flex items-center gap-2 rounded-full bg-danger-soft py-1 ps-2 pe-3 text-xs font-bold text-danger">
                        <span class="relative flex size-2.5 items-center justify-center">
                            <span class="absolute inline-flex size-full animate-ping rounded-full bg-danger-solid/60 motion-reduce:animate-none"></span>
                            <span class="relative size-2 rounded-full bg-danger-solid"></span>
                        </span>
                        {{ __('site.damage.status') }}
                    </span>
                    <h2 class="mt-3 text-[22px] leading-tight font-extrabold text-ink sm:text-[24px]">{{ __('site.damage.title') }}</h2>
                    <p class="mt-1.5 text-[13px] leading-6 text-ink-muted">{{ __('site.damage.body') }}</p>
                </div>
                <div class="shrink-0 rounded-2xl border border-line bg-surface/90 px-4 py-3 backdrop-blur">
                    <p class="text-xs font-semibold text-ink-subtle">{{ __('site.damage.total') }}</p>
                    <p class="tabular mt-1 text-[24px] leading-none font-extrabold text-primary" dir="ltr"><span data-count>{{ Money::short($damages->sum('amount')) }}</span></p>
                </div>
            </div>

            <svg viewBox="0 0 430 540" class="mx-auto mt-2 w-full max-w-[320px] overflow-visible" role="group" aria-label="{{ __('site.damage.map') }}">
                <defs>
                    <linearGradient id="damage-map-active" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0" stop-color="#2F8FE0"/>
                        <stop offset="1" stop-color="#0D4A8A"/>
                    </linearGradient>
                </defs>

                <g class="stroke-brand-300/60 dark:stroke-brand-400/30" stroke-width="1.2" stroke-dasharray="2 9" stroke-linecap="round" fill="none" aria-hidden="true">
                    <path d="M314 -8L-11 369"/>
                    <path d="M299 -21L-26 356"/>
                    <path d="M284 -34L-41 343"/>
                </g>
                <text x="128" y="160" transform="rotate(-49.2 128 160)" text-anchor="middle" class="fill-brand-400/80 text-[13px] font-bold tracking-wide dark:fill-brand-300/60" aria-hidden="true">{{ __('site.damage.sea') }}</text>

                @foreach ($governorates as $key => $governorate)
                    <path d="{{ $governorate['case']->outline() }}" transform="translate(0 7)" class="fill-brand-900/15 dark:fill-brand-400/20" aria-hidden="true"/>
                @endforeach

                @foreach ($governorates as $key => $governorate)
                    @php([$x, $y] = $governorate['case']->anchor())
                    <g data-governorate="{{ $key }}" data-active="false" x-on:click="select" x-on:keydown.enter.prevent="select" x-on:keydown.space.prevent="select" role="button" tabindex="0" aria-pressed="false" aria-label="{{ $governorate['case']->label() }} {{ Money::short($governorate['total']) }}" class="group/gov cursor-pointer outline-none">
                        <path d="{{ $governorate['case']->outline() }}" class="{{ $heat($governorate['total']) }} stroke-surface transition-[translate,fill] duration-300 ease-out-soft group-hover/gov:-translate-y-1 group-focus-visible/gov:stroke-ring group-data-[active=true]/gov:-translate-y-1.5 group-data-[active=true]/gov:fill-[url(#damage-map-active)]" stroke-width="2.5" stroke-linejoin="round"/>
                        <g class="pointer-events-none transition-[translate] duration-300 ease-out-soft group-hover/gov:-translate-y-1 group-data-[active=true]/gov:-translate-y-1.5">
                            <text x="{{ $x }}" y="{{ $y - 4 }}" text-anchor="middle" class="fill-ink text-[17px] font-extrabold group-data-[active=true]/gov:fill-white">{{ $governorate['case']->label() }}</text>
                            <text x="{{ $x }}" y="{{ $y + 15 }}" text-anchor="middle" class="tabular fill-primary text-[14px] font-bold group-data-[active=true]/gov:fill-white/85">{{ Money::short($governorate['total']) }}</text>
                        </g>
                    </g>
                @endforeach
            </svg>

            <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-xs font-semibold text-ink-subtle">
                <span class="flex items-center gap-1.5"><x-ui.icon name="map-pin" :size="14" />{{ __('site.damage.hint') }}</span>
                <span class="flex items-center gap-1.5" aria-hidden="true">
                    <span class="h-2 w-5 rounded-full bg-primary/15"></span>
                    <span class="h-2 w-5 rounded-full bg-primary/30"></span>
                    <span class="h-2 w-5 rounded-full bg-primary/45"></span>
                </span>
            </div>
        </div>

        <div class="flex flex-col p-6 sm:p-9">
            <div class="flex flex-wrap gap-2" role="tablist">
                @foreach (DamageCategory::cases() as $category)
                    <button type="button" role="tab" data-category="{{ $category->value }}" x-on:click="pick" aria-selected="false" class="inline-flex h-10 items-center gap-2 rounded-full border border-line px-3.5 text-[13px] font-bold text-ink-muted transition hover:border-line-strong hover:text-ink aria-selected:border-primary/30 aria-selected:bg-primary-soft aria-selected:text-primary-soft-ink">
                        <x-ui.icon :name="$category->icon()" :size="16" />
                        {{ $category->label() }}
                    </button>
                @endforeach
            </div>

            @foreach ($governorates as $key => $governorate)
                <div data-governorate-panel="{{ $key }}" @if ($key !== $selected) hidden @endif class="mt-7 flex flex-1 flex-col">
                    <div class="flex items-end justify-between gap-4 border-b border-line pb-5">
                        <div>
                            <p class="text-sm font-bold text-primary">{{ __('site.damage.focus') }}</p>
                            <h3 class="mt-1 text-2xl font-extrabold text-ink sm:text-[28px]">{{ $governorate['case']->label() }}</h3>
                        </div>
                        <div class="text-end">
                            <p class="text-xs font-semibold text-ink-subtle">{{ __('site.damage.governorate_total') }}</p>
                            <p class="tabular mt-1 text-[26px] leading-none font-extrabold text-ink" dir="ltr">{{ Money::short($governorate['total']) }}</p>
                        </div>
                    </div>

                    @foreach ($governorate['categories'] as $category => $items)
                        <div data-category-panel="{{ $category }}" @if ($category !== DamageCategory::Networks->value) hidden @endif class="flex flex-1 flex-col">
                            @if ($items->isEmpty())
                                <x-ui.empty-state icon="chart-column" :title="__('site.damage.empty')" class="flex-1" />
                            @else
                                @php($largest = max(1, $items->max('amount')))
                                <ul class="divide-y divide-line">
                                    @foreach ($items as $item)
                                        <li class="py-3.5">
                                            <div class="flex items-baseline justify-between gap-4">
                                                <span class="truncate text-sm font-semibold text-ink">{{ $item->item }}</span>
                                                <span class="tabular shrink-0 text-sm font-extrabold text-ink" dir="ltr">{{ Money::short($item->amount) }}</span>
                                            </div>
                                            <x-ui.progress :value="(int) round($item->amount / $largest * 100)" tone="primary" animated class="mt-2 h-1.5" />
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="mt-3 flex items-center justify-between gap-4 rounded-2xl bg-surface-2 px-4 py-3 text-sm">
                                    <span class="font-semibold text-ink-muted">{{ __('site.damage.category_total') }}</span>
                                    <span class="tabular font-extrabold text-primary" dir="ltr">{{ Money::short($items->sum('amount')) }}</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach

            <a href="{{ route('losses') }}" class="group mt-6 inline-flex items-center gap-2 self-start text-sm font-bold text-primary transition hover:text-primary-hover">
                {{ __('site.damage.more') }}
                <x-ui.icon name="arrow-left" :size="16" class="transition group-hover:-translate-x-1 ltr:group-hover:translate-x-1" />
            </a>
        </div>
    </div>
</section>
