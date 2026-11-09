@php
    $tabs = collect(config('navigation.settings_tabs'))->filter(fn (array $tab): bool => auth()->user()->hasPermission($tab['permission']));
@endphp

@if ($tabs->count() > 1)
    <nav class="flex gap-1 overflow-x-auto rounded-card border border-line bg-surface p-1.5 shadow-elevated" aria-label="{{ __('admin.settings.title') }}">
        @foreach ($tabs as $tab)
            @php($current = request()->routeIs($tab['active']))
            <a
                href="{{ route($tab['route']) }}"
                @if ($current) aria-current="page" @endif
                @class([
                    'inline-flex h-11 shrink-0 items-center gap-2 rounded-xl px-4 text-sm font-semibold whitespace-nowrap transition',
                    'bg-primary-soft text-primary-soft-ink' => $current,
                    'text-ink-muted hover:bg-surface-hover hover:text-ink' => ! $current,
                ])
            >
                <x-ui.icon :name="$tab['icon']" :size="17" />
                {{ __($tab['label']) }}
            </a>
        @endforeach
    </nav>
@endif
