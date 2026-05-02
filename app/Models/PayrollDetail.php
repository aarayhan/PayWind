<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class PayrollDetail extends Model
{
    protected $fillable = [
        'payroll_batch_id',
        'employee_id',
        'base_salary_snapshot',
        'gross_earning',
        'employee_benefit_total',
        'employer_benefit_total',
        'pph21_amount',
        'total_earning',
        'total_deduction',
        'take_home_pay',
    ];

    protected function casts(): array
    {
        return [
            'base_salary_snapshot' => 'decimal:2',
            'gross_earning' => 'decimal:2',
            'employee_benefit_total' => 'decimal:2',
            'employer_benefit_total' => 'decimal:2',
            'pph21_amount' => 'decimal:2',
            'total_earning' => 'decimal:2',
            'total_deduction' => 'decimal:2',
            'take_home_pay' => 'decimal:2',
        ];
    }

    public function payrollBatch(): BelongsTo
    {
        return $this->belongsTo(PayrollBatch::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }
}
