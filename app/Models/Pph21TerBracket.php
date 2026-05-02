<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pph21TerBracket extends Model
{
    protected $fillable = ['category', 'lower_bound', 'upper_bound', 'rate', 'sort_order'];

    protected function casts(): array
    {
        return [
            'rate' => 'float',
        ];
    }
}
