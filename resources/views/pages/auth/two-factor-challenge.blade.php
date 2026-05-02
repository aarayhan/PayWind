<x-layouts::auth :title="__('Two-factor authentication')">
    <div class="flex flex-col gap-6 rounded-[2rem] border border-zinc-200/80 bg-white/95 p-8 shadow-[0_24px_60px_-28px_rgba(15,23,42,0.3)] backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/90">
        <div
            class="relative w-full h-auto"
            x-cloak
            x-data="{
                showRecoveryInput: @js($errors->has('recovery_code')),
                code: '',
                recovery_code: '',
                toggleInput() {
                    this.showRecoveryInput = !this.showRecoveryInput;

                    this.code = '';
                    this.recovery_code = '';

                    $dispatch('clear-2fa-auth-code');

                    $nextTick(() => {
                        this.showRecoveryInput
                            ? this.$refs.recovery_code?.focus()
                            : $dispatch('focus-2fa-auth-code');
                    });
                },
            }"
        >
            <div x-show="!showRecoveryInput">
                <div class="space-y-3">
                    <div class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-indigo-700 dark:border-indigo-900/50 dark:bg-indigo-950/30 dark:text-indigo-300">
                        Two-Factor Access
                    </div>
                    <div class="space-y-1">
                        <flux:heading size="xl">{{ __('Masukkan authentication code') }}</flux:heading>
                        <flux:text class="text-zinc-600 dark:text-zinc-400">
                            {{ __('Gunakan kode dari authenticator untuk mengakses workspace payroll.') }}
                        </flux:text>
                    </div>
                </div>
            </div>

            <div x-show="showRecoveryInput">
                <div class="space-y-3">
                    <div class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-indigo-700 dark:border-indigo-900/50 dark:bg-indigo-950/30 dark:text-indigo-300">
                        Recovery Access
                    </div>
                    <div class="space-y-1">
                        <flux:heading size="xl">{{ __('Gunakan recovery code') }}</flux:heading>
                        <flux:text class="text-zinc-600 dark:text-zinc-400">
                            {{ __('Masukkan salah satu recovery code darurat untuk tetap bisa masuk ke sistem payroll.') }}
                        </flux:text>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('two-factor.login.store') }}">
                @csrf

                <div class="space-y-5 text-center">
                    <div x-show="!showRecoveryInput">
                        <div class="flex items-center justify-center my-5">
                            <flux:otp
                                x-model="code"
                                length="6"
                                name="code"
                                label="OTP Code"
                                label:sr-only
                                class="mx-auto"
                             />
                        </div>
                    </div>

                    <div x-show="showRecoveryInput">
                        <div class="my-5">
                            <flux:input
                                type="text"
                                name="recovery_code"
                                x-ref="recovery_code"
                                x-bind:required="showRecoveryInput"
                                autocomplete="one-time-code"
                                x-model="recovery_code"
                            />
                        </div>

                        @error('recovery_code')
                            <flux:text color="red">
                                {{ $message }}
                            </flux:text>
                        @enderror
                    </div>

                    <flux:button
                        variant="primary"
                        type="submit"
                        class="w-full !rounded-2xl !py-3 text-sm font-semibold"
                    >
                        {{ __('Continue') }}
                    </flux:button>
                </div>

                <div class="mt-5 space-x-0.5 text-sm leading-5 text-center">
                    <span class="opacity-50">{{ __('or you can') }}</span>
                    <div class="inline font-medium underline cursor-pointer opacity-80">
                        <span x-show="!showRecoveryInput" @click="toggleInput()">{{ __('login using a recovery code') }}</span>
                        <span x-show="showRecoveryInput" @click="toggleInput()">{{ __('login using an authentication code') }}</span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts::auth>
