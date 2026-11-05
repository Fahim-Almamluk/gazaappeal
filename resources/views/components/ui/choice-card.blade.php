@props([
    'name',
    'value',
    'label',
    'icon',
    'checked' => false,
])

<label {{ $attributes->class('group relative flex cursor-pointer items-center gap-3 rounded-2xl border border-line bg-surface p-3.5 transition duration-150 hover:border-line-strong has-[:checked]:border-primary has-[:checked]:bg-primary-soft has-[:checked]:shadow-[0_0_0_3px_color-mix(in_srgb,var(--primary)_14%,transparent)] has-[:focus-visible]:ring-4 has-[:focus-visible]:ring-primary/20 dark:bg-surface-2') }}>
    <input type="radio" name="{{ $name }}" value="{{ $value }}" class="peer sr-only" @checked($checked)>
    <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-surface-2 text-ink-muted transition group-has-[:checked]:bg-primary group-has-[:checked]:text-primary-fg dark:bg-surface">
        <x-ui.icon :name="$icon" :size="19" />
    </span>
    <span class="min-w-0 flex-1 text-sm leading-5 font-semibold text-ink">{{ $label }}</span>
    <span class="flex size-5 shrink-0 items-center justify-center rounded-full border-2 border-line-strong transition group-has-[:checked]:border-primary group-has-[:checked]:bg-primary" aria-hidden="true">
        <span class="size-1.5 rounded-full bg-primary-fg opacity-0 transition group-has-[:checked]:opacity-100"></span>
    </span>
</label>
