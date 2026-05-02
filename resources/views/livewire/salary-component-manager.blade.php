<div class="space-y-6">
    <section class="page-hero">
        <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            <div class="space-y-4">
                <div class="section-label">Salary Components</div>
                <div class="space-y-3">
                    <h1 class="font-display text-4xl font-semibold tracking-tight text-zinc-950 dark:text-white sm:text-5xl">
                        Master Komponen Gaji
                    </h1>
                    <p class="max-w-3xl text-sm leading-7 text-zinc-600 dark:text-zinc-300 sm:text-base">
                        Kelola daftar tunjangan dan potongan sebagai fondasi engine payroll, BPJS, dan pajak.
                    </p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <article class="metric-card">
                    <div class="text-xs font-semibold uppercase tracking-[0.26em] text-zinc-500 dark:text-zinc-400">Flexible Type</div>
                    <div class="mt-4 text-2xl font-semibold text-zinc-950 dark:text-white">Earning</div>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Dukung pendapatan dan deduction dalam satu master data.</p>
                </article>

                <article class="metric-card">
                    <div class="text-xs font-semibold uppercase tracking-[0.26em] text-zinc-500 dark:text-zinc-400">Default Amount</div>
                    <div class="mt-4 text-2xl font-semibold text-zinc-950 dark:text-white">Preset</div>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Nominal default bisa langsung dipakai saat generate batch.</p>
                </article>

                <article class="metric-card">
                    <div class="text-xs font-semibold uppercase tracking-[0.26em] text-zinc-500 dark:text-zinc-400">Rule Ready</div>
                    <div class="mt-4 text-2xl font-semibold text-zinc-950 dark:text-white">Formula</div>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Sediakan ruang untuk formula payroll yang lebih lanjut.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.02fr_1.18fr]">
        <div class="surface-card">
            <div class="mb-6 space-y-2">
                <div class="section-label">Create Component</div>
                <h2 class="font-display text-2xl font-semibold text-zinc-950 dark:text-white">Tambah Komponen</h2>
                <p class="text-sm leading-7 text-zinc-600 dark:text-zinc-300">
                    Bangun daftar komponen gaji yang akan dipakai untuk perhitungan payroll perusahaan.
                </p>
            </div>

            @if (session()->has('message'))
                <div class="mb-5 rounded-[1.4rem] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-200">
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

                <div class="md:col-span-2">
                    <flux:input wire:model="formula" label="Formula (Opsional)" placeholder="base_salary * 0.02" />
                    @error('formula') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2 pt-2">
                    <flux:button type="submit" variant="primary">Simpan Komponen</flux:button>
                </div>
            </form>
        </div>

        <div class="surface-card">
            <div class="mb-5 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div class="space-y-2">
                    <div class="section-label">Registry</div>
                    <h2 class="font-display text-2xl font-semibold text-zinc-950 dark:text-white">Daftar Komponen Gaji</h2>
                    <p class="text-sm leading-7 text-zinc-600 dark:text-zinc-300">
                        Gunakan master ini untuk membangun aturan pendapatan dan potongan payroll.
                    </p>
                </div>

                <div class="w-full md:w-80">
                    <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari komponen..." />
                </div>
            </div>

            <div class="soft-table overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Jenis</th>
                            <th>Sifat</th>
                            <th>Nominal Default</th>
                            <th>Formula</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($components as $component)
                            <tr>
                                <td class="font-medium text-zinc-900 dark:text-zinc-100">{{ $component->name }}</td>
                                <td>
                                    <span class="soft-pill {{ $component->type === 'earning' ? 'bg-sky-100 text-sky-700 dark:bg-sky-500/15 dark:text-sky-200' : 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-200' }}">
                                        {{ $component->type === 'earning' ? 'Pendapatan' : 'Potongan' }}
                                    </span>
                                </td>
                                <td>{{ $component->is_fixed ? 'Tetap' : 'Variabel' }}</td>
                                <td>
                                    {{ $component->default_amount !== null ? 'Rp '.number_format((float) $component->default_amount, 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-zinc-500 dark:text-zinc-400">{{ $component->formula ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-zinc-500 dark:text-zinc-400">
                                    Belum ada komponen gaji.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                {{ $components->links() }}
            </div>
        </div>
    </section>
</div>
