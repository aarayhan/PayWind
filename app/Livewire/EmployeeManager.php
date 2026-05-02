<?php

namespace App\Livewire;

use App\Models\Employee;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class EmployeeManager extends Component
{
    use WithPagination;

    public string $nip = '';
    public string $name = '';
    public string $email = '';
    public string $base_salary = '';
    public string $joined_at = '';
    public string $status = 'permanent';
    public string $ptkp_status = 'TK/0';
    public $search = '';

    protected $rules = [
        'nip' => 'required|unique:employees,nip',
        'name' => 'required|min:3',
        'email' => 'required|email|unique:employees,email',
        'base_salary' => 'required|numeric',
        'joined_at' => 'required|date',
        'ptkp_status' => 'required|in:TK/0,TK/1,TK/2,TK/3,K/0,K/1,K/2,K/3,KI/0,KI/1,KI/2,KI/3',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function save()
    {
        $this->validate();

        Employee::create([
            'nip' => $this->nip,
            'name' => $this->name,
            'email' => $this->email,
            'base_salary' => $this->base_salary,
            'joined_at' => $this->joined_at,
            'status' => $this->status,
            'ptkp_status' => $this->ptkp_status,
        ]);

        $this->reset(['nip', 'name', 'email', 'base_salary', 'joined_at']);
        $this->status = 'permanent';
        $this->ptkp_status = 'TK/0';
        session()->flash('message', 'Karyawan berhasil ditambahkan!');
    }

    public function render(): View
    {
        return view('livewire.employee-manager', [
            'employees' => Employee::where('name', 'like', '%'.$this->search.'%')
                            ->latest()
                            ->paginate(5)
        ])->layout('layouts.app', [
            'title' => 'Manajemen Karyawan',
        ]);
    }
}
