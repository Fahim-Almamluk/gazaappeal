@if (auth()->user()->isSuperAdmin())
    @php
        $tabs = [
            ['route' => 'admin.settings.edit', 'active' => 'admin.settings.*', 'icon' => 'settings', 'label' => 'admin.settings.tabs.general'],
            ['route' => 'admin.menu.index', 'active' => 'admin.menu.*', 'icon' => 'list-tree', 'label' => 'admin.settings.tabs.menu'],
            ['route' => 'admin.home-sections.edit', 'active' => 'admin.home-sections.*', 'icon' => 'layout-list', 'label' => 'admin.settings.tabs.home'],
        ];
    @endphp

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
