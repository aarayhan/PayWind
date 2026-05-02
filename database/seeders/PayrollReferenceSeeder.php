<?php

namespace Database\Seeders;

use App\Models\PayrollSetting;
use App\Models\Pph21TerBracket;
use App\Models\PtkpStatus;
use Illuminate\Database\Seeder;

class PayrollReferenceSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('payroll.settings') as $setting) {
            PayrollSetting::query()->updateOrCreate(
                ['group' => $setting['group'], 'key' => $setting['key']],
                ['label' => $setting['label'], 'value' => $setting['value']]
            );
        }

        foreach (config('payroll.ptkp_statuses') as $status) {
            PtkpStatus::query()->updateOrCreate(
                ['code' => $status['code']],
                [
                    'description' => $status['description'],
                    'ter_category' => $status['ter_category'],
                    'annual_ptkp' => $status['annual_ptkp'],
                ]
            );
        }

        foreach (config('payroll.ter_brackets') as $category => $brackets) {
            foreach ($brackets as $index => $bracket) {
                Pph21TerBracket::query()->updateOrCreate(
                    ['category' => $category, 'sort_order' => $index + 1],
                    [
                        'lower_bound' => $bracket['lower_bound'],
                        'upper_bound' => $bracket['upper_bound'],
                        'rate' => $bracket['rate'],
                    ]
                );
            }
        }
    }
}
