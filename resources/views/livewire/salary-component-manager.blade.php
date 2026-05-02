<div class="space-y-6">
    <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="mb-6 flex flex-col gap-2">
            <flux:heading size="xl">Master Komponen Gaji</flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-300">
                Kelola daftar tunjangan dan potongan yang akan menjadi bahan perhitungan payroll.
            </flux:text>
        </div>

        @if (session()->has('message'))
            <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-300">
                {{ session('message') }}
            </div>
        @endif

        <form wire:submit="save" class="grid gap-4 md:grid-cols-2">
            <div>
                <flux:input wire:model="name" label="Nama Komponen" placeholder="Contoh: Tunjangan Makan" />
                @error('name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <flux:select wire:model="type" label="Jenis Komponen">
                    <option value="earning">Tunjangan / Pendapatan</option>
                    <option value="deduction">Potongan</option>
                </flux:select>
                @error('type') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <flux:select wire:model.boolean="is_fixed" label="Sifat Komponen">
                    <option value="1">Tetap</option>
                    <option value="0">Variabel</option>
                </flux:select>
                @error('is_fixed') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <flux:input wire:model="default_amount" type="number" min="0" step="0.01" label="Nominal Default (Opsional)" placeholder="500000" />
                @error('default_amount') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <flux:input wire:model="formula" label="Formula (Opsional)" placeholder="base_salary * 0.02" />
                @error('formula') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <flux:button type="submit" variant="primary">Simpan Komponen</flux:button>
            </div>
        </form>
    </div>

    <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <flux:heading size="lg">Daftar Komponen Gaji</flux:heading>
                <flux:text class="text-zinc-600 dark:text-zinc-300">
                    Gunakan master ini untuk membangun aturan payroll perusahaan.
                </flux:text>
            </div>

            <div class="w-full md:w-80">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari komponen..." />
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-800/60">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">Nama</th>
                        <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">Jenis</th>
                        <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">Sifat</th>
                        <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">Nominal Default</th>
                        <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">Formula</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($components as $component)
                        <tr class="bg-white dark:bg-zinc-900">
                            <td class="px-4 py-3">{{ $component->name }}</td>
                            <td class="px-4 py-3">
                                {{ $component->type === 'earning' ? 'Pendapatan' : 'Potongan' }}
                            </td>
                            <td class="px-4 py-3">{{ $component->is_fixed ? 'Tetap' : 'Variabel' }}</td>
                            <td class="px-4 py-3">
                                {{ $component->default_amount !== null ? 'Rp '.number_format((float) $component->default_amount, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ $component->formula ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-zinc-500 dark:text-zinc-400">
                                Belum ada komponen gaji.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $components->links() }}
        </div>
    </div>
</div>
