<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-6 rounded-[2rem] border border-zinc-200/80 bg-white/95 p-8 shadow-[0_24px_60px_-28px_rgba(15,23,42,0.3)] backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/90">
        <div class="space-y-3">
            <div class="inline-flex items-center gap-2 rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-sky-700 dark:border-sky-900/50 dark:bg-sky-950/30 dark:text-sky-300">
                Payroll Access
            </div>
            <div class="space-y-1">
                <flux:heading size="xl">{{ __('Masuk ke workspace payroll') }}</flux:heading>
                <flux:text class="text-zinc-600 dark:text-zinc-400">
                    {{ __('Gunakan akun Anda untuk mengakses dashboard payroll, pengaturan pajak, dan proses batch gaji.') }}
                </flux:text>
            </div>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-center text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-300" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="nama@perusahaan.com"
            />

            <!-- Password -->
            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('Password')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Masukkan password Anda')"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                        {{ __('Forgot your password?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Remember me')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full !rounded-2xl !py-3 text-sm font-semibold" data-test="login-button">
                    {{ __('Masuk ke Dashboard') }}
                </flux:button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="space-x-1 rounded-2xl bg-zinc-50 px-4 py-3 text-center text-sm rtl:space-x-reverse text-zinc-600 dark:bg-zinc-900 dark:text-zinc-400">
                <span>{{ __('Don\'t have an account?') }}</span>
                <flux:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
            </div>
        @endif
    </div>
</x-layouts::auth>
