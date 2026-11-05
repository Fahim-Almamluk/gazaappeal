<dl class="grid grid-cols-3 rounded-2xl border border-line bg-surface/85 shadow-card backdrop-blur">
    @foreach ($impact as $item)
        <div class="group/stat relative flex flex-col-reverse items-center gap-1.5 border-s border-line px-3 py-4 text-center first:border-s-0">
            <span class="absolute inset-x-1/3 -top-px h-0.5 rounded-full bg-primary opacity-0 transition duration-300 group-hover/stat:opacity-100" aria-hidden="true"></span>
            <dt class="text-[11px] leading-5 font-semibold text-ink-muted sm:text-xs">{{ $item['label'] }}</dt>
            <dd class="tabular text-2xl leading-none font-extrabold text-brand-900 sm:text-[28px] dark:text-white"><span data-count>{{ $item['value'] }}</span></dd>
        </div>
    @endforeach
</dl>
