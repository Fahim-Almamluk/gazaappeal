@php
    use App\Enums\VolunteerStatus;
@endphp

<x-layouts.admin :title="$volunteer->name">
    <x-ui.detail-page icon="user-plus" :title="$volunteer->name" :subtitle="$volunteer->expertise->label()" :back="route('admin.volunteers.index')" :parent="__('admin.volunteers.title')" :parent-url="route('admin.volunteers.index')">
        <x-slot:actions>
            <x-ui.badge :tone="$volunteer->status->tone()" :icon="$volunteer->status->icon()" class="px-3 py-1.5 text-sm">{{ $volunteer->status->label() }}</x-ui.badge>
        </x-slot:actions>

        <x-ui.detail-section step="1" :title="__('admin.volunteers.sections.contact')">
            <x-ui.detail-item :label="__('site.fields.email')" ltr><a href="mailto:{{ $volunteer->email }}" class="text-primary hover:underline">{{ $volunteer->email }}</a></x-ui.detail-item>
            <x-ui.detail-item :label="__('site.fields.phone')" ltr><a href="tel:{{ preg_replace('/[^0-9+]/', '', $volunteer->phone) }}" class="text-primary hover:underline">{{ $volunteer->phone }}</a></x-ui.detail-item>
            <x-ui.detail-item :label="__('site.fields.country')">{{ $volunteer->country }}</x-ui.detail-item>
            <x-ui.detail-item :label="__('site.fields.experience_years')" mono>{{ trans_choice('admin.volunteers.years', $volunteer->experience_years) }}</x-ui.detail-item>
        </x-ui.detail-section>

        <x-ui.detail-section step="2" :title="__('site.fields.volunteer_message')" plain>
            <x-site.prose :text="$volunteer->message" class="p-6 text-[15px] leading-8" />
        </x-ui.detail-section>

        <x-slot:aside>
            <x-ui.panel :title="__('admin.volunteers.review.title')" icon="clipboard-check">
                @can('volunteers.update')
                    <form method="POST" action="{{ route('admin.volunteers.update', $volunteer) }}" class="space-y-4" novalidate>
                        @csrf
                        @method('PUT')
                        <x-ui.select name="status" :label="__('admin.volunteers.columns.status')" :options="VolunteerStatus::options()" :selected="$volunteer->status->value" required />
                        <x-ui.textarea name="notes" :label="__('admin.volunteers.review.notes')" :hint="__('admin.volunteers.review.notes_hint')" :value="old('notes', $volunteer->notes)" rows="4" maxlength="2000" />
                        <x-ui.button type="submit" icon="check" class="w-full">{{ __('ui.actions.save') }}</x-ui.button>
                    </form>
                @endcan
            </x-ui.panel>

            <x-ui.panel :title="__('admin.common.record')" icon="clock">
                <dl class="space-y-3.5 text-sm">
                    <div>
                        <dt class="text-xs font-semibold text-ink-subtle">{{ __('admin.common.received_at') }}</dt>
                        <dd class="tabular mt-0.5 font-semibold text-ink">{{ $volunteer->created_at->format('Y-m-d H:i') }}</dd>
                    </div>
                    @if ($volunteer->reviewer)
                        <div>
                            <dt class="text-xs font-semibold text-ink-subtle">{{ __('admin.volunteers.review.by') }}</dt>
                            <dd class="mt-0.5 font-semibold text-ink">{{ $volunteer->reviewer->name }} <time class="tabular block text-xs font-medium text-ink-muted" datetime="{{ $volunteer->updated_at->toIso8601String() }}">{{ $volunteer->updated_at->format('Y-m-d H:i') }}</time></dd>
                        </div>
                    @endif
                </dl>
            </x-ui.panel>
        </x-slot:aside>
    </x-ui.detail-page>
</x-layouts.admin>
