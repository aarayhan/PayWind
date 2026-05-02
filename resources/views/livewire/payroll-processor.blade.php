<div
    class="space-y-6"
    x-data="{ confirmBatchId: null }"
    @close-confirm.window="confirmBatchId = null"
>
    <section class="page-hero">
        <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <div class="space-y-4">
                <div class="section-label">Payroll Generation</div>
                <div class="space-y-3">
                    <h1 class="font-display text-4xl font-semibold tracking-tight text-zinc-950 dark:text-white sm:text-5xl">
                        Proses Payroll
                    </h1>
                    <p class="max-w-3xl text-sm leading-7 text-zinc-600 dark:text-zinc-300 sm:text-base">
                        Buat batch payroll per periode, jalankan generation slip gaji, lalu tinjau breakdown BPJS dan PPh 21 secara transparan.
                    </p>
                </div>
            </div>

            <div class="surface-card space-y-5 bg-white/75 dark:bg-white/5">
                <div class="space-y-2">
                    <div class="section-label">Control Panel</div>
                    <h2 class="font-display text-2xl font-semibold text-zinc-950 dark:text-white">Mulai Batch Baru</h2>
                    <p class="text-sm leading-7 text-zinc-600 dark:text-zinc-300">
                        Buat periode payroll berikutnya lalu proses snapshot gaji untuk seluruh karyawan.
                    </p>
                </div>

                <flux:modal.trigger name="new-payroll-batch">
                    <flux:button variant="primary">New Payroll Batch</flux:button>
                </flux:modal.trigger>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="rounded-[1.4rem] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-200">
                {{ session('message') }}
            </div>
        @endif

        @error('batch')
            <div class="rounded-[1.4rem] border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-200">
                {{ $message }}
            </div>
        @enderror
    </section>

    @if ($processingBatch)
        <div wire:poll.750ms="continueProcessing" class="surface-card border-sky-200 bg-sky-50/90 dark:border-sky-500/20 dark:bg-sky-500/10">
            <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div class="space-y-2">
                    <div class="section-label text-sky-700 dark:text-sky-200">Live Progress</div>
                    <h2 class="font-display text-2xl font-semibold text-sky-950 dark:text-sky-100">Processing {{ $processingBatch->month }}</h2>
                    <p class="text-sm text-sky-800 dark:text-sky-200">
                        {{ $processingBatch->processed_employees }} dari {{ $processingBatch->total_employees }} karyawan telah diproses.
                    </p>
                </div>

                <div class="soft-pill bg-white text-sky-700 dark:bg-sky-950/40 dark:text-sky-200">
                    {{ $processingBatch->progress_percentage }}%
                </div>
            </div>

            <div class="h-3 overflow-hidden rounded-full bg-sky-100 dark:bg-sky-950/40">
                <div
                    class="h-full rounded-full bg-sky-500 transition-all duration-500"
                    style="width: {{ $processingBatch->progress_percentage }}%"
                ></div>
            </div>
        </div>
    @endif

    <section class="surface-card">
        <div class="mb-5 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div class="space-y-2">
                <div class="section-label">Batch Register</div>
                <h2 class="font-display text-2xl font-semibold text-zinc-950 dark:text-white">Daftar Batch Payroll</h2>
                <p class="text-sm leading-7 text-zinc-600 dark:text-zinc-300">
                    Riwayat batch payroll dan status generation untuk tiap periode penggajian.
                </p>
            </div>
        </div>

        <div class="soft-table overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th>Slip</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($batches as $batch)
                        <tr>
                            <td>
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $batch->month }}</div>
                                <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $batch->description }}</div>
                            </td>
                            <td>
                                <span class="soft-pill
                                    {{ $batch->status === 'processed' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-200' : '' }}
                                    {{ $batch->status === 'draft' ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-200' : '' }}
                                    {{ $batch->status === 'approved' ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-200' : '' }}
                                    {{ $batch->status === 'paid' ? 'bg-zinc-200 text-zinc-700 dark:bg-zinc-700/50 dark:text-zinc-200' : '' }}">
                                    {{ ucfirst($batch->status) }}
                                </span>
                            </td>
                            <td class="text-zinc-600 dark:text-zinc-300">
                                {{ $batch->processed_employees }}/{{ $batch->total_employees }}
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">({{ $batch->progress_percentage }}%)</span>
                            </td>
                            <td class="font-medium text-zinc-900 dark:text-zinc-100">{{ $batch->payroll_details_count }}</td>
                            <td>
                                @if ($batch->status === 'draft' && $batch->payroll_details_count === 0)
                                    <button
                                        type="button"
                                        class="inline-flex rounded-[1rem] bg-zinc-950 px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-800 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-white dark:text-zinc-950 dark:hover:bg-zinc-200"
                                        @click="confirmBatchId = {{ $batch->id }}"
                                        @if ($activeBatchId !== null && $activeBatchId !== $batch->id) disabled @endif
                                    >
                                        Generate
                                    </button>
                                @else
                                    <div class="flex flex-wrap items-center gap-2">
                                        <flux:button size="sm" variant="filled" wire:click="showBatch({{ $batch->id }})">Lihat Detail</flux:button>
                                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Generate selesai atau terkunci</span>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-zinc-500 dark:text-zinc-400">
                                Belum ada batch payroll.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if ($selectedBatch)
        <section class="surface-card">
            <div class="mb-5 space-y-2">
                <div class="section-label">Batch Breakdown</div>
                <h2 class="font-display text-2xl font-semibold text-zinc-950 dark:text-white">Detail Hasil Generate {{ $selectedBatch->month }}</h2>
                <p class="text-sm leading-7 text-zinc-600 dark:text-zinc-300">
                    Breakdown earning, potongan karyawan, beban perusahaan, dan take home pay per karyawan.
                </p>
            </div>

            <div class="space-y-4">
                @foreach ($selectedBatch->payrollDetails as $detail)
                    <div x-data="{ open: false }" class="rounded-[1.7rem] border border-zinc-200 bg-white/80 p-5 shadow-sm dark:border-zinc-700 dark:bg-white/5">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="space-y-2">
                                <div class="font-semibold text-zinc-950 dark:text-zinc-100">{{ $detail->employee->name }}</div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $detail->employee->nip }} • PTKP {{ $detail->employee->ptkp_status }}
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <div class="soft-pill bg-zinc-100 text-zinc-700 dark:bg-white/10 dark:text-zinc-200">
                                    THP: Rp {{ number_format((float) $detail->take_home_pay, 0, ',', '.') }}
                                </div>
                                <flux:button size="sm" variant="filled" wire:click="recalculateTax({{ $detail->id }})">
                                    Recalculate Tax
                                </flux:button>
                                <button type="button" class="text-sm font-medium text-sky-600 dark:text-sky-300" @click="open = !open">
                                    <span x-text="open ? 'Sembunyikan' : 'Lihat Breakdown'"></span>
                                </button>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                            <div class="metric-card !rounded-[1.4rem] !p-4">
                                <div class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Gaji Pokok</div>
                                <div class="mt-3 text-lg font-semibold text-zinc-950 dark:text-white">Rp {{ number_format((float) $detail->base_salary_snapshot, 0, ',', '.') }}</div>
                            </div>
                            <div class="metric-card !rounded-[1.4rem] !p-4">
                                <div class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Bruto</div>
                                <div class="mt-3 text-lg font-semibold text-zinc-950 dark:text-white">Rp {{ number_format((float) $detail->gross_earning, 0, ',', '.') }}</div>
                            </div>
                            <div class="metric-card !rounded-[1.4rem] !p-4">
                                <div class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">BPJS Karyawan</div>
                                <div class="mt-3 text-lg font-semibold text-zinc-950 dark:text-white">Rp {{ number_format((float) $detail->employee_benefit_total, 0, ',', '.') }}</div>
                            </div>
                            <div class="metric-card !rounded-[1.4rem] !p-4">
                                <div class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">PPh 21</div>
                                <div class="mt-3 text-lg font-semibold text-zinc-950 dark:text-white">Rp {{ number_format((float) $detail->pph21_amount, 0, ',', '.') }}</div>
                            </div>
                        </div>

                        <div x-show="open" x-transition.opacity class="mt-4 grid gap-4 lg:grid-cols-3">
                            <div class="rounded-[1.5rem] border border-zinc-200 bg-zinc-50/90 p-4 dark:border-zinc-700 dark:bg-zinc-800/60">
                                <div class="mb-3 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Pendapatan</div>
                                <div class="space-y-2 text-sm">
                                    @foreach ($detail->items->where('component_type', 'earning') as $item)
                                        <div class="flex justify-between gap-3">
                                            <span>{{ $item->component_name }}</span>
                                            <span>Rp {{ number_format((float) $item->amount, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="rounded-[1.5rem] border border-rose-200 bg-rose-50/90 p-4 dark:border-rose-500/20 dark:bg-rose-500/10">
                                <div class="mb-3 text-sm font-semibold text-rose-700 dark:text-rose-200">Potongan Karyawan</div>
                                <div class="space-y-2 text-sm">
                                    @foreach ($detail->items->where('component_type', 'deduction')->where('paid_by', 'employee') as $item)
                                        <div class="flex justify-between gap-3">
                                            <span>{{ $item->component_name }}</span>
                                            <span>Rp {{ number_format((float) $item->amount, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="rounded-[1.5rem] border border-amber-200 bg-amber-50/90 p-4 dark:border-amber-500/20 dark:bg-amber-500/10">
                                <div class="mb-3 text-sm font-semibold text-amber-700 dark:text-amber-200">Beban Perusahaan</div>
                                <div class="space-y-2 text-sm">
                                    @foreach ($detail->items->where('component_type', 'deduction')->where('paid_by', 'employer') as $item)
                                        <div class="flex justify-between gap-3">
                                            <span>{{ $item->component_name }}</span>
                                            <span>Rp {{ number_format((float) $item->amount, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <flux:modal name="new-payroll-batch" class="max-w-lg">
        <form wire:submit="createBatch" class="space-y-6">
            <div>
                <flux:heading size="lg">Buat Payroll Batch Baru</flux:heading>
                <flux:text>Pilih bulan dan tahun periode penggajian.</flux:text>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <flux:select wire:model="batchMonth" label="Bulan">
                        @for ($month = 1; $month <= 12; $month++)
                            <option value="{{ $month }}">{{ str_pad((string) $month, 2, '0', STR_PAD_LEFT) }}</option>
                        @endfor
                    </flux:select>
                    @error('batchMonth') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <flux:input wire:model="batchYear" type="number" min="2024" max="2100" label="Tahun" />
                    @error('batchYear') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="filled">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Simpan Batch</flux:button>
            </div>
        </form>
    </flux:modal>

    <div
        x-cloak
        x-show="confirmBatchId !== null"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/55 px-4"
    >
        <div
            @click.outside="confirmBatchId = null"
            class="w-full max-w-lg rounded-[2rem] border border-zinc-200 bg-white p-6 shadow-2xl dark:border-zinc-700 dark:bg-zinc-900"
        >
            <div class="space-y-3">
                <flux:heading size="lg">Konfirmasi Generate Payroll</flux:heading>
                <flux:text class="text-zinc-600 dark:text-zinc-300">
                    Proses ini akan membuat snapshot payroll untuk semua karyawan pada batch terpilih. Pastikan Anda tidak menjalankan generate dua kali.
                </flux:text>
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <button
                    type="button"
                    class="inline-flex rounded-[1rem] border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-600 dark:text-zinc-200 dark:hover:bg-zinc-800"
                    @click="confirmBatchId = null"
                >
                    Batal
                </button>
                <button
                    type="button"
                    class="inline-flex rounded-[1rem] bg-zinc-950 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 dark:hover:bg-zinc-200"
                    @click="$wire.processBatch(confirmBatchId); confirmBatchId = null"
                >
                    Ya, Generate
                </button>
            </div>
        </div>
    </div>
</div>
