<div class="space-y-6">
    <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="mb-6 flex flex-col gap-2">
            <flux:heading size="xl">Manajemen Karyawan</flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-300">
                Simpan data induk karyawan yang akan dipakai sebagai dasar proses payroll.
            </flux:text>
        </div>

        @if (session()->has('message'))
            <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-300">
                {{ session('message') }}
            </div>
        @endif

        <form wire:submit="save" class="grid gap-4 md:grid-cols-2">
            <div>
                <flux:input wire:model="nip" label="NIP" placeholder="Contoh: 2024001" />
                @error('nip') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <flux:input wire:model="name" label="Nama Lengkap" placeholder="Nama karyawan" />
                @error('name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <flux:input wire:model="email" type="email" label="Email" placeholder="nama@perusahaan.com" />
                @error('email') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <flux:input wire:model="base_salary" type="number" min="0" step="0.01" label="Gaji Pokok" placeholder="5000000" />
                @error('base_salary') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <flux:input wire:model="joined_at" type="date" label="Tanggal Bergabung" />
                @error('joined_at') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <flux:select wire:model="status" label="Status Karyawan">
                    <option value="permanent">Tetap</option>
                    <option value="contract">Kontrak</option>
                    <option value="intern">Intern</option>
                </flux:select>
            </div>

            <div class="md:col-span-2">
                <flux:select wire:model="ptkp_status" label="Status PTKP">
                    <option value="TK/0">TK/0</option>
                    <option value="TK/1">TK/1</option>
                    <option value="TK/2">TK/2</option>
                    <option value="TK/3">TK/3</option>
                    <option value="K/0">K/0</option>
                    <option value="K/1">K/1</option>
                    <option value="K/2">K/2</option>
                    <option value="K/3">K/3</option>
                    <option value="KI/0">KI/0</option>
                    <option value="KI/1">KI/1</option>
                    <option value="KI/2">KI/2</option>
                    <option value="KI/3">KI/3</option>
                </flux:select>
                @error('ptkp_status') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <flux:button type="submit" variant="primary">Simpan Karyawan</flux:button>
            </div>
        </form>
    </div>

    <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <flux:heading size="lg">Daftar Karyawan</flux:heading>
                <flux:text class="text-zinc-600 dark:text-zinc-300">
                    Cari dan tinjau data karyawan yang sudah terdaftar.
                </flux:text>
            </div>

            <div class="w-full md:w-80">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari nama karyawan..." />
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-800/60">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">NIP</th>
                        <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">Nama</th>
                        <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">Email</th>
                        <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">Gaji Pokok</th>
                        <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">Status</th>
                        <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">PTKP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($employees as $employee)
                        <tr class="bg-white dark:bg-zinc-900">
                            <td class="px-4 py-3">{{ $employee->nip }}</td>
                            <td class="px-4 py-3">{{ $employee->name }}</td>
                            <td class="px-4 py-3">{{ $employee->email }}</td>
                            <td class="px-4 py-3">Rp {{ number_format((float) $employee->base_salary, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 capitalize">{{ $employee->status }}</td>
                            <td class="px-4 py-3">{{ $employee->ptkp_status }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-zinc-500 dark:text-zinc-400">
                                Belum ada data karyawan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $employees->links() }}
        </div>
    </div>
</div>
