<div class="relative" x-data="dropdown" x-on:click.outside="close" x-on:keydown.escape.window="close">
    <div x-data="theme">
        <x-ui.button variant="ghost" size="icon" x-on:click="toggle" x-bind:aria-expanded="open" aria-haspopup="menu" aria-label="{{ __('ui.theme.label') }}">
            <span x-show="isLight"><x-ui.icon name="sun" /></span>
            <span x-show="isDark" x-cloak><x-ui.icon name="moon" /></span>
            <span x-show="isSystem" x-cloak><x-ui.icon name="monitor" /></span>
        </x-ui.button>

        <div
            x-show="open"
            x-cloak
            x-transition
            role="menu"
            @class([
                'absolute end-0 z-50 w-44 rounded-xl border border-line bg-surface p-1.5 shadow-overlay',
                'bottom-full mb-2' => $up ?? false,
                'mt-2' => ! ($up ?? false),
            ])
        >
            @foreach (['light' => ['sun', 'setLight', 'isLight'], 'dark' => ['moon', 'setDark', 'isDark'], 'system' => ['monitor', 'setSystem', 'isSystem']] as $mode => [$icon, $action, $state])
                <button
                    type="button"
                    role="menuitemradio"
                    x-bind:aria-checked="{{ $state }}"
                    x-on:click="{{ $action }}"
                    class="flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-sm text-ink-muted transition hover:bg-surface-hover hover:text-ink"
                >
                    <x-ui.icon :name="$icon" :size="16" />
                    <span class="flex-1 text-start">{{ __("ui.theme.{$mode}") }}</span>
                    <span x-show="{{ $state }}" class="text-primary"><x-ui.icon name="check" :size="15" /></span>
                </button>
            @endforeach
        </div>
    </div>
</div>
