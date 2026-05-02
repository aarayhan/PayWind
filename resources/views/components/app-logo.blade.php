@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="PayWind" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-9 items-center justify-center rounded-2xl bg-sky-600 text-white shadow-sm">
            <x-app-logo-icon class="size-5 fill-current text-white" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="PayWind" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-9 items-center justify-center rounded-2xl bg-sky-600 text-white shadow-sm">
            <x-app-logo-icon class="size-5 fill-current text-white" />
        </x-slot>
    </flux:brand>
@endif
