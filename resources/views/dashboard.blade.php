<x-layouts::app :title="__('Dashboard')">
    <div class="flex w-full flex-1 flex-col gap-6">
        <section class="overflow-hidden rounded-[2rem] border border-zinc-200 bg-[radial-gradient(circle_at_top_left,_rgba(56,189,248,0.18),_transparent_25%),linear-gradient(135deg,#ffffff_0%,#f8fafc_52%,#eef2ff_100%)] p-6 shadow-sm dark:border-zinc-700 dark:bg-[radial-gradient(circle_at_top_left,_rgba(56,189,248,0.15),_transparent_20%),linear-gradient(135deg,#09090b_0%,#111827_48%,#0f172a_100%)]">
            <div class="grid gap-6 xl:grid-cols-[1.35fr_0.9fr]">
                <div class="space-y-6">
                    <div class="space-y-3">
                        <div class="inline-flex items-center gap-2 rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-sky-700 dark:border-sky-900/50 dark:bg-sky-950/30 dark:text-sky-300">
                            Payroll Operations
                        </div>
                        <div class="space-y-2">
                            <flux:heading size="xl">{{ __('Dashboard Payroll') }}</flux:heading>
                            <flux:text class="max-w-2xl text-zinc-600 dark:text-zinc-300">
                                {{ __('Pantau batch gaji, master komponen, pajak, dan kesiapan operasional payroll dari satu tampilan utama.') }}
                            </flux:text>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="rounded-3xl border border-zinc-200 bg-white/85 p-5 shadow-sm backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/60">
                            <div class="text-xs font-medium uppercase tracking-[0.24em] text-zinc-500 dark:text-zinc-400">Karyawan</div>
                            <div class="mt-3 text-3xl font-semibold text-zinc-950 dark:text-zinc-50">{{ number_format($employeeCount) }}</div>
                            <div class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Data induk yang siap diproses.</div>
                        </div>

                        <div class="rounded-3xl border border-zinc-200 bg-white/85 p-5 shadow-sm backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/60">
                            <div class="text-xs font-medium uppercase tracking-[0.24em] text-zinc-500 dark:text-zinc-400">Komponen Tetap</div>
                            <div class="mt-3 text-3xl font-semibold text-zinc-950 dark:text-zinc-50">{{ number_format($fixedComponentCount) }}</div>
                            <div class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Pendapatan default untuk basis payroll.</div>
                        </div>

                        <div class="rounded-3xl border border-zinc-200 bg-white/85 p-5 shadow-sm backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/60">
                            <div class="text-xs font-medium uppercase tracking-[0.24em] text-zinc-500 dark:text-zinc-400">Potongan</div>
                            <div class="mt-3 text-3xl font-semibold text-zinc-950 dark:text-zinc-50">{{ number_format($deductionComponentCount) }}</div>
                            <div class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Komponen deduction yang tercatat.</div>
                        </div>

                        <div class="rounded-3xl border border-zinc-200 bg-white/85 p-5 shadow-sm backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/60">
                            <div class="text-xs font-medium uppercase tracking-[0.24em] text-zinc-500 dark:text-zinc-400">Batch Processed</div>
                            <div class="mt-3 text-3xl font-semibold text-zinc-950 dark:text-zinc-50">{{ number_format($processedBatchCount) }}</div>
                            <div class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Batch yang selesai dihitung.</div>
                        </div>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-zinc-200 bg-white/90 p-6 shadow-sm backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/70">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-xs font-medium uppercase tracking-[0.24em] text-zinc-500 dark:text-zinc-400">Operational Status</div>
                            <div class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-zinc-50">
                                {{ $processingBatch ? 'Payroll Sedang Berjalan' : 'Tidak Ada Batch Aktif' }}
                            </div>
                        </div>

                        <div class="rounded-2xl bg-zinc-100 px-3 py-2 text-sm font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                            {{ $processingBatch ? $processingBatch->progress_percentage.'%' : 'Standby' }}
                        </div>
                    </div>

                    @if ($processingBatch)
                        <div class="mt-6 space-y-3">
                            <div class="text-sm text-zinc-600 dark:text-zinc-300">
                                Batch {{ $processingBatch->month }} sedang memproses {{ $processingBatch->processed_employees }} dari {{ $processingBatch->total_employees }} karyawan.
                            </div>
                            <div class="h-3 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                                <div class="h-full rounded-full bg-sky-500 transition-all duration-500" style="width: {{ $processingBatch->progress_percentage }}%"></div>
                            </div>
                        </div>
                    @else
                        <div class="mt-6 rounded-3xl border border-dashed border-zinc-300 px-4 py-5 text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                            Tidak ada batch draft yang sedang berjalan saat ini.
                        </div>
                    @endif

                    <div class="mt-6 grid gap-3">
                        <flux:button variant="primary" :href="route('payroll-batches.index')" wire:navigate>
                            {{ __('Buka Proses Payroll') }}
                        </flux:button>
                        <flux:button variant="filled" :href="route('employees.index')" wire:navigate>
                            {{ __('Kelola Karyawan') }}
                        </flux:button>
                        @can('access-payroll-settings')
                            <flux:button variant="ghost" :href="route('payroll-settings.index')" wire:navigate>
                                {{ __('Atur Rule Payroll') }}
                            </flux:button>
                        @endcan
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.25fr_0.95fr]">
            <div class="rounded-[2rem] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <flux:heading size="lg">{{ __('Riwayat Batch Payroll') }}</flux:heading>
                        <flux:text class="text-zinc-600 dark:text-zinc-300">
                            {{ __('Snapshot batch terbaru untuk memantau progres payroll bulanan.') }}
                        </flux:text>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/60">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">Periode</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">Status</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">Progress</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">Slip</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @forelse ($recentBatches as $batch)
                                <tr>
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
                                    <td class="px-4 py-3">{{ $batch->processed_employees }}/{{ $batch->total_employees }} ({{ $batch->progress_percentage }}%)</td>
                                    <td class="px-4 py-3">{{ $batch->payroll_details_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-zinc-500 dark:text-zinc-400">
                                        Belum ada batch payroll yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-[2rem] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="mb-4">
                        <flux:heading size="lg">{{ __('Trend Batch Terbaru') }}</flux:heading>
                        <flux:text class="text-zinc-600 dark:text-zinc-300">
                            {{ __('Visual ringan untuk melihat volume slip dan status batch terakhir.') }}
                        </flux:text>
                    </div>

                    @if (count($batchTrend) > 0)
                        @php
                            $maxSlips = max(1, collect($batchTrend)->max('slips'));
                        @endphp

                        <div class="space-y-4">
                            @foreach ($batchTrend as $trend)
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-sm">
                                        <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $trend['month'] }}</div>
                                        <div class="text-zinc-500 dark:text-zinc-400">{{ $trend['slips'] }} slip • {{ ucfirst($trend['status']) }}</div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <div class="h-3 flex-1 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                                            <div
                                                class="h-full rounded-full transition-all duration-500 {{ $trend['status'] === 'processed' ? 'bg-emerald-500' : 'bg-sky-500' }}"
                                                style="width: {{ max(10, (int) round(($trend['slips'] / $maxSlips) * 100)) }}%"
                                            ></div>
                                        </div>
                                        <div class="w-12 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ $trend['progress'] }}%</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-3xl border border-dashed border-zinc-300 px-4 py-5 text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                            Belum ada data batch untuk divisualisasikan.
                        </div>
                    @endif
                </div>

                <div class="rounded-[2rem] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="mb-4">
                        <flux:heading size="lg">{{ __('Batch Terakhir') }}</flux:heading>
                        <flux:text class="text-zinc-600 dark:text-zinc-300">
                            {{ __('Ringkasan cepat dari batch payroll terbaru.') }}
                        </flux:text>
                    </div>

                    @if ($latestBatch)
                        <div class="space-y-4">
                            <div class="rounded-3xl bg-zinc-50 p-5 dark:bg-zinc-800/70">
                                <div class="text-xs font-medium uppercase tracking-[0.24em] text-zinc-500 dark:text-zinc-400">Periode</div>
                                <div class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-zinc-50">{{ $latestBatch->month }}</div>
                                <div class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $latestBatch->description }}</div>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-2">
                                <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                                    <div class="text-xs uppercase tracking-[0.24em] text-zinc-500 dark:text-zinc-400">Status</div>
                                    <div class="mt-2 text-lg font-semibold text-zinc-950 dark:text-zinc-50">{{ ucfirst($latestBatch->status) }}</div>
                                </div>
                                <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                                    <div class="text-xs uppercase tracking-[0.24em] text-zinc-500 dark:text-zinc-400">Slip</div>
                                    <div class="mt-2 text-lg font-semibold text-zinc-950 dark:text-zinc-50">{{ $latestBatch->payroll_details_count }}</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="rounded-3xl border border-dashed border-zinc-300 px-4 py-5 text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                            Belum ada batch payroll untuk diringkas.
                        </div>
                    @endif
                </div>

                <div class="rounded-[2rem] border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="mb-4">
                        <flux:heading size="lg">{{ __('Quick Actions') }}</flux:heading>
                        <flux:text class="text-zinc-600 dark:text-zinc-300">
                            {{ __('Akses cepat ke area utama payroll.') }}
                        </flux:text>
                    </div>

                    <div class="grid gap-3">
                        <a href="{{ route('salary-components.index') }}" wire:navigate class="rounded-2xl border border-zinc-200 px-4 py-4 transition hover:border-sky-300 hover:bg-sky-50 dark:border-zinc-700 dark:hover:border-sky-800 dark:hover:bg-sky-950/20">
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">Master Komponen Gaji</div>
                            <div class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Kelola tunjangan, potongan, dan basis perhitungan.</div>
                        </a>
                        <a href="{{ route('payroll-batches.index') }}" wire:navigate class="rounded-2xl border border-zinc-200 px-4 py-4 transition hover:border-sky-300 hover:bg-sky-50 dark:border-zinc-700 dark:hover:border-sky-800 dark:hover:bg-sky-950/20">
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">Proses Batch Payroll</div>
                            <div class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Buat batch baru dan monitor progress generation.</div>
                        </a>
                        @can('manage-user-roles')
                            <a href="{{ route('user-roles.index') }}" wire:navigate class="rounded-2xl border border-zinc-200 px-4 py-4 transition hover:border-sky-300 hover:bg-sky-50 dark:border-zinc-700 dark:hover:border-sky-800 dark:hover:bg-sky-950/20">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">Kelola Role User</div>
                                <div class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Atur akses super-admin, payroll manager, dan employee.</div>
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layouts::app>
