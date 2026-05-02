<div class="space-y-6">
    <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="mb-2">
            <flux:heading size="xl">Payroll Settings</flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-300">
                Kelola rule dinamis BPJS, bracket TER, mapping PTKP, dan toggle global payroll tanpa ubah kode.
            </flux:text>
        </div>

        @if (session()->has('message'))
            <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-300">
                {{ session('message') }}
            </div>
        @endif

        @if ($isLocked)
            <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700 dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-300">
                Payroll settings sedang terkunci karena ada batch payroll yang masih diproses di sesi lain.
            </div>
        @endif
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mb-4">
                <flux:heading size="lg">Management Tarif BPJS & Global Toggle</flux:heading>
                <flux:text class="text-zinc-600 dark:text-zinc-300">
                    Edit tarif, plafon, basis minimum, cut-off payroll, metode pembulatan, dan toggle umum lain.
                </flux:text>
            </div>

            <form wire:submit="saveSettings" class="space-y-5">
                @foreach ($settingGroups as $group => $items)
                    <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                        <div class="mb-3 text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                            {{ str_replace('_', ' ', $group) }}
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            @foreach ($items as $item)
                                <div>
                                    <flux:input wire:model="settings.{{ $item->id }}" :label="$item->label" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                @can('manage-payroll-settings')
                    <flux:button type="submit" variant="primary" :disabled="$isLocked">Simpan Settings</flux:button>
                @endcan
            </form>
        </div>

        <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mb-4">
                <flux:heading size="lg">Komponen Default Payroll</flux:heading>
                <flux:text class="text-zinc-600 dark:text-zinc-300">
                    Tandai komponen pendapatan tetap yang mempengaruhi basis payroll.
                </flux:text>
            </div>

            <form wire:submit="saveDefaultComponents" class="space-y-4">
                <div class="grid gap-3">
                    @foreach ($earningComponents as $component)
                        <label class="flex items-center gap-3 rounded-2xl border border-zinc-200 px-4 py-3 dark:border-zinc-700">
                            <input type="checkbox" wire:model="defaultComponentIds" value="{{ $component->id }}" class="size-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-500">
                            <div class="flex-1">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $component->name }}</div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                    Nominal default:
                                    {{ $component->default_amount !== null ? 'Rp '.number_format((float) $component->default_amount, 0, ',', '.') : '-' }}
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>

                @can('manage-payroll-settings')
                    <flux:button type="submit" variant="primary" :disabled="$isLocked">Simpan Komponen Default</flux:button>
                @endcan
            </form>
        </div>
    </div>

    <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="mb-4">
            <flux:heading size="lg">PTKP & Status Mapping</flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-300">
                Hubungkan status PTKP ke kategori TER dan nilai PTKP tahunan yang berlaku.
            </flux:text>
        </div>

        <form wire:submit="savePtkpStatuses" class="space-y-4">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/60">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">Status</th>
                            <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">Deskripsi</th>
                            <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">Kategori TER</th>
                            <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">PTKP Tahunan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach ($ptkpRows as $row)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $row->code }}</td>
                                <td class="px-4 py-3">
                                    <flux:input wire:model="ptkpStatuses.{{ $row->id }}.description" />
                                </td>
                                <td class="px-4 py-3">
                                    <flux:select wire:model="ptkpStatuses.{{ $row->id }}.ter_category">
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                    </flux:select>
                                </td>
                                <td class="px-4 py-3">
                                    <flux:input wire:model="ptkpStatuses.{{ $row->id }}.annual_ptkp" type="number" min="0" step="0.01" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @can('manage-payroll-settings')
                <flux:button type="submit" variant="primary" :disabled="$isLocked">Simpan Mapping PTKP</flux:button>
            @endcan
        </form>
    </div>

    <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div>
                <flux:heading size="lg">TER Bracket Editor</flux:heading>
                <flux:text class="text-zinc-600 dark:text-zinc-300">
                    Edit threshold bruto dan tarif PPh 21 kategori A, B, dan C langsung dari UI.
                </flux:text>
            </div>

            @can('manage-payroll-settings')
                <div class="flex gap-2">
                    <flux:button size="sm" variant="filled" wire:click="addTerBracket('A')" :disabled="$isLocked">Tambah A</flux:button>
                    <flux:button size="sm" variant="filled" wire:click="addTerBracket('B')" :disabled="$isLocked">Tambah B</flux:button>
                    <flux:button size="sm" variant="filled" wire:click="addTerBracket('C')" :disabled="$isLocked">Tambah C</flux:button>
                </div>
            @endcan
        </div>

        <form wire:submit="saveTerBrackets" class="space-y-6">
            @foreach ($terGroups as $category => $rows)
                <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                    <div class="mb-3 text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                        Kategori {{ $category }}
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
                            <thead class="bg-zinc-50 dark:bg-zinc-800/60">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">Batas Bawah</th>
                                    <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">Batas Atas</th>
                                    <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">Tarif</th>
                                    <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                                @foreach ($rows as $row)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <flux:input wire:model="terBrackets.{{ $row->id }}.lower_bound" type="number" min="0" step="1" />
                                            @error("terBrackets.{$row->id}.lower_bound") <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                                        </td>
                                        <td class="px-4 py-3">
                                            <flux:input wire:model="terBrackets.{{ $row->id }}.upper_bound" type="number" min="0" step="1" />
                                            @error("terBrackets.{$row->id}.upper_bound") <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                                        </td>
                                        <td class="px-4 py-3">
                                            <flux:input wire:model="terBrackets.{{ $row->id }}.rate" type="number" min="0" step="0.0001" />
                                        </td>
                                        <td class="px-4 py-3">
                                            @can('manage-payroll-settings')
                                                <flux:button size="sm" variant="danger" wire:click="deleteTerBracket({{ $row->id }})" type="button" :disabled="$isLocked">
                                                    Hapus
                                                </flux:button>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

            @can('manage-payroll-settings')
                <flux:button type="submit" variant="primary" :disabled="$isLocked">Simpan Bracket TER</flux:button>
            @endcan
        </form>
    </div>
</div>
