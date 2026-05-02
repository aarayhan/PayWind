<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PtkpStatus extends Model
{
    protected $fillable = ['code', 'description', 'ter_category', 'annual_ptkp'];

    protected function casts(): array
    {
        return [
            'annual_ptkp' => 'decimal:2',
        ];
    }
}
