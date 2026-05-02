<x-layouts::auth :title="__('Forgot password')">
    <div class="flex flex-col gap-6 rounded-[2rem] border border-zinc-200/80 bg-white/95 p-8 shadow-[0_24px_60px_-28px_rgba(15,23,42,0.3)] backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/90">
        <div class="space-y-3">
            <div class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-amber-700 dark:border-amber-900/50 dark:bg-amber-950/30 dark:text-amber-300">
                Password Recovery
            </div>
            <div class="space-y-1">
                <flux:heading size="xl">{{ __('Reset akses payroll Anda') }}</flux:heading>
                <flux:text class="text-zinc-600 dark:text-zinc-400">
                    {{ __('Masukkan email kerja Anda untuk menerima tautan reset password.') }}
                </flux:text>
            </div>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-center text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-300" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                type="email"
                required
                autofocus
                placeholder="nama@perusahaan.com"
            />

            <flux:button variant="primary" type="submit" class="w-full !rounded-2xl !py-3 text-sm font-semibold" data-test="email-password-reset-link-button">
                {{ __('Kirim Link Reset Password') }}
            </flux:button>
        </form>

        <div class="rounded-2xl bg-zinc-50 px-4 py-3 text-center text-sm rtl:space-x-reverse text-zinc-600 dark:bg-zinc-900 dark:text-zinc-400">
            <span>{{ __('Or, return to') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('log in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
