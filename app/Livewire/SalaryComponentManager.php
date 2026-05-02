<?php

namespace App\Livewire;

use App\Models\SalaryComponent;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class SalaryComponentManager extends Component
{
    use WithPagination;

    public string $name = '';
    public string $type = 'earning';
    public bool $is_fixed = true;
    public string $default_amount = '';
    public string $formula = '';
    public string $search = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'type' => 'required|in:earning,deduction',
            'is_fixed' => 'required|boolean',
            'default_amount' => 'nullable|numeric|min:0',
            'formula' => 'nullable|string|max:255',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function save(): void
    {
        $validated = $this->validate();

        SalaryComponent::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'is_fixed' => $validated['is_fixed'],
            'default_amount' => $validated['default_amount'] !== '' ? $validated['default_amount'] : null,
            'formula' => $validated['formula'] !== '' ? $validated['formula'] : null,
        ]);

        $this->reset(['name', 'default_amount', 'formula', 'search']);
        $this->type = 'earning';
        $this->is_fixed = true;

        session()->flash('message', 'Komponen gaji berhasil ditambahkan!');
    }

    public function render(): View
    {
        return view('livewire.salary-component-manager', [
            'components' => SalaryComponent::query()
                ->where('name', 'like', '%'.$this->search.'%')
                ->latest()
                ->paginate(8),
        ])->layout('layouts.app', [
            'title' => 'Master Komponen Gaji',
        ]);
    }
}
