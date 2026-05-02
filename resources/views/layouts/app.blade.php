<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main class="px-2 pb-2 pt-1 lg:px-4 lg:pb-4">
        {{ $slot }}
    </flux:main>
</x-layouts::app.sidebar>
