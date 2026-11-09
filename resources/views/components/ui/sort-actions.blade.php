@php
    $edge = '[li:first-child>[data-row]_&]:invisible';
    $tail = '[li:last-child>[data-row]_&]:invisible';
@endphp

<x-ui.icon-button icon="chevron-up" :label="__('ui.sort.up')" data-step="-1" x-on:click="move" :class="$edge" />
<x-ui.icon-button icon="chevron-down" :label="__('ui.sort.down')" data-step="1" x-on:click="move" :class="$tail" />
<button
    type="button"
    x-on:click="toggle"
    aria-label="{{ __('ui.sort.toggle') }}"
    title="{{ __('ui.sort.toggle') }}"
    class="relative inline-flex size-9 shrink-0 items-center justify-center rounded-lg text-success transition duration-150 hover:bg-success-soft focus-visible:ring-4 focus-visible:ring-primary/20 focus-visible:outline-none [li[data-visible=false]>[data-row]_&]:text-ink-subtle [li[data-visible=false]>[data-row]_&]:hover:bg-surface-hover"
>
    <x-ui.icon name="eye" :size="18" class="[li[data-visible=false]>[data-row]_&]:hidden" />
    <x-ui.icon name="eye-off" :size="18" class="hidden [li[data-visible=false]>[data-row]_&]:block" />
</button>
