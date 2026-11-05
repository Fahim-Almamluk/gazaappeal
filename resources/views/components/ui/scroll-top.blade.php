<div
    x-data="scrollTop"
    data-show="false"
    inert
    class="fixed end-5 bottom-5 z-50 translate-y-4 scale-90 opacity-0 transition duration-300 ease-out data-[show=true]:translate-y-0 data-[show=true]:scale-100 data-[show=true]:opacity-100 sm:end-8 sm:bottom-8 print:hidden"
>
    <button
        x-ref="button"
        type="button"
        x-on:click="top"
        title="{{ __('ui.common.back_to_top') }}"
        aria-label="{{ __('ui.common.back_to_top') }}"
        class="group relative flex size-12 items-center justify-center rounded-full border border-line bg-surface/90 text-primary shadow-overlay backdrop-blur-xl transition duration-200 hover:-translate-y-1 hover:border-primary hover:bg-primary hover:text-primary-fg focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-bg focus-visible:outline-none"
    >
        <svg class="absolute inset-0 size-full -rotate-90" viewBox="0 0 48 48" fill="none" aria-hidden="true">
            <circle cx="24" cy="24" r="22.5" pathLength="1" stroke-dasharray="1" class="scroll-ring stroke-primary transition-colors group-hover:stroke-primary-fg" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <x-ui.icon name="arrow-up" :size="20" class="transition duration-200 group-hover:-translate-y-0.5" />
    </button>
</div>
