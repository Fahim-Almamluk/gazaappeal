@props([
    'name' => 'q',
    'value' => '',
    'placeholder' => null,
])

@php($id = $attributes->get('id', 'search-'.$name))

<div class="relative w-full sm:w-72">
    <span class="pointer-events-none absolute inset-y-0 start-0 flex w-10 items-center justify-center text-ink-subtle"><x-ui.icon name="search" :size="16" /></span>
    <label for="{{ $id }}" class="sr-only">{{ __('ui.filter.apply') }}</label>
    <input id="{{ $id }}" type="search" name="{{ $name }}" value="{{ $value }}" placeholder="{{ $placeholder }}" autocomplete="off" class="h-10 w-full rounded-xl border border-line bg-surface ps-10 pe-3 text-sm text-ink placeholder:text-ink-subtle hover:border-line-strong focus:border-primary focus:ring-4 focus:ring-primary/12 focus:outline-none dark:bg-surface-2">
</div>
