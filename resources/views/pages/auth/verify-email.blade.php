<x-layouts::auth :title="__('Email verification')">
    <div class="mt-4 flex flex-col gap-6 rounded-[2rem] border border-zinc-200/80 bg-white/95 p-8 shadow-[0_24px_60px_-28px_rgba(15,23,42,0.3)] backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/90">
        <div class="space-y-3">
            <div class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-300">
                Email Verification
            </div>
            <div class="space-y-1">
                <flux:heading size="xl">{{ __('Verifikasi email akses payroll') }}</flux:heading>
                <flux:text class="text-zinc-600 dark:text-zinc-400">
                    {{ __('Silakan klik tautan yang sudah kami kirim ke email Anda sebelum melanjutkan ke dashboard payroll.') }}
                </flux:text>
            </div>
        </div>

        @if (session('status') == 'verification-link-sent')
            <flux:text class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-center font-medium !text-green-600 !dark:text-green-400 dark:border-emerald-900/50 dark:bg-emerald-950/30">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </flux:text>
        @endif

        <div class="flex flex-col items-center justify-between space-y-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <flux:button type="submit" variant="primary" class="w-full !rounded-2xl !py-3 text-sm font-semibold">
                    {{ __('Kirim Ulang Email Verifikasi') }}
                </flux:button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:button variant="ghost" type="submit" class="text-sm cursor-pointer" data-test="logout-button">
                    {{ __('Log out') }}
                </flux:button>
            </form>
        </div>
    </div>
</x-layouts::auth>
