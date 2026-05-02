<?php

use App\Models\Employee;
use App\Models\PayrollBatch;
use App\Models\SalaryComponent;
use Illuminate\Support\Facades\Route;
use App\Livewire\EmployeeManager;
use App\Livewire\PayrollProcessor;
use App\Livewire\PayrollSettingsManager;
use App\Livewire\SalaryComponentManager;
use App\Livewire\UserRoleManager;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        $latestBatch = PayrollBatch::query()
            ->withCount('payrollDetails')
            ->latest()
            ->first();

        $processingBatch = PayrollBatch::query()
            ->where('status', 'draft')
            ->whereColumn('processed_employees', '<', 'total_employees')
            ->latest()
            ->first();

        $recentBatches = PayrollBatch::query()
            ->withCount('payrollDetails')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', [
            'employeeCount' => Employee::query()->count(),
            'fixedComponentCount' => SalaryComponent::query()->where('type', 'earning')->where('is_fixed', true)->count(),
            'deductionComponentCount' => SalaryComponent::query()->where('type', 'deduction')->count(),
            'processedBatchCount' => PayrollBatch::query()->where('status', 'processed')->count(),
            'latestBatch' => $latestBatch,
            'processingBatch' => $processingBatch,
            'recentBatches' => $recentBatches,
            'batchTrend' => $recentBatches
                ->reverse()
                ->values()
                ->map(fn (PayrollBatch $batch) => [
                    'month' => $batch->month,
                    'slips' => $batch->payroll_details_count,
                    'progress' => $batch->progress_percentage,
                    'status' => $batch->status,
                ]),
        ]);
    })->name('dashboard');
    Route::get('/employees', EmployeeManager::class)->name('employees.index');
    Route::get('/salary-components', SalaryComponentManager::class)->name('salary-components.index');
    Route::get('/payroll-batches', PayrollProcessor::class)->name('payroll-batches.index');
    Route::middleware('payroll.role')->group(function () {
        Route::get('/payroll-settings', PayrollSettingsManager::class)->name('payroll-settings.index');
    });
    Route::get('/user-roles', UserRoleManager::class)->name('user-roles.index');
});

require __DIR__.'/settings.php';
