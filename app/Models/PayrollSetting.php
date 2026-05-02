<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollSetting extends Model
{
    protected $fillable = ['group', 'key', 'label', 'value'];

    public static function getValue(string $group, string $key, ?string $default = null): ?string
    {
        return static::query()
            ->where('group', $group)
            ->where('key', $key)
            ->value('value') ?? $default;
    }
}
