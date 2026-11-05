@props([
    'action',
    'active' => false,
])

<form method="GET" action="{{ $action }}" role="search" {{ $attributes->class('flex flex-col gap-4 border-b border-line p-4 sm:p-5') }}>
    <div class="flex flex-wrap items-center gap-3">
        {{ $slot }}
    </div>

    <div class="flex items-center justify-end gap-2 border-t border-line pt-4">
        @if ($active)
            <x-ui.button variant="reset" icon="rotate-ccw" :href="$action">{{ __('ui.filter.reset') }}</x-ui.button>
        @endif
        <x-ui.button type="submit" icon="search" class="min-w-32">{{ __('ui.filter.apply') }}</x-ui.button>
    </div>
</form>
