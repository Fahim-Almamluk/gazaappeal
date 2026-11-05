@php
    $state = $message->state();
@endphp

<x-layouts.admin :title="$message->name">
    <x-ui.detail-page icon="mail-open" :title="$message->name" :subtitle="$message->organization" :back="route('admin.messages.index')" :parent="__('admin.messages.title')" :parent-url="route('admin.messages.index')">
        <x-slot:actions>
            <x-ui.button :href="'mailto:'.$message->email" icon="send">{{ __('admin.messages.reply') }}</x-ui.button>
        </x-slot:actions>

        <x-ui.detail-section step="1" :title="__('admin.messages.sections.sender')">
            <x-ui.detail-item :label="__('site.fields.email')" ltr>{{ $message->email }}</x-ui.detail-item>
            <x-ui.detail-item :label="__('site.fields.phone')" ltr>{{ $message->phone ?? __('ui.common.none') }}</x-ui.detail-item>
        </x-ui.detail-section>

        <x-ui.detail-section step="2" :title="__('admin.messages.sections.body')" plain>
            <x-site.prose :text="$message->body" class="p-6 text-[15px] leading-8" />
        </x-ui.detail-section>

        <x-slot:aside>
            <x-ui.panel :title="__('admin.messages.columns.state')" icon="inbox">
                <x-ui.badge :tone="$state->tone()" :icon="$state->icon()">{{ $state->label() }}</x-ui.badge>
                @can('messages.update')
                    @if ($message->archived_at)
                        <form method="POST" action="{{ route('admin.messages.restore', $message) }}" @confirm('success', 'restore', ['name' => $message->name])>
                            @csrf
                            @method('PATCH')
                            <x-ui.button type="submit" variant="soft" icon="rotate-ccw" class="w-full">{{ __('admin.messages.restore') }}</x-ui.button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.messages.archive', $message) }}" @confirm('warning', 'archive', ['name' => $message->name])>
                            @csrf
                            @method('PATCH')
                            <x-ui.button type="submit" variant="secondary" icon="archive" class="w-full">{{ __('admin.messages.archive') }}</x-ui.button>
                        </form>
                    @endif
                @endcan
            </x-ui.panel>

            <x-ui.panel :title="__('admin.common.record')" icon="clock">
                <dl class="space-y-3.5 text-sm">
                    <div>
                        <dt class="text-xs font-semibold text-ink-subtle">{{ __('admin.common.received_at') }}</dt>
                        <dd class="tabular mt-0.5 font-semibold text-ink">{{ $message->created_at->format('Y-m-d H:i') }}</dd>
                    </div>
                    @if ($message->archived_at)
                        <div>
                            <dt class="text-xs font-semibold text-ink-subtle">{{ __('admin.messages.archived_at') }}</dt>
                            <dd class="tabular mt-0.5 font-semibold text-ink">{{ $message->archived_at->format('Y-m-d H:i') }}</dd>
                        </div>
                    @endif
                </dl>
            </x-ui.panel>
        </x-slot:aside>
    </x-ui.detail-page>
</x-layouts.admin>
