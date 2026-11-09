@props([
    'module',
    'route',
    'publishable' => true,
])

@canany(["{$module}.update", "{$module}.delete"])
    <form id="bulk-form" method="POST" action="{{ route("admin.{$route}.bulk") }}" class="hidden">
        @csrf
    </form>

    <div x-show="any" x-cloak class="flex flex-wrap items-center gap-2 border-b border-line bg-primary-soft/60 px-6 py-2.5 text-sm">
        <span class="font-semibold text-primary-soft-ink" data-template="{{ __('admin.bulk.selected', ['count' => '{count}']) }}" x-text="label"></span>

        <div class="ms-auto flex flex-wrap items-center gap-2">
            @if ($publishable)
                @can("{$module}.update")
                    <x-ui.button type="submit" form="bulk-form" name="action" value="publish" variant="secondary" size="sm" icon="eye">{{ __('admin.bulk.publish') }}</x-ui.button>
                    <x-ui.button type="submit" form="bulk-form" name="action" value="hide" variant="secondary" size="sm" icon="eye-off">{{ __('admin.bulk.hide') }}</x-ui.button>
                @endcan
            @endif
            @can("{$module}.delete")
                <button type="submit" form="bulk-form" name="action" value="delete" @confirm('danger', 'bulk_delete') class="inline-flex h-8 shrink-0 items-center justify-center gap-1.5 rounded-lg bg-danger-soft px-3 text-[13px] font-semibold whitespace-nowrap text-danger transition duration-150 select-none hover:brightness-[.97] dark:hover:brightness-125">
                    <x-ui.icon name="trash-2" :size="16" />
                    {{ __('admin.bulk.delete') }}
                </button>
            @endcan
            <x-ui.button variant="ghost" size="sm" x-on:click="clear">{{ __('admin.bulk.clear') }}</x-ui.button>
        </div>
    </div>
@endcanany
