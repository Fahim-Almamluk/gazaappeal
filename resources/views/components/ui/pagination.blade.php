@props(['paginator'])

@if ($paginator->hasPages())
    <nav {{ $attributes->class('flex items-center justify-end gap-2 border-t border-line px-6 py-4 text-sm') }} aria-label="{{ __('pagination.label') }}">
        @if ($paginator->onFirstPage())
            <x-ui.button variant="secondary" size="sm" disabled>{{ __('pagination.previous') }}</x-ui.button>
        @else
            <x-ui.button variant="secondary" size="sm" :href="$paginator->previousPageUrl()" rel="prev">{{ __('pagination.previous') }}</x-ui.button>
        @endif

        <span class="tabular px-1 text-ink-muted">{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>

        @if ($paginator->hasMorePages())
            <x-ui.button variant="secondary" size="sm" :href="$paginator->nextPageUrl()" rel="next">{{ __('pagination.next') }}</x-ui.button>
        @else
            <x-ui.button variant="secondary" size="sm" disabled>{{ __('pagination.next') }}</x-ui.button>
        @endif
    </nav>
@endif
