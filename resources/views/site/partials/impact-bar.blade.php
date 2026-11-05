<dl class="group/stats grid grid-cols-3 gap-1 rounded-2xl border border-line bg-surface/80 p-1 shadow-elevated backdrop-blur">
    @foreach ($impact as $item)
        <div @class([
            'flex flex-col-reverse items-center gap-0.5 rounded-xl px-3 py-2.5 text-center transition duration-200 ease-out-soft',
            '-translate-y-0.5 bg-primary shadow-brand [--stat-label:var(--primary-fg)] [--stat-value:var(--primary-fg)] group-hover/stats:not-hover:translate-y-0 group-hover/stats:not-hover:bg-transparent group-hover/stats:not-hover:shadow-none group-hover/stats:not-hover:[--stat-label:var(--ink-muted)] group-hover/stats:not-hover:[--stat-value:var(--primary)] dark:shadow-none' => $item['featured'] ?? false,
            '[--stat-label:var(--ink-muted)] [--stat-value:var(--primary)] hover:-translate-y-0.5 hover:bg-primary hover:shadow-brand hover:[--stat-label:var(--primary-fg)] hover:[--stat-value:var(--primary-fg)] dark:hover:shadow-none' => ! ($item['featured'] ?? false),
        ])>
            <dt class="text-[11px] font-semibold text-(--stat-label) transition sm:text-xs">{{ $item['label'] }}</dt>
            <dd class="tabular text-xl leading-7 font-extrabold text-(--stat-value) transition sm:text-2xl sm:leading-8"><span data-count>{{ $item['value'] }}</span></dd>
        </div>
    @endforeach
</dl>
