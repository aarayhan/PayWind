<x-layouts::auth :title="__('Confirm password')">
    <div class="flex flex-col gap-6 rounded-[2rem] border border-zinc-200/80 bg-white/95 p-8 shadow-[0_24px_60px_-28px_rgba(15,23,42,0.3)] backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/90">
        <div class="space-y-3">
            <div class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-rose-700 dark:border-rose-900/50 dark:bg-rose-950/30 dark:text-rose-300">
                Secure Confirmation
            </div>
            <div class="space-y-1">
                <flux:heading size="xl">{{ __('Konfirmasi password Anda') }}</flux:heading>
                <flux:text class="text-zinc-600 dark:text-zinc-400">
                    {{ __('Area ini sensitif. Masukkan password untuk melanjutkan perubahan penting di sistem payroll.') }}
                </flux:text>
            </div>
        </div>

        <x-auth-session-status class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-center text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-300" :status="session('status')" />

        <form method="POST" action="{{ route('password.confirm.store') }}" class="flex flex-col gap-6">
            @csrf

            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="__('Masukkan password Anda')"
                viewable
            />

            <flux:button variant="primary" type="submit" class="w-full !rounded-2xl !py-3 text-sm font-semibold" data-test="confirm-password-button">
                {{ __('Konfirmasi Akses') }}
            </flux:button>
        </form>
    </div>
</x-layouts::auth>
