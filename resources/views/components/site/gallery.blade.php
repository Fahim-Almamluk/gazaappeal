@props(['items'])

@php
    $layout = ['col-span-2 row-span-2', '', '', '', '', 'col-span-2', 'col-span-2'];
@endphp

<div {{ $attributes }} x-data="gallery" x-on:keydown.window="key">
    <ul class="reveal-stagger grid auto-rows-[150px] grid-flow-dense grid-cols-2 gap-3 sm:auto-rows-[190px] lg:auto-rows-[210px] lg:grid-cols-4 lg:gap-4">
        @foreach ($items as $index => $item)
            <li @class(['reveal', $layout[$index % count($layout)]])>
                <button
                    type="button"
                    x-on:click="open"
                    data-gallery-item
                    data-index="{{ $index }}"
                    data-src="{{ $item->imageUrl() }}"
                    data-caption="{{ $item->caption }}"
                    aria-label="{{ $item->caption }}"
                    class="group relative block size-full cursor-zoom-in overflow-hidden rounded-2xl bg-brand-100 shadow-card ring-1 ring-line transition duration-300 hover:shadow-float focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary dark:bg-surface-2"
                >
                    <img src="{{ $item->imageUrl() }}" alt="{{ $item->caption }}" loading="lazy" decoding="async" class="size-full object-cover transition duration-500 ease-out-soft group-hover:scale-[1.06]">

                    <span class="absolute inset-x-0 bottom-0 bg-linear-to-t from-brand-900/80 via-brand-900/30 to-transparent px-4 pt-10 pb-3.5 text-start text-sm leading-6 font-semibold text-white opacity-0 transition duration-300 group-hover:opacity-100 group-focus-visible:opacity-100 [@media(hover:none)]:opacity-100">
                        <span class="line-clamp-2">{{ $item->caption }}</span>
                    </span>

                    <span class="absolute end-3 top-3 flex size-9 scale-90 items-center justify-center rounded-full bg-white/90 text-brand-700 opacity-0 shadow-sm backdrop-blur transition duration-300 group-hover:scale-100 group-hover:opacity-100 group-focus-visible:opacity-100">
                        <x-ui.icon name="maximize-2" :size="16" />
                    </span>
                </button>
            </li>
        @endforeach
    </ul>

    <template x-teleport="body">
        <div
            x-show="isOpen"
            x-cloak
            x-transition.opacity.duration.200ms
            x-trap.noscroll="isOpen"
            role="dialog"
            aria-modal="true"
            aria-label="{{ __('site.gallery_section.title') }}"
            class="fixed inset-0 z-[60] flex flex-col bg-ink/90 backdrop-blur-sm"
        >
            <div class="flex items-center justify-between px-4 py-3 text-white sm:px-6">
                <span class="tabular rounded-full bg-white/10 px-3 py-1 text-sm font-semibold backdrop-blur" x-text="counter"></span>
                <button type="button" x-on:click="close" class="flex size-10 items-center justify-center rounded-full bg-white/10 transition hover:bg-white/20 focus-visible:outline-2 focus-visible:outline-white" aria-label="{{ __('ui.common.close') }}">
                    <x-ui.icon name="x" :size="20" />
                </button>
            </div>

            <div class="relative flex min-h-0 flex-1 items-center justify-center px-4 sm:px-20" x-on:click.self="close" x-on:pointerdown="down" x-on:pointerup="up">
                <img x-bind:src="src" x-bind:alt="caption" class="max-h-full max-w-full rounded-xl object-contain shadow-2xl select-none" draggable="false">

                <button type="button" x-on:click="prev" class="absolute end-2 top-1/2 flex size-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-white backdrop-blur transition hover:bg-white/25 focus-visible:outline-2 focus-visible:outline-white sm:end-5" aria-label="{{ __('site.gallery_section.prev') }}">
                    <x-ui.icon name="chevron-right" :size="22" />
                </button>
                <button type="button" x-on:click="next" class="absolute start-2 top-1/2 flex size-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-white backdrop-blur transition hover:bg-white/25 focus-visible:outline-2 focus-visible:outline-white sm:start-5" aria-label="{{ __('site.gallery_section.next') }}">
                    <x-ui.icon name="chevron-left" :size="22" />
                </button>
            </div>

            <p class="px-6 pt-3 pb-6 text-center text-base leading-7 font-semibold text-white" x-text="caption"></p>
        </div>
    </template>
</div>
