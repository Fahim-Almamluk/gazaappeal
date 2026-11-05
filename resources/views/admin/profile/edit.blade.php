<x-layouts.admin :title="__('admin.profile.title')">
    <div class="mx-auto max-w-6xl space-y-6">
        <section class="relative isolate overflow-hidden rounded-panel bg-linear-to-l from-brand-700 via-brand-600 to-brand-500 p-7 text-white sm:p-9">
            <div class="pointer-events-none absolute -top-24 start-10 -z-10 size-72 rounded-full bg-brand-300/30 blur-3xl"></div>
            <div class="grain pointer-events-none absolute inset-0 -z-10 opacity-[0.15] mix-blend-overlay"></div>

            <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                <span class="flex size-20 shrink-0 items-center justify-center rounded-full border border-white/25 bg-white/15 backdrop-blur">
                    <x-ui.icon name="user-round" :size="36" />
                </span>
                <div class="space-y-2">
                    <h1 class="text-[28px] leading-tight font-extrabold sm:text-[32px]">{{ $user->name }}</h1>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white py-1.5 ps-2.5 pe-3.5 text-xs font-bold text-brand-700">
                            <x-ui.icon name="shield-check" :size="15" />
                            {{ $user->role?->name }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-white/20 bg-white/10 px-3.5 py-1.5 text-xs font-medium backdrop-blur" dir="ltr">
                            <x-ui.icon name="mail" :size="14" />
                            {{ $user->email }}
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-2 xl:items-start">
            <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-4" novalidate>
                @csrf
                @method('PUT')
                <x-ui.form-section step="1" :title="__('admin.profile.sections.info')" :description="__('admin.profile.sections.info_hint')">
                    <div class="sm:col-span-2">
                        <x-ui.input name="name" icon="user-round" :label="__('admin.users.fields.name')" :value="old('name', $user->name)" maxlength="120" required />
                    </div>
                    <div class="sm:col-span-2">
                        <x-ui.input name="email" type="email" dir="ltr" icon="at-sign" :label="__('admin.users.fields.email')" :value="old('email', $user->email)" maxlength="190" required />
                    </div>
                </x-ui.form-section>
                <div class="flex justify-end">
                    <x-ui.button type="submit" icon="check">{{ __('ui.actions.save_changes') }}</x-ui.button>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.profile.password') }}" class="space-y-4" novalidate>
                @csrf
                @method('PUT')
                <x-ui.form-section step="2" :title="__('admin.profile.sections.password')" :description="__('admin.profile.sections.password_hint')">
                    <div class="sm:col-span-2">
                        <x-ui.password name="current_password" bag="password" :label="__('admin.profile.fields.current_password')" autocomplete="current-password" required />
                    </div>
                    <x-ui.password name="password" bag="password" :label="__('admin.users.fields.password')" autocomplete="new-password" required />
                    <x-ui.password name="password_confirmation" bag="password" :label="__('admin.users.fields.password_confirmation')" autocomplete="new-password" required />
                </x-ui.form-section>
                <div class="flex justify-end">
                    <x-ui.button type="submit" icon="lock">{{ __('admin.profile.change_password') }}</x-ui.button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
