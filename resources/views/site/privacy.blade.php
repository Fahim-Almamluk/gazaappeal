<x-layouts.site :title="__('privacy.title')" :description="__('privacy.subtitle')">
    <x-site.page-header :title="__('privacy.title')" :subtitle="__('privacy.subtitle')" />

    <article class="mx-auto mt-10 w-full max-w-[900px] space-y-5 px-5 sm:px-8">
        @foreach (__('privacy.sections') as $section)
            <section class="reveal rounded-card border border-line bg-surface p-6 shadow-elevated sm:p-8">
                <h2 class="flex items-center gap-3 text-lg font-bold text-ink">
                    <span class="tabular flex size-8 shrink-0 items-center justify-center rounded-lg border border-primary/40 bg-primary-soft text-sm font-bold text-primary-soft-ink">{{ $loop->iteration }}</span>
                    {{ $section['title'] }}
                </h2>
                <p class="mt-3 text-[15px] leading-8 text-ink-muted">{{ $section['body'] }}</p>
                @if (! empty($section['items']))
                    <ul class="mt-3 space-y-2">
                        @foreach ($section['items'] as $item)
                            <li class="flex gap-2.5 text-[15px] leading-8 text-ink-muted">
                                <x-ui.icon name="check" :size="16" class="mt-2 shrink-0 text-success" />
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        @endforeach
    </article>
</x-layouts.site>
