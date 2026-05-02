<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\PayrollBatch;
use App\Models\PayrollDetail;
use App\Models\PayrollItem;
use App\Models\SalaryComponent;
use App\Services\InsuranceService;
use App\Services\TaxService;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;

class PayrollProcessor extends Component
{
    public string $batchMonth = '';
    public string $batchYear = '';
    public ?int $activeBatchId = null;
    public ?int $selectedBatchId = null;

    protected function rules(): array
    {
        return [
            'batchMonth' => ['required', 'integer', 'between:1,12'],
            'batchYear' => ['required', 'integer', 'between:2024,2100'],
        ];
    }

    public function mount(): void
    {
        $this->batchMonth = now()->format('m');
        $this->batchYear = now()->format('Y');

        $this->activeBatchId = PayrollBatch::query()
            ->where('status', 'draft')
            ->whereColumn('processed_employees', '<', 'total_employees')
            ->latest()
            ->value('id');
    }

    public function createBatch(): void
    {
        $validated = $this->validate();

        $period = sprintf('%04d-%02d', (int) $validated['batchYear'], (int) $validated['batchMonth']);

        validator(
            ['month' => $period],
            ['month' => ['required', Rule::unique('payroll_batches', 'month')]]
        )->validate();

        PayrollBatch::create([
            'month' => $period,
            'status' => 'draft',
            'description' => 'Payroll periode '.$this->formatPeriodLabel($period),
        ]);

        session()->flash('message', 'Batch payroll berhasil dibuat.');
    }

    public function processBatch(int $batchId): void
    {
        $batch = PayrollBatch::query()->findOrFail($batchId);

        if ($this->activeBatchId !== null && $this->activeBatchId !== $batch->id) {
            $this->addError('batch', 'Masih ada batch lain yang sedang diproses.');

            return;
        }

        if ($batch->status !== 'draft') {
            $this->addError('batch', 'Hanya batch draft yang dapat diproses.');

            return;
        }

        if ($batch->payrollDetails()->exists()) {
            $this->addError('batch', 'Batch ini sudah pernah digenerate.');

            return;
        }

        $employeeCount = Employee::query()->count();

        if ($employeeCount === 0) {
            $this->addError('batch', 'Belum ada karyawan yang dapat diproses.');

            return;
        }

        $batch->update([
            'total_employees' => $employeeCount,
            'processed_employees' => 0,
            'started_at' => now(),
            'processed_at' => null,
            'status' => 'draft',
        ]);

        $this->activeBatchId = $batch->id;
    }

    public function continueProcessing(): void
    {
        if ($this->activeBatchId === null) {
            return;
        }

        $batch = PayrollBatch::query()->find($this->activeBatchId);

        if (! $batch) {
            $this->activeBatchId = null;

            return;
        }

        if ($batch->processed_employees >= $batch->total_employees) {
            $batch->update([
                'status' => 'processed',
                'processed_at' => now(),
            ]);

            $this->activeBatchId = null;
            session()->flash('message', 'Generate payroll selesai.');

            return;
        }

        $employee = Employee::query()
            ->orderBy('id')
            ->skip($batch->processed_employees)
            ->first();

        if (! $employee) {
            $batch->update([
                'status' => 'processed',
                'processed_at' => now(),
            ]);

            $this->activeBatchId = null;

            return;
        }

        $this->generateEmployeePayroll($batch, $employee);

        $batch->increment('processed_employees');
    }

    public function showBatch(int $batchId): void
    {
        $this->selectedBatchId = $batchId;
    }

    public function recalculateTax(int $payrollDetailId, InsuranceService $insuranceService, TaxService $taxService): void
    {
        $detail = PayrollDetail::query()->with(['employee', 'items'])->findOrFail($payrollDetailId);

        $this->removeSystemDeductionItems($detail);
        $this->applyStatutoryDeductions($detail->fresh(['employee', 'items']), $detail->employee, $insuranceService, $taxService);

        if ($this->selectedBatchId !== null) {
            $this->selectedBatchId = $detail->payroll_batch_id;
        }

        session()->flash('message', 'Pajak dan BPJS berhasil dihitung ulang.');
    }

    public function render(): View
    {
        return view('livewire.payroll-processor', [
            'batches' => PayrollBatch::query()
                ->withCount('payrollDetails')
                ->latest()
                ->get(),
            'processingBatch' => $this->activeBatchId
                ? PayrollBatch::query()->find($this->activeBatchId)
                : null,
            'selectedBatch' => $this->selectedBatchId
                ? PayrollBatch::query()
                    ->with(['payrollDetails.employee', 'payrollDetails.items'])
                    ->find($this->selectedBatchId)
                : null,
        ])->layout('layouts.app', [
            'title' => 'Proses Payroll',
        ]);
    }

