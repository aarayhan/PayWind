<div class="space-y-6">
    <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="mb-2">
            <flux:heading size="xl">User Role Management</flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-300">
                Kelola role akses user. Halaman ini hanya dapat diakses oleh super-admin.
            </flux:text>
        </div>

        @if (session()->has('message'))
            <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-300">
                {{ session('message') }}
            </div>
        @endif
    </div>

    <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <flux:heading size="lg">Daftar User</flux:heading>
                <flux:text class="text-zinc-600 dark:text-zinc-300">
                    Cari user dan ubah role mereka langsung dari dashboard admin.
                </flux:text>
            </div>

            <div class="w-full md:w-80">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari nama atau email..." />
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-800/60">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">Nama</th>
                        <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">Email</th>
                        <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">Role</th>
                        <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-300">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($users as $user)
                        <tr>
                            <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">{{ $user->name }}</td>
                            <td class="px-4 py-3">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                <flux:select wire:model="roles.{{ $user->id }}">
                                    <option value="employee">employee</option>
                                    <option value="payroll-manager">payroll-manager</option>
                                    <option value="super-admin">super-admin</option>
                                </flux:select>
                                @error("roles.{$user->id}") <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                            </td>
                            <td class="px-4 py-3">
                                <flux:button size="sm" variant="primary" wire:click="saveRole({{ $user->id }})">
                                    Simpan Role
                                </flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-zinc-500 dark:text-zinc-400">
                                Tidak ada user ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</div>
