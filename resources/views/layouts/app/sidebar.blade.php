<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen">
        <div class="mx-auto min-h-screen max-w-[1800px] px-3 py-3 lg:px-4">
        <flux:sidebar sticky collapsible="mobile" class="app-shell border-e-0 bg-[linear-gradient(180deg,rgba(255,255,255,0.94)_0%,rgba(246,250,255,0.94)_52%,rgba(240,253,250,0.9)_100%)] dark:bg-[linear-gradient(180deg,rgba(9,9,11,0.94)_0%,rgba(17,24,39,0.94)_52%,rgba(6,78,59,0.28)_100%)]">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="users" :href="route('employees.index')" :current="request()->routeIs('employees.*')" wire:navigate>
                        {{ __('Karyawan') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="wallet" :href="route('salary-components.index')" :current="request()->routeIs('salary-components.*')" wire:navigate>
                        {{ __('Komponen Gaji') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="calculator" :href="route('payroll-batches.index')" :current="request()->routeIs('payroll-batches.*')" wire:navigate>
                        {{ __('Proses Payroll') }}
                    </flux:sidebar.item>
                    @can('access-payroll-settings')
                        <flux:sidebar.item icon="cog-6-tooth" :href="route('payroll-settings.index')" :current="request()->routeIs('payroll-settings.*')" wire:navigate>
                            {{ __('Payroll Settings') }}
                        </flux:sidebar.item>
                    @endcan
                    @can('manage-user-roles')
                        <flux:sidebar.item icon="shield-check" :href="route('user-roles.index')" :current="request()->routeIs('user-roles.*')" wire:navigate>
                            {{ __('User Roles') }}
                        </flux:sidebar.item>
                    @endcan
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <div class="mx-3 mb-3 rounded-[1.5rem] border border-white/60 bg-white/75 p-4 text-sm shadow-sm backdrop-blur dark:border-white/6 dark:bg-zinc-900/60">
                <div class="mb-1 font-semibold text-zinc-900 dark:text-zinc-100">PayWind Workspace</div>
                <div class="text-zinc-500 dark:text-zinc-400">
                    Payroll, tax, BPJS, dan audit trail dalam satu panel operasional.
                </div>
            </div>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
