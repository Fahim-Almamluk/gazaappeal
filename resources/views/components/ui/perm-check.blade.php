@props(['state' => null, 'label' => null, 'plain' => false])

<label
    @if ($state !== null) data-chip data-state="{{ $state }}" @endif
    {{ $attributes->only('title')->class([
        'group/chip relative inline-flex shrink-0 items-center gap-2.5 select-none',
        'cursor-pointer rounded-xl border border-line bg-surface px-3 py-2 text-sm font-semibold text-ink-muted transition hover:border-line-strong hover:text-ink' => $label !== null && ! $plain,
        'data-[state=on]:border-primary/40 data-[state=on]:bg-primary-soft/60 data-[state=on]:text-ink' => ! $plain,
        'data-[state=granted]:border-success/40 data-[state=granted]:bg-success-soft data-[state=granted]:text-ink' => ! $plain,
        'data-[state=revoked]:border-danger/30 data-[state=revoked]:bg-danger-soft data-[state=revoked]:text-ink-muted' => ! $plain,
        'cursor-pointer text-xs font-bold text-ink-muted hover:text-ink' => $plain,
    ]) }}
>
    <span class="relative inline-flex size-6 shrink-0 items-center justify-center">
        <input
            type="checkbox"
            {{ $attributes->except('title')->class([
                'peer absolute inset-0 size-full cursor-pointer appearance-none rounded-md border-2 border-line-strong bg-surface transition',
                'checked:border-primary checked:bg-primary indeterminate:border-primary indeterminate:bg-primary-soft',
                'group-data-[state=granted]/chip:checked:border-success group-data-[state=granted]/chip:checked:bg-success',
                'group-data-[state=revoked]/chip:border-danger-solid group-data-[state=revoked]/chip:bg-danger-soft',
                'hover:border-primary/60 focus-visible:ring-4 focus-visible:ring-primary/25 focus-visible:outline-none',
                'disabled:cursor-not-allowed disabled:opacity-60',
            ]) }}
        >
        <x-ui.icon name="check" :size="15" class="pointer-events-none relative hidden text-white peer-checked:block" />
        <x-ui.icon name="minus" :size="15" class="pointer-events-none relative hidden text-primary peer-indeterminate:block peer-checked:hidden" />
        <x-ui.icon name="x" :size="14" class="pointer-events-none relative hidden text-danger group-data-[state=revoked]/chip:block" />
    </span>

    @if ($label !== null)
        <span class="min-w-0 group-data-[state=revoked]/chip:line-through">{{ $label }}</span>
    @endif
</label>
