<dl class="group/stats grid grid-cols-3 gap-1.5 rounded-2xl border border-line bg-surface/85 p-1.5 shadow-elevated backdrop-blur">
    @foreach ($impact as $item)
        @php
            $featured = $item['featured'] ?? false;
        @endphp

        <div @class([
            'relative flex flex-col-reverse items-center gap-0.5 rounded-xl px-3 py-3 text-center transition duration-300 ease-out-soft',
            '-translate-y-1 [--raised:1] [--stat-label:rgb(255_255_255/0.85)] [--stat-value:#fff] group-hover/stats:not-hover:translate-y-0 group-hover/stats:not-hover:[--raised:0] group-hover/stats:not-hover:[--stat-label:var(--ink-muted)] group-hover/stats:not-hover:[--stat-value:var(--primary)]' => $featured,
            '[--raised:0] [--stat-label:var(--ink-muted)] [--stat-value:var(--primary)] hover:-translate-y-1 hover:[--raised:1] hover:[--stat-label:rgb(255_255_255/0.85)] hover:[--stat-value:#fff]' => ! $featured,
        ])>
            <span class="pointer-events-none absolute inset-0 overflow-hidden rounded-xl bg-linear-to-b from-brand-500 to-brand-700 opacity-(--raised) shadow-[inset_0_1px_0_rgb(255_255_255/0.3),0_14px_28px_-10px_rgb(21_104_201/0.75)] ring-1 ring-white/15 ring-inset transition-opacity duration-300 dark:shadow-[inset_0_1px_0_rgb(255_255_255/0.2)]" aria-hidden="true">
                <span class="absolute inset-x-0 top-0 h-1/2 bg-linear-to-b from-white/20 to-transparent"></span>
            </span>
            <dt class="relative text-[11px] font-semibold text-(--stat-label) transition-colors duration-300 sm:text-xs">{{ $item['label'] }}</dt>
            <dd class="relative tabular text-xl leading-7 font-extrabold text-(--stat-value) transition-colors duration-300 sm:text-2xl sm:leading-8"><span data-count>{{ $item['value'] }}</span></dd>
        </div>
    @endforeach
</dl>
