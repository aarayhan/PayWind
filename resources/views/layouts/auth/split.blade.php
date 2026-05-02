<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-100 antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
            <div class="relative hidden h-full flex-col overflow-hidden p-10 text-white lg:flex dark:border-e dark:border-neutral-800">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(56,189,248,0.22),_transparent_30%),linear-gradient(160deg,#0f172a_0%,#111827_45%,#18181b_100%)]"></div>
                <div class="absolute inset-0 opacity-40 [background-image:linear-gradient(rgba(255,255,255,0.06)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.06)_1px,transparent_1px)] [background-size:72px_72px]"></div>
                <a href="{{ route('home') }}" class="relative z-20 flex items-center text-lg font-medium" wire:navigate>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl border border-white/15 bg-white/10 backdrop-blur">
                        <x-app-logo-icon class="me-2 h-7 fill-current text-white" />
                    </span>
                    <span class="ms-3">
                        {{ config('app.name', 'Laravel') }}
                    </span>
                </a>

                <div class="relative z-20 mt-auto">
                    <div class="max-w-xl space-y-8">
                        <div class="space-y-4">
                            <div class="inline-flex items-center gap-2 rounded-full border border-sky-300/20 bg-sky-300/10 px-4 py-1.5 text-sm font-medium text-sky-100">
                                Internal Payroll Workspace
                            </div>
                            <h1 class="text-4xl font-semibold leading-tight tracking-tight text-white">
                                Kendalikan payroll, pajak, dan benefit dari satu workspace yang rapi.
                            </h1>
                            <p class="max-w-lg text-base leading-7 text-zinc-300">
                                Kelola batch payroll, komponen gaji, BPJS, PPh 21 TER, audit trail, dan role akses tanpa keluar dari dashboard.
                            </p>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-3">
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-sm">
                                <div class="text-xs font-medium uppercase tracking-[0.24em] text-zinc-400">Batch</div>
                                <div class="mt-2 text-lg font-semibold text-white">Payroll Processor</div>
                                <div class="mt-1 text-sm text-zinc-300">Generate slip gaji bulanan dengan snapshot aman.</div>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-sm">
                                <div class="text-xs font-medium uppercase tracking-[0.24em] text-zinc-400">Tax</div>
                                <div class="mt-2 text-lg font-semibold text-white">TER Engine</div>
                                <div class="mt-1 text-sm text-zinc-300">Aturan pajak dan BPJS dinamis, bukan hardcoded.</div>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-sm">
                                <div class="text-xs font-medium uppercase tracking-[0.24em] text-zinc-400">Control</div>
                                <div class="mt-2 text-lg font-semibold text-white">Audit Ready</div>
                                <div class="mt-1 text-sm text-zinc-300">Semua perubahan rule payroll bisa ditelusuri.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full lg:p-8">
                <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[420px]">
                    <a href="{{ route('home') }}" class="z-20 flex flex-col items-center gap-2 font-medium lg:hidden" wire:navigate>
                        <span class="flex h-10 w-10 items-center justify-center rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                            <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" />
                        </span>

                        <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                    </a>
                    {{ $slot }}
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
