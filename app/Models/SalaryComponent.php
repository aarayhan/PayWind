<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class SalaryComponent extends Model
{
    protected $fillable = [
        'name',
        'type',
        'is_fixed',
        'default_amount',
        'formula',
    ];

    protected function casts(): array
    {
        return [
            'is_fixed' => 'boolean',
            'default_amount' => 'decimal:2',
        ];
    }

    public function payrollItems(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }
}
