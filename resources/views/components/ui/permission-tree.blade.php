@props([
    'selected' => [],
    'inherited' => null,
    'disabled' => false,
])

@inject('catalog', 'App\Support\Permissions')

@php
    $tree = $catalog->tree();
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
    $checkbox = 'size-[18px] shrink-0 cursor-pointer rounded accent-[var(--primary)] disabled:cursor-not-allowed';
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

    @if ($inherited !== null)
        <div class="flex flex-wrap items-center gap-x-5 gap-y-2 border-b border-line px-4 py-2.5 text-xs font-semibold text-ink-subtle">
            <span class="inline-flex items-center gap-1.5"><span class="size-2.5 rounded-full bg-primary"></span>{{ __('permissions.legend.inherited') }}</span>
            <span class="inline-flex items-center gap-1.5"><span class="size-2.5 rounded-full bg-success"></span>{{ __('permissions.legend.granted') }}</span>
            <span class="inline-flex items-center gap-1.5"><span class="size-2.5 rounded-full bg-danger-solid"></span>{{ __('permissions.legend.revoked') }}</span>
        </div>
    @endif

    <ul role="tree" class="divide-y divide-line">
        @foreach ($tree as $group)
            <li role="treeitem" aria-expanded="true" data-group="{{ $group['key'] }}" class="group/node">
                <div class="flex items-center gap-3 px-4 py-3">
                    <button type="button" x-on:click="toggle" class="flex size-8 shrink-0 items-center justify-center rounded-lg text-ink-subtle transition hover:bg-surface-hover hover:text-ink" aria-label="{{ __('permissions.tree.toggle', ['name' => $group['label']]) }}">
                        <x-ui.icon name="chevron-down" :size="18" class="transition-transform duration-200 group-aria-[expanded=false]/node:-rotate-90 rtl:group-aria-[expanded=false]/node:rotate-90" />
                    </button>
                    <input type="checkbox" data-level="group" class="{{ $checkbox }}" aria-label="{{ $group['label'] }}" @disabled($disabled)>
                    <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary-soft-ink"><x-ui.icon :name="$group['icon']" :size="18" /></span>
                    <span class="flex-1 font-bold text-ink">{{ $group['label'] }}</span>
                    <span class="tabular rounded-full bg-surface-2 px-2.5 py-1 text-xs font-bold text-ink-muted" data-group-count dir="ltr">0/0</span>
                </div>

                <ul role="group" data-group-panel class="relative ms-[2.1rem] border-s border-dashed border-line-strong pb-3 ps-5 sm:ms-[2.6rem]">
                    @foreach ($group['modules'] as $module)
                        <li role="treeitem" data-module="{{ $module['key'] }}" data-search="{{ $module['label'] }} {{ $group['label'] }} {{ collect($module['actions'])->pluck('label')->implode(' ') }}" class="relative py-2 before:absolute before:top-[1.35rem] before:-start-5 before:h-px before:w-4 before:bg-line-strong">
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-2.5 rounded-xl px-3 py-2 transition hover:bg-surface-hover/70">
                                <label class="flex min-w-48 flex-1 cursor-pointer items-center gap-3">
                                    <input type="checkbox" data-level="module" class="{{ $checkbox }}" @disabled($disabled)>
                                    <span class="flex size-8 shrink-0 items-center justify-center rounded-lg border border-line bg-surface text-ink-muted"><x-ui.icon :name="$module['icon']" :size="16" /></span>
                                    <span class="text-sm font-semibold text-ink">{{ $module['label'] }}</span>
                                </label>

                                <div class="flex flex-wrap items-center gap-1.5">
                                    @foreach ($module['actions'] as $action)
                                        <label
                                            data-chip
                                            data-state="{{ $stateOf($action['key']) }}"
                                            @class([
                                                'group/chip inline-flex h-8 cursor-pointer items-center gap-1.5 rounded-full border px-3 text-xs font-bold transition select-none has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-primary/40',
                                                'border-line bg-surface text-ink-muted hover:border-line-strong hover:text-ink',
                                                'data-[state=on]:border-primary/40 data-[state=on]:bg-primary-soft data-[state=on]:text-primary-soft-ink',
                                                'data-[state=granted]:border-success/40 data-[state=granted]:bg-success-soft data-[state=granted]:text-success',
                                                'data-[state=revoked]:border-danger/30 data-[state=revoked]:bg-danger-soft data-[state=revoked]:text-danger',
                                                'cursor-not-allowed opacity-80' => $disabled,
                                            ])
                                            @if ($inherited !== null && in_array($action['key'], $inherited, true)) title="{{ __('permissions.legend.inherited') }}" @endif
                                        >
                                            <input
                                                type="checkbox"
                                                name="permissions[]"
                                                value="{{ $action['key'] }}"
                                                data-level="action"
                                                @if ($inherited !== null) data-inherited="{{ in_array($action['key'], $inherited, true) ? '1' : '0' }}" @endif
                                                class="sr-only"
                                                @checked($isChecked($action['key']))
                                                @disabled($disabled)
                                            >
                                            <span class="hidden group-data-[state=on]/chip:inline-flex"><x-ui.icon name="check" :size="13" /></span>
                                            <span class="hidden group-data-[state=granted]/chip:inline-flex"><x-ui.icon name="plus" :size="13" /></span>
                                            <span class="hidden group-data-[state=revoked]/chip:inline-flex"><x-ui.icon name="x" :size="13" /></span>
                                            <span class="group-data-[state=revoked]/chip:line-through">{{ $action['label'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </li>
        @endforeach
    </ul>
</div>
