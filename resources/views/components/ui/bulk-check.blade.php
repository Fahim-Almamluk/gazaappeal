@props([
    'module',
    'id' => null,
])

@canany(["{$module}.update", "{$module}.delete"])
    @if ($id === null)
        <input type="checkbox" data-all-check x-on:change="toggleAll" aria-label="{{ __('admin.bulk.select_all') }}" class="size-4 cursor-pointer rounded border-line-strong text-primary focus:ring-primary/30">
    @else
        <input type="checkbox" data-row-check form="bulk-form" name="ids[]" value="{{ $id }}" aria-label="{{ __('admin.bulk.select_row') }}" {{ $attributes->class('size-4 cursor-pointer rounded border-line-strong text-primary focus:ring-primary/30') }}>
    @endif
@endcanany
