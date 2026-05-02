<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'nip',
        'name',
        'email',
        'bank_name',
        'bank_account',
        'base_salary',
        'joined_at',
        'status',
        'ptkp_status',
    ];

    protected function casts(): array
    {
        return [
            'base_salary' => 'decimal:2',
            'joined_at' => 'date',
        ];
    }

    public function payrollDetails(): HasMany
    {
        return $this->hasMany(PayrollDetail::class);
    }
}
