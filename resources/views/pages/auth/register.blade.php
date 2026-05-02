<x-layouts::auth :title="__('Register')">
    <div class="flex flex-col gap-6 rounded-[2rem] border border-zinc-200/80 bg-white/95 p-8 shadow-[0_24px_60px_-28px_rgba(15,23,42,0.3)] backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/90">
        <div class="space-y-3">
            <div class="inline-flex items-center gap-2 rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-sky-700 dark:border-sky-900/50 dark:bg-sky-950/30 dark:text-sky-300">
                Account Setup
            </div>
            <div class="space-y-1">
                <flux:heading size="xl">{{ __('Buat akun payroll baru') }}</flux:heading>
                <flux:text class="text-zinc-600 dark:text-zinc-400">
                    {{ __('Lengkapi data dasar untuk membuat akun akses ke sistem payroll.') }}
                </flux:text>
            </div>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-center text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-300" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Name -->
            <flux:input
                name="name"
                :label="__('Name')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Full name')"
            />

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="nama@perusahaan.com"
            />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Buat password yang aman')"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Ulangi password')"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full !rounded-2xl !py-3 text-sm font-semibold" data-test="register-user-button">
                    {{ __('Buat Akun') }}
                </flux:button>
            </div>
        </form>

        <div class="rounded-2xl bg-zinc-50 px-4 py-3 text-center text-sm rtl:space-x-reverse text-zinc-600 dark:bg-zinc-900 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
