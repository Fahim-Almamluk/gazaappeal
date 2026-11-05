@php
    $messages = collect(['success' => 'circle-check', 'error' => 'circle-alert'])
        ->filter(fn (string $icon, string $type): bool => session()->has($type));
    $tones = ['success' => 'text-success', 'error' => 'text-danger'];
@endphp

@if ($messages->isNotEmpty())
    <div class="pointer-events-none fixed bottom-5 end-5 z-[70] flex w-[min(380px,calc(100vw-2.5rem))] flex-col gap-2" aria-live="polite">
        @foreach ($messages as $type => $icon)
            <div x-data="toast" x-show="shown" x-bind:class="fade" class="pointer-events-auto flex items-start gap-3 rounded-2xl border border-line bg-surface p-4 shadow-overlay transition-opacity duration-300" role="status">
                <span class="mt-0.5 {{ $tones[$type] }}"><x-ui.icon :name="$icon" :size="20" /></span>
                <p class="flex-1 text-sm font-medium text-ink">{{ session($type) }}</p>
                <button type="button" x-on:click="dismiss" class="rounded-md p-0.5 text-ink-subtle transition hover:text-ink" aria-label="{{ __('ui.common.close') }}">
                    <x-ui.icon name="x" :size="16" />
                </button>
            </div>
        @endforeach
    </div>
@endif
