@props([
    'paginator',
    'anchor' => 'list',
])

@php
    $paginator->fragment($anchor)->onEachSide(1);
    $links = collect($paginator->linkCollection())->slice(1, -1)->values();
    $itemClass = 'tabular flex size-10 items-center justify-center rounded-full text-sm font-bold transition duration-150';
    $arrowClass = 'flex size-11 items-center justify-center rounded-full border border-line bg-surface text-ink shadow-sm transition duration-150';
@endphp

@if ($paginator->hasPages())
    <nav {{ $attributes->class('mt-12 flex flex-col items-center gap-4') }} aria-label="{{ __('pagination.label') }}">
        <div class="flex items-center gap-2 sm:gap-3">
            @if ($paginator->onFirstPage())
                <span class="{{ $arrowClass }} cursor-not-allowed opacity-40" aria-hidden="true"><x-ui.icon name="chevron-right" :size="20" /></span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $arrowClass }} hover:border-primary hover:text-primary" aria-label="{{ __('pagination.previous') }}">
                    <x-ui.icon name="chevron-right" :size="20" />
                </a>
            @endif

            <span class="tabular rounded-full border border-line bg-surface px-5 py-2.5 text-sm font-bold text-ink shadow-sm sm:hidden">{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>

            <ul class="hidden items-center gap-1 rounded-full border border-line bg-surface p-1 shadow-sm sm:flex">
                @foreach ($links as $link)
                    <li>
                        @if (! is_numeric($link['label']))
                            <span class="{{ $itemClass }} text-ink-subtle" aria-hidden="true">…</span>
                        @elseif ($link['active'])
                            <span class="{{ $itemClass }} bg-primary text-primary-fg shadow-brand" aria-current="page">{{ $link['label'] }}</span>
                        @else
                            <a href="{{ $link['url'] }}" class="{{ $itemClass }} text-ink-muted hover:bg-primary-soft hover:text-primary-soft-ink" aria-label="{{ __('pagination.page', ['page' => $link['label']]) }}">{{ $link['label'] }}</a>
                        @endif
                    </li>
                @endforeach
            </ul>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $arrowClass }} hover:border-primary hover:text-primary" aria-label="{{ __('pagination.next') }}">
                    <x-ui.icon name="chevron-left" :size="20" />
                </a>
            @else
                <span class="{{ $arrowClass }} cursor-not-allowed opacity-40" aria-hidden="true"><x-ui.icon name="chevron-left" :size="20" /></span>
            @endif
        </div>

        <p class="tabular text-xs font-medium text-ink-subtle">{{ __('pagination.showing', ['from' => $paginator->firstItem(), 'to' => $paginator->lastItem(), 'total' => $paginator->total()]) }}</p>
    </nav>
@endif
