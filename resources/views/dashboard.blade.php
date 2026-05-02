<x-layouts::app :title="__('Dashboard')">
    <div class="flex w-full flex-1 flex-col gap-6">
        <section class="page-hero">
            <div class="grid gap-6 xl:grid-cols-[1.35fr_0.95fr]">
                <div class="space-y-6">
                    <div class="space-y-4">
                        <div class="section-label">
                            Command Center
                        </div>

                        <div class="space-y-3">
                            <h1 class="font-display text-4xl font-semibold tracking-tight text-zinc-950 dark:text-white sm:text-5xl">
                                PayWind Dashboard
                            </h1>
                            <p class="max-w-3xl text-sm leading-7 text-zinc-600 dark:text-zinc-300 sm:text-base">
                                Pantau batch gaji, kesiapan data induk, dan rule payroll dalam satu workspace operasional yang lebih rapi untuk tim HR dan finance.
                            </p>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <article class="metric-card">
                            <div class="text-xs font-semibold uppercase tracking-[0.26em] text-zinc-500 dark:text-zinc-400">Karyawan Aktif</div>
                            <div class="mt-4 text-3xl font-semibold text-zinc-950 dark:text-white">{{ number_format($employeeCount) }}</div>
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Data induk yang siap ikut perhitungan payroll.</p>
                        </article>

                        <article class="metric-card">
                            <div class="text-xs font-semibold uppercase tracking-[0.26em] text-zinc-500 dark:text-zinc-400">Komponen Tetap</div>
                            <div class="mt-4 text-3xl font-semibold text-zinc-950 dark:text-white">{{ number_format($fixedComponentCount) }}</div>
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Pendapatan dasar yang ikut basis hitung rutin.</p>
                        </article>

                        <article class="metric-card">
                            <div class="text-xs font-semibold uppercase tracking-[0.26em] text-zinc-500 dark:text-zinc-400">Potongan</div>
                            <div class="mt-4 text-3xl font-semibold text-zinc-950 dark:text-white">{{ number_format($deductionComponentCount) }}</div>
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Komponen deduction yang tersimpan di sistem.</p>
                        </article>

                        <article class="metric-card">
                            <div class="text-xs font-semibold uppercase tracking-[0.26em] text-zinc-500 dark:text-zinc-400">Batch Selesai</div>
                            <div class="mt-4 text-3xl font-semibold text-zinc-950 dark:text-white">{{ number_format($processedBatchCount) }}</div>
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Riwayat payroll yang sudah selesai diproses.</p>
                        </article>
                    </div>
                </div>

                <aside class="surface-card space-y-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="space-y-2">
                            <div class="section-label">Live Status</div>
                            <h2 class="font-display text-2xl font-semibold text-zinc-950 dark:text-white">
                                {{ $processingBatch ? 'Generation Sedang Berjalan' : 'Workspace Dalam Kondisi Stabil' }}
                            </h2>
                            <p class="text-sm leading-7 text-zinc-600 dark:text-zinc-300">
                                {{ $processingBatch ? 'Pantau pemrosesan payroll saat ini tanpa perlu pindah halaman.' : 'Belum ada batch draft yang sedang memproses data karyawan.' }}
                            </p>
                        </div>

                        <div class="soft-pill {{ $processingBatch ? 'bg-sky-100 text-sky-700 dark:bg-sky-500/15 dark:text-sky-200' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-200' }}">
                            {{ $processingBatch ? $processingBatch->progress_percentage.'%' : 'Standby' }}
                        </div>
                    </div>

                    @if ($processingBatch)
                        <div class="space-y-3 rounded-[1.5rem] border border-sky-200/70 bg-sky-50/80 p-5 dark:border-sky-500/20 dark:bg-sky-500/10">
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <div class="font-medium text-sky-900 dark:text-sky-100">{{ $processingBatch->month }}</div>
                                <div class="text-sky-700 dark:text-sky-200">{{ $processingBatch->processed_employees }}/{{ $processingBatch->total_employees }} karyawan</div>
                            </div>

                            <div class="h-3 overflow-hidden rounded-full bg-white/70 dark:bg-sky-950/40">
                                <div class="h-full rounded-full bg-sky-500 transition-all duration-500" style="width: {{ $processingBatch->progress_percentage }}%"></div>
                            </div>
                        </div>
                    @else
                        <div class="rounded-[1.5rem] border border-dashed border-zinc-300 px-4 py-5 text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                            Buat batch baru untuk mulai menghitung payroll periode berikutnya.
                        </div>
                    @endif

                    <div class="grid gap-3">
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
                </aside>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.22fr_0.98fr]">
            <div class="surface-card">
                <div class="mb-5 flex items-start justify-between gap-4">
                    <div class="space-y-2">
                        <div class="section-label">Recent Batches</div>
                        <h2 class="font-display text-2xl font-semibold text-zinc-950 dark:text-white">Riwayat Batch Payroll</h2>
                        <p class="text-sm leading-7 text-zinc-600 dark:text-zinc-300">
                            Snapshot batch terbaru untuk memantau progres payroll per periode.
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
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentBatches as $batch)
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-zinc-500 dark:text-zinc-400">
                                        Belum ada batch payroll yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-6">
                <div class="surface-card">
                    <div class="mb-5 space-y-2">
                        <div class="section-label">Batch Trend</div>
                        <h2 class="font-display text-2xl font-semibold text-zinc-950 dark:text-white">Trend Batch Terbaru</h2>
                        <p class="text-sm leading-7 text-zinc-600 dark:text-zinc-300">
                            Visual ringan untuk melihat volume slip dan progres beberapa batch terakhir.
                        </p>
                    </div>

                    @if (count($batchTrend) > 0)
                        @php
                            $maxSlips = max(1, collect($batchTrend)->max('slips'));
                        @endphp

                        <div class="space-y-4">
                            @foreach ($batchTrend as $trend)
                                <div class="rounded-[1.4rem] border border-zinc-200/80 bg-white/70 p-4 dark:border-white/10 dark:bg-white/5">
                                    <div class="flex items-center justify-between gap-3 text-sm">
                                        <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $trend['month'] }}</div>
                                        <div class="text-zinc-500 dark:text-zinc-400">{{ $trend['slips'] }} slip • {{ ucfirst($trend['status']) }}</div>
                                    </div>

                                    <div class="mt-3 flex items-center gap-3">
                                        <div class="h-3 flex-1 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                                            <div
                                                class="h-full rounded-full transition-all duration-500 {{ $trend['status'] === 'processed' ? 'bg-emerald-500' : 'bg-sky-500' }}"
                                                style="width: {{ max(10, (int) round(($trend['slips'] / $maxSlips) * 100)) }}%"
                                            ></div>
                                        </div>
                                        <div class="w-12 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400">{{ $trend['progress'] }}%</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-[1.5rem] border border-dashed border-zinc-300 px-4 py-5 text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                            Belum ada data batch untuk divisualisasikan.
                        </div>
                    @endif
                </div>

                <div class="surface-card">
                    <div class="mb-5 space-y-2">
                        <div class="section-label">Latest Snapshot</div>
                        <h2 class="font-display text-2xl font-semibold text-zinc-950 dark:text-white">Batch Terakhir</h2>
                        <p class="text-sm leading-7 text-zinc-600 dark:text-zinc-300">
                            Ringkasan cepat dari batch payroll terbaru yang ada di sistem.
                        </p>
                    </div>

                    @if ($latestBatch)
                        <div class="space-y-4">
                            <div class="rounded-[1.6rem] border border-zinc-200/80 bg-zinc-50/90 p-5 dark:border-white/10 dark:bg-white/5">
                                <div class="text-xs font-semibold uppercase tracking-[0.26em] text-zinc-500 dark:text-zinc-400">Periode</div>
                                <div class="mt-3 font-display text-3xl font-semibold text-zinc-950 dark:text-white">{{ $latestBatch->month }}</div>
                                <div class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $latestBatch->description }}</div>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-2">
                                <div class="rounded-[1.4rem] border border-zinc-200 p-4 dark:border-zinc-700">
                                    <div class="text-xs font-semibold uppercase tracking-[0.26em] text-zinc-500 dark:text-zinc-400">Status</div>
                                    <div class="mt-3 text-lg font-semibold text-zinc-950 dark:text-white">{{ ucfirst($latestBatch->status) }}</div>
                                </div>
                                <div class="rounded-[1.4rem] border border-zinc-200 p-4 dark:border-zinc-700">
                                    <div class="text-xs font-semibold uppercase tracking-[0.26em] text-zinc-500 dark:text-zinc-400">Slip</div>
                                    <div class="mt-3 text-lg font-semibold text-zinc-950 dark:text-white">{{ $latestBatch->payroll_details_count }}</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="rounded-[1.5rem] border border-dashed border-zinc-300 px-4 py-5 text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                            Belum ada batch payroll untuk diringkas.
                        </div>
                    @endif
                </div>

                <div class="surface-card">
                    <div class="mb-5 space-y-2">
                        <div class="section-label">Quick Actions</div>
                        <h2 class="font-display text-2xl font-semibold text-zinc-950 dark:text-white">Area Utama</h2>
                        <p class="text-sm leading-7 text-zinc-600 dark:text-zinc-300">
                            Shortcut ke modul yang paling sering dipakai saat operasional payroll berjalan.
                        </p>
                    </div>

                    <div class="grid gap-3">
                        <a href="{{ route('salary-components.index') }}" wire:navigate class="rounded-[1.4rem] border border-zinc-200 bg-white/80 px-4 py-4 transition hover:-translate-y-0.5 hover:border-sky-300 hover:bg-sky-50 dark:border-zinc-700 dark:bg-white/5 dark:hover:border-sky-500/40 dark:hover:bg-sky-500/10">
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">Master Komponen Gaji</div>
                            <div class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Kelola tunjangan, potongan, dan basis perhitungan.</div>
                        </a>

                        <a href="{{ route('payroll-batches.index') }}" wire:navigate class="rounded-[1.4rem] border border-zinc-200 bg-white/80 px-4 py-4 transition hover:-translate-y-0.5 hover:border-sky-300 hover:bg-sky-50 dark:border-zinc-700 dark:bg-white/5 dark:hover:border-sky-500/40 dark:hover:bg-sky-500/10">
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">Proses Batch Payroll</div>
                            <div class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Buat batch baru dan monitor progress generation.</div>
                        </a>

                        @can('manage-user-roles')
                            <a href="{{ route('user-roles.index') }}" wire:navigate class="rounded-[1.4rem] border border-zinc-200 bg-white/80 px-4 py-4 transition hover:-translate-y-0.5 hover:border-sky-300 hover:bg-sky-50 dark:border-zinc-700 dark:bg-white/5 dark:hover:border-sky-500/40 dark:hover:bg-sky-500/10">
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
