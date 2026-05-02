<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class PayrollBatch extends Model
{
    protected $fillable = [
        'month',
        'status',
        'description',
        'total_employees',
        'processed_employees',
        'started_at',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    public function payrollDetails(): HasMany
    {
        return $this->hasMany(PayrollDetail::class);
    }

    public function getProgressPercentageAttribute(): int
    {
        if ($this->total_employees === 0) {
            return 0;
        }

        return (int) round(($this->processed_employees / $this->total_employees) * 100);
    }
}
