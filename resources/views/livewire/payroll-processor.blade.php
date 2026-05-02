<div
    class="space-y-6"
    x-data="{ confirmBatchId: null }"
    @close-confirm.window="confirmBatchId = null"
>
    <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div class="space-y-2">
                <flux:heading size="xl">Proses Payroll</flux:heading>
                <flux:text class="text-zinc-600 dark:text-zinc-300">
                    Buat batch payroll per periode, lalu generate slip gaji secara bertahap dengan progress real-time.
                </flux:text>
            </div>

            <flux:modal.trigger name="new-payroll-batch">
                <flux:button variant="primary">New Payroll Batch</flux:button>
            </flux:modal.trigger>
        </div>

        @if (session()->has('message'))
            <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-300">
                {{ session('message') }}
            </div>
        @endif

        @error('batch')
            <div class="mt-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/50 dark:bg-rose-950/40 dark:text-rose-300">
                {{ $message }}
            </div>
        @enderror
    </div>

    @if ($processingBatch)
        <div wire:poll.750ms="continueProcessing" class="rounded-3xl border border-sky-200 bg-sky-50 p-6 shadow-sm dark:border-sky-900/40 dark:bg-sky-950/30">
            <div class="mb-4 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <flux:heading size="lg">Processing {{ $processingBatch->month }}</flux:heading>
                    <flux:text class="text-sky-800 dark:text-sky-200">
                        {{ $processingBatch->processed_employees }} dari {{ $processingBatch->total_employees }} karyawan telah diproses.
                    </flux:text>
                </div>

                <div class="text-sm font-medium text-sky-700 dark:text-sky-300">
                    {{ $processingBatch->progress_percentage }}%
                </div>
            </div>

            <div class="h-3 overflow-hidden rounded-full bg-sky-100 dark:bg-sky-900/50">
                <div
                    class="h-full rounded-full bg-sky-500 transition-all duration-500"
                    style="width: {{ $processingBatch->progress_percentage }}%"
                ></div>
            </div>
        </div>
    @endif

    <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="mb-4">
            <flux:heading size="lg">Daftar Batch Payroll</flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-300">
                Riwayat batch payroll dan status proses generation untuk tiap periode.
            </flux:text>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-800/60">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">Periode</th>
                        <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">Status</th>
                        <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">Progress</th>
                        <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">Slip</th>
                        <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($batches as $batch)
                        <tr class="bg-white dark:bg-zinc-900">
                            <td class="px-4 py-3">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $batch->month }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $batch->description }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-medium
                                    {{ $batch->status === 'processed' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300' : '' }}
                                    {{ $batch->status === 'draft' ? 'bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300' : '' }}
                                    {{ $batch->status === 'approved' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300' : '' }}
                                    {{ $batch->status === 'paid' ? 'bg-zinc-200 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300' : '' }}">
                                    {{ ucfirst($batch->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                {{ $batch->processed_employees }}/{{ $batch->total_employees }}
                                <span class="text-zinc-500 dark:text-zinc-400">({{ $batch->progress_percentage }}%)</span>
                            </td>
                            <td class="px-4 py-3">{{ $batch->payroll_details_count }}</td>
                            <td class="px-4 py-3">
                                @if ($batch->status === 'draft' && $batch->payroll_details_count === 0)
                                    <button
                                        type="button"
                                        class="inline-flex rounded-xl bg-zinc-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-700 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-300"
                                        @click="confirmBatchId = {{ $batch->id }}"
                                        @if ($activeBatchId !== null && $activeBatchId !== $batch->id) disabled @endif
                                    >
                                        Generate
                                    </button>
                                @else
                                    <div class="flex items-center gap-2">
                                        <flux:button size="sm" variant="filled" wire:click="showBatch({{ $batch->id }})">Lihat Detail</flux:button>
                                        <span class="text-zinc-500 dark:text-zinc-400">Generate selesai / terkunci</span>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-zinc-500 dark:text-zinc-400">
                                Belum ada batch payroll.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($selectedBatch)
        <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mb-4">
                <flux:heading size="lg">Detail Hasil Generate {{ $selectedBatch->month }}</flux:heading>
                <flux:text class="text-zinc-600 dark:text-zinc-300">
                    Breakdown earning, BPJS, dan PPh 21 per karyawan.
                </flux:text>
            </div>

            <div class="space-y-4">
                @foreach ($selectedBatch->payrollDetails as $detail)
                    <div x-data="{ open: false }" class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                            <div>
                                <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $detail->employee->name }}</div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $detail->employee->nip }} • PTKP {{ $detail->employee->ptkp_status }}
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <div class="rounded-xl bg-zinc-100 px-3 py-2 text-sm dark:bg-zinc-800">
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

                        <div x-show="open" x-transition.opacity class="mt-4 grid gap-4 lg:grid-cols-3">
                            <div class="rounded-2xl bg-zinc-50 p-4 dark:bg-zinc-800/70">
                                <div class="mb-2 text-sm font-semibold text-zinc-700 dark:text-zinc-200">Pendapatan</div>
                                <div class="space-y-2 text-sm">
                                    @foreach ($detail->items->where('component_type', 'earning') as $item)
                                        <div class="flex justify-between gap-3">
                                            <span>{{ $item->component_name }}</span>
                                            <span>Rp {{ number_format((float) $item->amount, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="rounded-2xl bg-rose-50 p-4 dark:bg-rose-950/20">
                                <div class="mb-2 text-sm font-semibold text-rose-700 dark:text-rose-200">Potongan Karyawan</div>
                                <div class="space-y-2 text-sm">
                                    @foreach ($detail->items->where('component_type', 'deduction')->where('paid_by', 'employee') as $item)
                                        <div class="flex justify-between gap-3">
                                            <span>{{ $item->component_name }}</span>
                                            <span>Rp {{ number_format((float) $item->amount, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="rounded-2xl bg-amber-50 p-4 dark:bg-amber-950/20">
                                <div class="mb-2 text-sm font-semibold text-amber-700 dark:text-amber-200">Beban Perusahaan</div>
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
        </div>
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
            class="w-full max-w-lg rounded-3xl border border-zinc-200 bg-white p-6 shadow-2xl dark:border-zinc-700 dark:bg-zinc-900"
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
                    class="inline-flex rounded-xl border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-600 dark:text-zinc-200 dark:hover:bg-zinc-800"
                    @click="confirmBatchId = null"
                >
                    Batal
                </button>
                <button
                    type="button"
                    class="inline-flex rounded-xl bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-700 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-300"
                    @click="$wire.processBatch(confirmBatchId); confirmBatchId = null"
                >
                    Ya, Generate
                </button>
            </div>
        </div>
    </div>
</div>