    protected function generateEmployeePayroll(PayrollBatch $batch, Employee $employee): void
    {
        $baseSalaryComponent = SalaryComponent::query()->firstOrCreate(
            ['name' => 'Gaji Pokok'],
            [
                'type' => 'earning',
                'is_fixed' => true,
                'default_amount' => null,
                'formula' => null,
            ]
        );

        $detail = PayrollDetail::query()->create([
            'payroll_batch_id' => $batch->id,
            'employee_id' => $employee->id,
            'base_salary_snapshot' => $employee->base_salary,
            'gross_earning' => 0,
            'employee_benefit_total' => 0,
            'employer_benefit_total' => 0,
            'pph21_amount' => 0,
            'total_earning' => 0,
            'total_deduction' => 0,
            'take_home_pay' => 0,
        ]);

        PayrollItem::query()->create([
            'payroll_detail_id' => $detail->id,
            'salary_component_id' => $baseSalaryComponent->id,
            'component_name' => 'Gaji Pokok',
            'component_type' => 'earning',
            'paid_by' => 'employee',
            'amount' => (float) $employee->base_salary,
        ]);

        $fixedComponents = SalaryComponent::query()
            ->where('is_fixed', true)
            ->where('type', 'earning')
            ->where('name', '!=', 'Gaji Pokok')
            ->orderBy('id')
            ->get();

        foreach ($fixedComponents as $component) {
            PayrollItem::query()->create([
                'payroll_detail_id' => $detail->id,
                'salary_component_id' => $component->id,
                'component_name' => $component->name,
                'component_type' => 'earning',
                'paid_by' => 'employee',
                'amount' => (float) ($component->default_amount ?? 0),
            ]);
        }

        $this->applyStatutoryDeductions(
            $detail->fresh(['employee', 'items']),
            $employee,
            app(InsuranceService::class),
            app(TaxService::class),
        );
    }

    protected function applyStatutoryDeductions(
        PayrollDetail $detail,
        Employee $employee,
        InsuranceService $insuranceService,
        TaxService $taxService,
    ): void {
        $grossEarning = (float) $detail->items
            ->where('component_type', 'earning')
            ->sum('amount');

        $insurance = $insuranceService->calculate((float) $detail->base_salary_snapshot);

        foreach ($insurance['employee_items'] as $item) {
            $component = $this->ensureSystemComponent($item['name'], 'deduction');

            PayrollItem::query()->create([
                'payroll_detail_id' => $detail->id,
                'salary_component_id' => $component->id,
                'component_name' => $item['name'],
                'component_type' => 'deduction',
                'paid_by' => 'employee',
                'amount' => $item['amount'],
            ]);
        }

        foreach ($insurance['employer_items'] as $item) {
            $component = $this->ensureSystemComponent($item['name'], 'deduction');

            PayrollItem::query()->create([
                'payroll_detail_id' => $detail->id,
                'salary_component_id' => $component->id,
                'component_name' => $item['name'],
                'component_type' => 'deduction',
                'paid_by' => 'employer',
                'amount' => $item['amount'],
            ]);
        }

        $tax = $taxService->calculateMonthlyPph21(
            $grossEarning,
            $employee->ptkp_status ?? 'TK/0',
        );

        $pph21Component = $this->ensureSystemComponent('PPh 21', 'deduction');

        PayrollItem::query()->create([
            'payroll_detail_id' => $detail->id,
            'salary_component_id' => $pph21Component->id,
            'component_name' => 'PPh 21',
            'component_type' => 'deduction',
            'paid_by' => 'employee',
            'amount' => $tax['amount'],
        ]);

        $detail->refresh()->load('items');

        $totalEarning = (float) $detail->items
            ->where('component_type', 'earning')
            ->sum('amount');

        $employeeDeductions = (float) $detail->items
            ->where('component_type', 'deduction')
            ->where('paid_by', 'employee')
            ->sum('amount');

        $employerDeductions = (float) $detail->items
            ->where('component_type', 'deduction')
            ->where('paid_by', 'employer')
            ->sum('amount');

        $detail->update([
            'gross_earning' => $grossEarning,
            'employee_benefit_total' => $insurance['employee_total'],
            'employer_benefit_total' => $insurance['employer_total'],
            'pph21_amount' => $tax['amount'],
            'total_earning' => $totalEarning,
            'total_deduction' => $employeeDeductions,
            'take_home_pay' => $totalEarning - $employeeDeductions,
        ]);
    }

    protected function ensureSystemComponent(string $name, string $type): SalaryComponent
    {
        return SalaryComponent::query()->firstOrCreate(
            ['name' => $name],
            [
                'type' => $type,
                'is_fixed' => true,
                'default_amount' => null,
                'formula' => null,
            ]
        );
    }

    protected function removeSystemDeductionItems(PayrollDetail $detail): void
    {
        $detail->items()
            ->whereIn('component_name', [
                'BPJS Kesehatan',
                'JHT',
                'JP',
                'BPJS Kesehatan Perusahaan',
                'JHT Perusahaan',
                'JP Perusahaan',
                'JKK Perusahaan',
                'JKM Perusahaan',
                'PPh 21',
            ])
            ->delete();
    }

    protected function formatPeriodLabel(string $period): string
    {
        [$year, $month] = explode('-', $period);

        return match ((int) $month) {
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        }.' '.$year;
    }
}
