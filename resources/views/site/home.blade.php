@inject('settings', 'App\Support\SiteSettings')

@php
    $impact = [
        ['value' => $settings->get('impact_people'), 'label' => __('site.hero.stats.people')],
        ['value' => $settings->get('impact_duration'), 'label' => __('site.hero.stats.duration'), 'featured' => true],
        ['value' => number_format($projectsCount), 'label' => __('site.hero.stats.projects')],
    ];
@endphp

<x-layouts.site>
    @include('site.partials.hero')

    @php
        $blocks = collect($sections)
            ->map(fn (array $data, string $key): string => trim(view("site.sections.{$key}", $data)->render()))
            ->filter()
            ->values();
    @endphp

    @foreach ($blocks as $block)
        <div @class([
            'mt-24',
            'has-[>[data-band]]:mt-0 [&>[data-band]]:border-t-0' => $loop->first,
            '[&>[data-band]]:border-b!' => ! $loop->last,
        ])>{!! $block !!}</div>
    @endforeach
</x-layouts.site>
