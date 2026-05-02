<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class UserRoleManager extends Component
{
    use WithPagination;

    public string $search = '';
    public array $roles = [];

    public function mount(): void
    {
        abort_unless(Gate::allows('manage-user-roles'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function saveRole(int $userId): void
    {
        abort_unless(Gate::allows('manage-user-roles'), 403);

        $role = $this->roles[$userId] ?? null;

        if (! in_array($role, ['employee', 'payroll-manager', 'super-admin'], true)) {
            $this->addError("roles.{$userId}", 'Role tidak valid.');

            return;
        }

        $user = User::query()->findOrFail($userId);
        $user->role = $role;
        $user->save();

        session()->flash('message', "Role untuk {$user->name} berhasil diperbarui.");
    }

    public function render(): View
    {
        $users = User::query()
            ->where(function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->orderBy('name')
            ->paginate(10);

        foreach ($users as $user) {
            $this->roles[$user->id] ??= $user->role;
        }

        return view('livewire.user-role-manager', [
            'users' => $users,
        ])->layout('layouts.app', [
            'title' => 'User Roles',
        ]);
    }
}
