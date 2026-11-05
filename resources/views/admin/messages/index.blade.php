@php
    use App\Enums\MessageState;
@endphp

<x-layouts.admin :title="__('admin.messages.title')">
    <div class="space-y-6">
        <x-ui.page-header icon="mail" :title="__('admin.messages.title')" :subtitle="__('admin.messages.subtitle')" />

        <x-ui.card :padding="false" class="overflow-hidden">
            <x-ui.filter-bar :action="route('admin.messages.index')" :active="filled($search) || $state">
                <x-ui.search-input :value="$search" :placeholder="__('admin.messages.search')" />
                <div class="w-full sm:w-44">
                    <x-ui.select name="state" compact :options="MessageState::options()" :selected="$state?->value" :placeholder="__('admin.messages.inbox')" :aria-label="__('admin.messages.columns.state')" />
                </div>
            </x-ui.filter-bar>

            <x-ui.results-count :paginator="$messages" />

            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] text-sm">
                    <thead>
                        <tr class="border-b border-line bg-surface-2/60 text-xs font-semibold text-ink-subtle">
                            <th scope="col" class="w-14 px-4 py-3 text-start">#</th>
                            <th scope="col" class="px-6 py-3 text-start">{{ __('admin.messages.columns.sender') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('admin.messages.columns.message') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('admin.messages.columns.state') }}</th>
                            <th scope="col" class="px-3 py-3 text-start">{{ __('admin.common.received_at') }}</th>
                            <th scope="col" class="px-6 py-3 text-end">{{ __('ui.actions.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($messages as $message)
                            @php($messageState = $message->state())
                            <tr @class(['transition hover:bg-surface-hover', 'bg-primary-soft/30' => $messageState === MessageState::Unread])>
                                <td class="tabular px-4 py-3.5 text-xs font-semibold text-ink-subtle">{{ $messages->firstItem() + $loop->index }}</td>
                                <td class="px-6 py-3.5">
                                    <p @class(['text-ink', 'font-bold' => $messageState === MessageState::Unread, 'font-semibold' => $messageState !== MessageState::Unread])>{{ $message->name }}</p>
                                    <p class="text-xs text-ink-subtle" dir="ltr">{{ $message->email }}</p>
                                </td>
                                <td class="px-3 py-3.5"><p class="max-w-md truncate text-ink-muted">{{ $message->body }}</p></td>
                                <td class="px-3 py-3.5"><x-ui.badge :tone="$messageState->tone()" :icon="$messageState->icon()">{{ $messageState->label() }}</x-ui.badge></td>
                                <td class="px-3 py-3.5 text-xs text-ink-subtle"><time datetime="{{ $message->created_at->toIso8601String() }}">{{ $message->created_at->diffForHumans() }}</time></td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center justify-end">
                                        <x-ui.icon-button icon="eye" tone="primary" :label="__('ui.actions.view')" :href="route('admin.messages.show', $message)" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><x-ui.empty-state icon="mail-open" :title="__('admin.messages.empty')" /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-ui.pagination :paginator="$messages" />
        </x-ui.card>
    </div>
</x-layouts.admin>
