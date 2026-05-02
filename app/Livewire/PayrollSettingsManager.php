<?php

namespace App\Livewire;

use App\Models\PayrollSetting;
use App\Models\Pph21TerBracket;
use App\Models\PtkpStatus;
use App\Models\PayrollBatch;
use App\Models\SalaryComponent;
use App\Rules\ContiguousTerBracketRule;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class PayrollSettingsManager extends Component
{
    public array $settings = [];
    public array $ptkpStatuses = [];
    public array $terBrackets = [];
    public array $defaultComponentIds = [];
    public bool $isLocked = false;

    public function mount(): void
    {
        abort_unless(Gate::allows('access-payroll-settings'), 403);

        $this->loadState();
    }

    public function saveSettings(): void
    {
        $this->authorizeMutation();

        foreach ($this->settings as $id => $value) {
            $setting = PayrollSetting::query()->find($id);

            if (! $setting) {
                continue;
            }

            $setting->value = (string) $value;
            $setting->save();
        }

        session()->flash('message', 'Pengaturan payroll berhasil disimpan.');
    }

    public function savePtkpStatuses(): void
    {
        $this->authorizeMutation();

        foreach ($this->ptkpStatuses as $id => $status) {
            $ptkpStatus = PtkpStatus::query()->find($id);

            if (! $ptkpStatus) {
                continue;
            }

            $ptkpStatus->description = $status['description'];
            $ptkpStatus->ter_category = $status['ter_category'];
            $ptkpStatus->annual_ptkp = $status['annual_ptkp'];
            $ptkpStatus->save();
        }

        session()->flash('message', 'Mapping PTKP berhasil diperbarui.');
    }

    public function saveTerBrackets(): void
    {
        $this->authorizeMutation();

        $rule = new ContiguousTerBracketRule();

        $validator = Validator::make(
            ['terBrackets' => $this->terBrackets],
            ['terBrackets' => [$rule]],
        );

        if ($validator->fails()) {
            foreach ($rule->issues() as $issue) {
                $this->addError("terBrackets.{$issue['id']}.{$issue['field']}", $issue['message']);
            }

            return;
        }

        foreach ($this->terBrackets as $id => $bracket) {
            $terBracket = Pph21TerBracket::query()->find($id);

            if (! $terBracket) {
                continue;
            }

            $terBracket->lower_bound = $bracket['lower_bound'];
            $terBracket->upper_bound = $bracket['upper_bound'] !== '' ? $bracket['upper_bound'] : null;
            $terBracket->rate = $bracket['rate'];
            $terBracket->save();
        }

        session()->flash('message', 'Bracket TER berhasil diperbarui.');
    }

    public function addTerBracket(string $category): void
    {
        $this->authorizeMutation();

        $sortOrder = (int) Pph21TerBracket::query()
            ->where('category', $category)
            ->max('sort_order') + 1;

        Pph21TerBracket::query()->create([
            'category' => $category,
            'lower_bound' => 0,
            'upper_bound' => null,
            'rate' => 0,
            'sort_order' => $sortOrder,
        ]);

        $this->loadState();
        session()->flash('message', "Bracket kategori {$category} ditambahkan.");
    }

    public function deleteTerBracket(int $bracketId): void
    {
        $this->authorizeMutation();

        Pph21TerBracket::query()->whereKey($bracketId)->delete();
        $this->resequenceBrackets();
        $this->loadState();

        session()->flash('message', 'Bracket TER berhasil dihapus.');
    }

    public function saveDefaultComponents(): void
    {
        $this->authorizeMutation();

        $selectedIds = collect($this->defaultComponentIds)->map(fn ($id) => (int) $id)->all();

        SalaryComponent::query()
            ->where('type', 'earning')
            ->update(['is_fixed' => false]);

        SalaryComponent::query()
            ->whereIn('id', $selectedIds)
            ->update(['is_fixed' => true]);

        session()->flash('message', 'Komponen default payroll berhasil diperbarui.');
    }

    public function render(): View
    {
        return view('livewire.payroll-settings-manager', [
            'settingGroups' => PayrollSetting::query()->orderBy('group')->orderBy('label')->get()->groupBy('group'),
            'ptkpRows' => PtkpStatus::query()->orderBy('code')->get(),
            'terGroups' => Pph21TerBracket::query()->orderBy('category')->orderBy('sort_order')->get()->groupBy('category'),
            'earningComponents' => SalaryComponent::query()->where('type', 'earning')->orderBy('name')->get(),
        ])->layout('layouts.app', [
            'title' => 'Payroll Settings',
        ]);
    }

    protected function loadState(): void
    {
        $this->refreshLockState();

        $this->settings = PayrollSetting::query()
            ->get()
            ->mapWithKeys(fn (PayrollSetting $setting) => [$setting->id => $setting->value ?? ''])
            ->all();

        $this->ptkpStatuses = PtkpStatus::query()
            ->get()
            ->mapWithKeys(fn (PtkpStatus $status) => [$status->id => [
                'code' => $status->code,
                'description' => $status->description,
                'ter_category' => $status->ter_category,
                'annual_ptkp' => (string) $status->annual_ptkp,
            ]])
            ->all();

        $this->terBrackets = Pph21TerBracket::query()
            ->get()
            ->mapWithKeys(fn (Pph21TerBracket $bracket) => [$bracket->id => [
                'category' => $bracket->category,
                'lower_bound' => (string) $bracket->lower_bound,
                'upper_bound' => $bracket->upper_bound !== null ? (string) $bracket->upper_bound : '',
                'rate' => (string) $bracket->rate,
            ]])
            ->all();

        $this->defaultComponentIds = SalaryComponent::query()
            ->where('type', 'earning')
            ->where('is_fixed', true)
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();
    }

    protected function resequenceBrackets(): void
    {
        foreach (['A', 'B', 'C'] as $category) {
            Pph21TerBracket::query()
                ->where('category', $category)
                ->orderBy('lower_bound')
                ->get()
                ->values()
                ->each(function (Pph21TerBracket $bracket, int $index): void {
                    $bracket->update(['sort_order' => $index + 1]);
                });
        }
    }

    protected function authorizeMutation(): void
    {
        abort_unless(Gate::allows('manage-payroll-settings'), 403);
        $this->refreshLockState();

        if ($this->isLocked) {
            abort(423, 'Payroll settings terkunci saat proses generation masih berjalan.');
        }
    }

    protected function refreshLockState(): void
    {
        $this->isLocked = PayrollBatch::query()
            ->where('status', 'draft')
            ->whereColumn('processed_employees', '<', 'total_employees')
            ->exists();
    }
}
