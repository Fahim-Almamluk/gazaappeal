@php
    $promises = [
        'house' => __('auth.panel.promises.homes'),
        'zap' => __('auth.panel.promises.services'),
        'heart' => __('auth.panel.promises.support'),
    ];
@endphp

<div {{ $attributes->class('relative isolate flex-col overflow-hidden rounded-[24px] bg-brand-50 p-8 ring-1 ring-brand-100 dark:bg-brand-900 dark:ring-line xl:p-10') }}>
    <div class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(ellipse_55%_45%_at_50%_55%,rgb(255_255_255/0.9),transparent_75%)] dark:bg-[radial-gradient(ellipse_55%_45%_at_50%_55%,rgb(47_143_224/0.22),transparent_75%)]"></div>
    <div class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(ellipse_38%_30%_at_50%_58%,rgb(250_204_21/0.16),transparent_70%)]"></div>
    <div class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(circle_at_1px_1px,rgb(21_104_201/0.14)_1px,transparent_0)] bg-[length:22px_22px] [mask-image:radial-gradient(ellipse_70%_60%_at_50%_55%,#000_10%,transparent_70%)] dark:bg-[radial-gradient(circle_at_1px_1px,rgb(255_255_255/0.12)_1px,transparent_0)]"></div>

    <div class="animate-rise">
        <p class="flex items-center gap-2 text-[13px] font-bold text-primary dark:text-brand-300">
            <span class="h-px w-6 bg-primary/50 dark:bg-brand-300/50"></span>
            {{ __('auth.panel.eyebrow') }}
        </p>
        <h2 class="mt-3 text-[28px] leading-[1.35] font-bold text-ink xl:text-[32px] dark:text-white [&_mark]:animate-shimmer [&_mark]:bg-linear-to-l [&_mark]:from-brand-700 [&_mark]:via-brand-400 [&_mark]:to-brand-700 [&_mark]:bg-[length:200%_auto] [&_mark]:bg-clip-text [&_mark]:text-transparent motion-reduce:[&_mark]:animate-none dark:[&_mark]:from-sun dark:[&_mark]:via-brand-100 dark:[&_mark]:to-sun">
            {!! __('auth.panel.title') !!}
        </h2>
        <p class="mt-2 max-w-md text-sm leading-7 text-ink-muted dark:text-brand-100/80">{{ __('auth.panel.body') }}</p>
    </div>

    <div class="flex min-h-0 flex-1 items-center justify-center py-2">
        <x-scene.neighborhood class="max-h-full max-w-[560px]" />
    </div>

    <ul class="flex flex-wrap items-center justify-center gap-2">
        @foreach ($promises as $icon => $promise)
            <li class="inline-flex items-center gap-2 rounded-full border border-line bg-surface/80 py-1.5 ps-1.5 pe-3.5 text-xs font-bold text-ink shadow-sm backdrop-blur dark:border-white/10 dark:bg-white/10 dark:text-white">
                <span class="flex size-6 items-center justify-center rounded-full bg-primary-soft text-primary-soft-ink dark:bg-white/15 dark:text-sun">
                    <x-ui.icon :name="$icon" :size="14" />
                </span>
                {{ $promise }}
            </li>
        @endforeach
    </ul>
</div>
