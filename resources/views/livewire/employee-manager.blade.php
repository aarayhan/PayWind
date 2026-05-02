<div class="space-y-6">
    <section class="page-hero">
        <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            <div class="space-y-4">
                <div class="section-label">Employee Master</div>
                <div class="space-y-3">
                    <h1 class="font-display text-4xl font-semibold tracking-tight text-zinc-950 dark:text-white sm:text-5xl">
                        Manajemen Karyawan
                    </h1>
                    <p class="max-w-3xl text-sm leading-7 text-zinc-600 dark:text-zinc-300 sm:text-base">
                        Simpan data induk karyawan, gaji pokok, dan status PTKP sebagai basis utama seluruh proses payroll.
                    </p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <article class="metric-card sm:col-span-1">
                    <div class="text-xs font-semibold uppercase tracking-[0.26em] text-zinc-500 dark:text-zinc-400">Search Ready</div>
                    <div class="mt-4 text-2xl font-semibold text-zinc-950 dark:text-white">Live</div>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Pencarian nama update langsung saat admin mengetik.</p>
                </article>

                <article class="metric-card sm:col-span-1">
                    <div class="text-xs font-semibold uppercase tracking-[0.26em] text-zinc-500 dark:text-zinc-400">Payroll Ready</div>
                    <div class="mt-4 text-2xl font-semibold text-zinc-950 dark:text-white">PTKP</div>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Setiap karyawan langsung siap dipakai untuk PPh 21 TER.</p>
                </article>

                <article class="metric-card sm:col-span-1">
                    <div class="text-xs font-semibold uppercase tracking-[0.26em] text-zinc-500 dark:text-zinc-400">Data Quality</div>
                    <div class="mt-4 text-2xl font-semibold text-zinc-950 dark:text-white">Structured</div>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">NIP, email, status, dan gaji pokok tersimpan dalam format konsisten.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.02fr_1.18fr]">
        <div class="surface-card">
            <div class="mb-6 space-y-2">
                <div class="section-label">New Record</div>
                <h2 class="font-display text-2xl font-semibold text-zinc-950 dark:text-white">Tambah Karyawan</h2>
                <p class="text-sm leading-7 text-zinc-600 dark:text-zinc-300">
                    Masukkan data dasar karyawan sebelum mereka ikut batch payroll.
                </p>
            </div>

            @if (session()->has('message'))
                <div class="mb-5 rounded-[1.4rem] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-200">
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

                <div class="md:col-span-2 pt-2">
                    <flux:button type="submit" variant="primary">Simpan Karyawan</flux:button>
                </div>
            </form>
        </div>

        <div class="surface-card">
            <div class="mb-5 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div class="space-y-2">
                    <div class="section-label">Directory</div>
                    <h2 class="font-display text-2xl font-semibold text-zinc-950 dark:text-white">Daftar Karyawan</h2>
                    <p class="text-sm leading-7 text-zinc-600 dark:text-zinc-300">
                        Cari dan tinjau data karyawan yang sudah tersimpan di master.
                    </p>
                </div>

                <div class="w-full md:w-80">
                    <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari nama karyawan..." />
                </div>
            </div>

            <div class="soft-table overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr>
                            <th>NIP</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Gaji Pokok</th>
                            <th>Status</th>
                            <th>PTKP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employees as $employee)
                            <tr>
                                <td class="font-medium text-zinc-900 dark:text-zinc-100">{{ $employee->nip }}</td>
                                <td>{{ $employee->name }}</td>
                                <td class="text-zinc-500 dark:text-zinc-400">{{ $employee->email }}</td>
                                <td>Rp {{ number_format((float) $employee->base_salary, 0, ',', '.') }}</td>
                                <td>
                                    <span class="soft-pill bg-zinc-100 text-zinc-700 dark:bg-white/10 dark:text-zinc-200">{{ ucfirst($employee->status) }}</span>
                                </td>
                                <td>{{ $employee->ptkp_status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-zinc-500 dark:text-zinc-400">
                                    Belum ada data karyawan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                {{ $employees->links() }}
            </div>
        </div>
    </section>
</div>
