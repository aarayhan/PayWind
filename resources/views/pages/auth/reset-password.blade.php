<x-layouts::auth :title="__('Reset password')">
    <div class="flex flex-col gap-6 rounded-[2rem] border border-zinc-200/80 bg-white/95 p-8 shadow-[0_24px_60px_-28px_rgba(15,23,42,0.3)] backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/90">
        <div class="space-y-3">
            <div class="inline-flex items-center gap-2 rounded-full border border-violet-200 bg-violet-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-violet-700 dark:border-violet-900/50 dark:bg-violet-950/30 dark:text-violet-300">
                Password Reset
            </div>
            <div class="space-y-1">
                <flux:heading size="xl">{{ __('Atur ulang password payroll') }}</flux:heading>
                <flux:text class="text-zinc-600 dark:text-zinc-400">
                    {{ __('Masukkan password baru untuk melanjutkan akses ke dashboard payroll.') }}
                </flux:text>
            </div>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-center text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-300" :status="session('status')" />

        <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Token -->
            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <!-- Email Address -->
            <flux:input
                name="email"
                value="{{ request('email') }}"
                :label="__('Email')"
                type="email"
                required
                autocomplete="email"
            />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Masukkan password baru')"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Ulangi password baru')"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full !rounded-2xl !py-3 text-sm font-semibold" data-test="reset-password-button">
                    {{ __('Simpan Password Baru') }}
                </flux:button>
            </div>
        </form>
    </div>
</x-layouts::auth>
