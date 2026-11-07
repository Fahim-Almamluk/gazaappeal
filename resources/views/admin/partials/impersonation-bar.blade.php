<div class="flex min-h-11 flex-wrap items-center justify-center gap-x-4 gap-y-1 border-b border-warning/25 bg-warning-soft px-4 py-1.5 text-sm text-warning sm:px-6 lg:px-8" role="status">
    <span class="flex items-center gap-2 font-semibold">
        <x-ui.icon name="user-round" :size="16" />
        {{ __('admin.impersonation.banner', ['name' => auth()->user()->name]) }}
    </span>
    <form method="POST" action="{{ route('admin.impersonation.leave') }}">
        @csrf
        @method('DELETE')
        <button type="submit" class="inline-flex h-8 items-center gap-1.5 rounded-lg border border-warning/30 bg-surface px-3 text-xs font-bold text-ink transition hover:border-warning/60">
            <x-ui.icon name="undo-2" :size="15" />
            {{ __('admin.impersonation.leave') }}
        </button>
    </form>
</div>
