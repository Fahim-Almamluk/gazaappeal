@props([
    'selected' => [],
    'inherited' => null,
    'disabled' => false,
])

@inject('catalog', 'App\Support\Permissions')

@php
    $tree = $catalog->tree(auth()->user()->isSuperAdmin());
    $selected = old('permissions', $selected);
    $isChecked = fn (string $key): bool => $disabled || in_array($key, $selected, true);
    $stateOf = function (string $key) use ($inherited, $isChecked): string {
        if ($inherited === null) {
            return $isChecked($key) ? 'on' : 'off';
        }

        $fromRole = in_array($key, $inherited, true);

        return match (true) {
            $isChecked($key) && $fromRole => 'on',
            $isChecked($key) => 'granted',
            $fromRole => 'revoked',
            default => 'off',
        };
    };
@endphp

<div {{ $attributes->class('overflow-hidden rounded-2xl border border-line bg-surface') }} x-data="permissionTree" x-on:change="change">
    <div class="flex flex-wrap items-center gap-3 border-b border-line bg-surface-2/60 px-4 py-3">
        <label class="relative min-w-52 flex-1">
            <span class="pointer-events-none absolute inset-y-0 start-3 flex items-center text-ink-subtle"><x-ui.icon name="search" :size="16" /></span>
            <input type="search" x-on:input="filter" placeholder="{{ __('permissions.tree.search') }}" aria-label="{{ __('permissions.tree.search') }}" class="h-10 w-full rounded-xl border border-line bg-surface ps-9 pe-3 text-sm text-ink placeholder:text-ink-subtle focus:border-primary focus:ring-4 focus:ring-primary/12 focus:outline-none dark:bg-surface">
        </label>

        <span class="inline-flex h-10 items-center gap-2 rounded-xl bg-primary-soft px-3 text-sm font-bold text-primary-soft-ink">
            <x-ui.icon name="shield-check" :size="16" />
            <span class="tabular" data-counter dir="ltr">0 / 0</span>
        </span>

        @unless ($disabled)
            <div class="flex items-center gap-1.5">
                <button type="button" x-on:click="selectAll" class="inline-flex h-10 items-center gap-1.5 rounded-xl border border-line bg-surface px-3 text-sm font-semibold text-ink-muted transition hover:border-primary/40 hover:text-primary">
                    <x-ui.icon name="check" :size="15" />
                    {{ __('permissions.tree.select_all') }}
                </button>
                <button type="button" x-on:click="clearAll" class="inline-flex h-10 items-center gap-1.5 rounded-xl border border-line bg-surface px-3 text-sm font-semibold text-ink-muted transition hover:border-danger/40 hover:text-danger">
                    <x-ui.icon name="x" :size="15" />
                    {{ __('permissions.tree.clear_all') }}
                </button>
            </div>
        @endunless
    </div>

    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 border-b border-line px-4 py-2.5 text-xs font-semibold text-ink-muted">
        <span class="inline-flex items-center gap-2"><span class="flex size-4 items-center justify-center rounded border-2 border-primary bg-primary text-white"><x-ui.icon name="check" :size="11" /></span>{{ $inherited === null ? __('permissions.legend.active') : __('permissions.legend.inherited') }}</span>
        @if ($inherited !== null)
            <span class="inline-flex items-center gap-2"><span class="flex size-4 items-center justify-center rounded border-2 border-success bg-success text-white"><x-ui.icon name="check" :size="11" /></span>{{ __('permissions.legend.granted') }}</span>
            <span class="inline-flex items-center gap-2"><span class="flex size-4 items-center justify-center rounded border-2 border-danger-solid bg-danger-soft text-danger"><x-ui.icon name="x" :size="10" /></span>{{ __('permissions.legend.revoked') }}</span>
        @endif
        <span class="inline-flex items-center gap-2"><span class="size-4 rounded border-2 border-line-strong bg-surface"></span>{{ __('permissions.legend.inactive') }}</span>
    </div>

    <div class="divide-y divide-line">
        @foreach ($tree as $group)
            <section data-group="{{ $group['key'] }}" aria-expanded="true" class="group/node">
                <div class="flex items-center gap-3 bg-surface-2/70 px-4 py-3">
                    <button type="button" x-on:click="toggle" class="flex size-8 shrink-0 items-center justify-center rounded-lg text-ink-subtle transition hover:bg-surface-hover hover:text-ink" aria-label="{{ __('permissions.tree.toggle', ['name' => $group['label']]) }}">
                        <x-ui.icon name="chevron-down" :size="18" class="transition-transform duration-200 group-aria-[expanded=false]/node:-rotate-90 rtl:group-aria-[expanded=false]/node:rotate-90" />
                    </button>
                    <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary-soft-ink"><x-ui.icon :name="$group['icon']" :size="18" /></span>
                    <span class="flex-1 font-bold text-ink">{{ $group['label'] }}</span>
                    <span class="tabular rounded-full bg-surface px-2.5 py-1 text-xs font-bold text-ink-muted ring-1 ring-line" data-group-count dir="ltr">0/0</span>
                    <x-ui.perm-check plain :label="__('permissions.tree.all')" data-level="group" aria-label="{{ $group['label'] }}" :disabled="$disabled" />
                </div>

                @foreach ($group['modules'] as $module)
                    <div data-module="{{ $module['key'] }}" data-search="{{ $module['label'] }} {{ $group['label'] }} {{ collect($module['actions'])->pluck('label')->implode(' ') }}" class="border-t border-line px-4 py-4 sm:ps-16">
                        <div class="mb-3 flex items-center gap-3">
                            <span class="flex size-8 shrink-0 items-center justify-center rounded-lg border border-line bg-surface text-ink-muted"><x-ui.icon :name="$module['icon']" :size="16" /></span>
                            <h3 class="flex-1 font-semibold text-ink">{{ $module['label'] }}</h3>
                            <x-ui.perm-check plain :label="__('permissions.tree.all')" data-level="module" aria-label="{{ $module['label'] }} — {{ __('permissions.tree.all') }}" :disabled="$disabled" />
                        </div>

                        <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-4">
                            @foreach ($module['actions'] as $action)
                                <x-ui.perm-check
                                    :state="$stateOf($action['key'])"
                                    :label="$action['label']"
                                    name="permissions[]"
                                    value="{{ $action['key'] }}"
                                    data-level="action"
                                    :checked="$isChecked($action['key'])"
                                    :disabled="$disabled"
                                    :data-inherited="$inherited === null ? null : (in_array($action['key'], $inherited, true) ? '1' : '0')"
                                />
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </section>
        @endforeach
    </div>
</div>
