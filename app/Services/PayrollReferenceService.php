<?php

namespace App\Services;

use App\Models\PayrollSetting;
use App\Models\Pph21TerBracket;
use App\Models\PtkpStatus;
use Illuminate\Support\Collection;

class PayrollReferenceService
{
    public function setting(string $group, string $key): string
    {
        $default = collect(config('payroll.settings'))
            ->first(fn (array $setting) => $setting['group'] === $group && $setting['key'] === $key)['value'] ?? null;

        return PayrollSetting::getValue($group, $key, $default) ?? '';
    }

    public function decimalSetting(string $group, string $key): float
    {
        return (float) $this->setting($group, $key);
    }

    public function ptkpStatus(string $code): array
    {
        $status = PtkpStatus::query()->where('code', $code)->first();

        if ($status) {
            return [
                'code' => $status->code,
                'description' => $status->description,
                'ter_category' => $status->ter_category,
                'annual_ptkp' => (float) $status->annual_ptkp,
            ];
        }

        return collect(config('payroll.ptkp_statuses'))->firstWhere('code', $code)
            ?? collect(config('payroll.ptkp_statuses'))->firstWhere('code', 'TK/0');
    }

    public function terBrackets(string $category): Collection
    {
        $brackets = Pph21TerBracket::query()
            ->where('category', $category)
            ->orderBy('sort_order')
            ->get();

        if ($brackets->isNotEmpty()) {
            return $brackets->map(fn (Pph21TerBracket $bracket) => [
                'lower_bound' => (int) $bracket->lower_bound,
                'upper_bound' => $bracket->upper_bound !== null ? (int) $bracket->upper_bound : null,
                'rate' => (float) $bracket->rate,
            ]);
        }

        return collect(config("payroll.ter_brackets.{$category}", []));
    }
}
