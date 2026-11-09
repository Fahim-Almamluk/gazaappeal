<button
    type="button"
    x-on:mousedown="grab"
    aria-label="{{ __('ui.sort.drag') }}"
    title="{{ __('ui.sort.drag') }}"
    {{ $attributes->class('flex size-9 shrink-0 cursor-grab touch-none items-center justify-center rounded-lg text-ink-subtle transition hover:bg-surface-hover hover:text-ink active:cursor-grabbing focus-visible:ring-4 focus-visible:ring-primary/20 focus-visible:outline-none max-sm:hidden') }}
>
    <x-ui.icon name="grip-vertical" :size="18" />
</button>
