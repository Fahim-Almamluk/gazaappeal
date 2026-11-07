@php
    $alerts = collect([
        ['count' => $inbox['volunteers'], 'label' => trans_choice('ui.header.new_volunteers', $inbox['volunteers']), 'icon' => 'user-plus', 'url' => route('admin.volunteers.index', ['status' => App\Enums\VolunteerStatus::New->value])],
        ['count' => $inbox['messages'], 'label' => trans_choice('ui.header.new_messages', $inbox['messages']), 'icon' => 'mail', 'url' => route('admin.messages.index', ['state' => App\Enums\MessageState::Unread->value])],
    ])->where('count', '>', 0);
@endphp

<header class="sticky top-0 z-30 flex h-16 shrink-0 items-center gap-2 border-b border-line bg-surface/85 px-4 backdrop-blur-md sm:px-6 lg:px-8">
    <x-ui.button variant="ghost" size="icon" class="lg:hidden" x-on:click="openMobile" aria-label="{{ __('ui.common.menu') }}">
        <x-ui.icon name="menu" />
    </x-ui.button>

    <x-ui.button variant="ghost" size="icon" class="max-lg:hidden" x-on:click="toggleCollapse" x-bind:aria-expanded="expanded" aria-label="{{ __('ui.header.toggle_sidebar') }}">
        <x-ui.icon name="panel-right" />
    </x-ui.button>

    <div class="ms-auto flex items-center gap-1">
        <x-ui.button variant="ghost" :href="route('home')" target="_blank" icon="globe" class="max-sm:hidden">{{ __('ui.common.view_site') }}</x-ui.button>

        <x-ui.language-switch class="me-1" />

        @include('partials.theme-toggle')

        <div class="relative" x-data="dropdown" x-on:click.outside="close" x-on:keydown.escape.window="close">
            <x-ui.button variant="ghost" size="icon" class="relative" x-on:click="toggle" x-bind:aria-expanded="open" aria-haspopup="true" aria-label="{{ __('ui.header.notifications') }}">
                <x-ui.icon name="bell" />
                @if ($alerts->isNotEmpty())
                    <span class="absolute top-2 end-2 size-2 rounded-full bg-danger-solid ring-2 ring-surface"></span>
                @endif
            </x-ui.button>
            <div x-show="open" x-cloak x-transition.origin.top class="absolute end-0 z-50 mt-2 w-80 rounded-2xl border border-line bg-surface shadow-overlay">
                <p class="border-b border-line px-4 py-3 text-sm font-semibold text-ink">{{ __('ui.header.notifications') }}</p>
                @forelse ($alerts as $alert)
                    <a href="{{ $alert['url'] }}" class="flex items-center gap-3 px-4 py-3 text-sm text-ink-muted transition hover:bg-surface-hover hover:text-ink">
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary-soft-ink"><x-ui.icon :name="$alert['icon']" :size="17" /></span>
                        <span class="flex-1 font-medium">{{ $alert['label'] }}</span>
                        <x-ui.icon name="chevron-left" :size="16" class="text-ink-subtle" />
                    </a>
                @empty
                    <div class="flex flex-col items-center gap-2 px-4 py-8 text-center">
                        <span class="flex size-11 items-center justify-center rounded-full bg-surface-2 text-ink-subtle"><x-ui.icon name="bell" :size="20" /></span>
                        <p class="text-sm text-ink-muted">{{ __('ui.header.no_notifications') }}</p>
                    </div>
                @endforelse
            </div>
        </div>

        <span class="mx-1.5 hidden h-6 w-px bg-line sm:block"></span>

        <div class="relative" x-data="dropdown" x-on:click.outside="close" x-on:keydown.escape.window="close">
            <button type="button" x-on:click="toggle" x-bind:aria-expanded="open" aria-haspopup="menu" aria-label="{{ __('ui.header.account') }}" class="flex items-center gap-2.5 rounded-xl p-1 pe-2 transition hover:bg-surface-hover">
                <span class="relative flex size-9 items-center justify-center rounded-full bg-primary-soft text-primary ring-1 ring-primary/15">
                    <x-ui.icon name="user-round" :size="20" />
                    <span class="absolute -end-0.5 -bottom-0.5 size-2.5 rounded-full bg-success ring-2 ring-surface"></span>
                </span>
                <span class="hidden text-sm font-semibold text-ink sm:block">{{ auth()->user()->name }}</span>
                <x-ui.icon name="chevron-down" :size="16" class="hidden text-ink-subtle sm:block" />
            </button>

            <div x-show="open" x-cloak x-transition.origin.top role="menu" class="absolute end-0 z-50 mt-2 w-60 rounded-2xl border border-line bg-surface p-1.5 shadow-overlay">
                <a href="{{ route('admin.profile.edit') }}" role="menuitem" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-ink-muted transition hover:bg-surface-hover hover:text-ink">
                    <x-ui.icon name="user-round" :size="16" /> {{ __('ui.header.profile') }}
                </a>
                <div class="my-1 h-px bg-line"></div>
                <form method="POST" action="{{ route('logout') }}" @confirm('warning', 'logout')>
                    @csrf
                    <button type="submit" role="menuitem" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-danger transition hover:bg-danger-soft">
                        <x-ui.icon name="log-out" :size="16" /> {{ __('ui.header.logout') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
