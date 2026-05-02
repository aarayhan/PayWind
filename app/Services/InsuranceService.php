<?php

namespace App\Services;

class InsuranceService
{
    public function __construct(
        protected PayrollReferenceService $references,
    ) {
    }

    public function calculate(float $baseSalary): array
    {
        $healthMinBase = $this->references->decimalSetting('bpjs_kesehatan', 'minimum_salary_base');
        $jpMinBase = $this->references->decimalSetting('bpjs_ketenagakerjaan', 'minimum_salary_base');

        $healthBase = min(max($baseSalary, $healthMinBase), $this->references->decimalSetting('bpjs_kesehatan', 'salary_cap'));
        $jpBase = min(max($baseSalary, $jpMinBase), $this->references->decimalSetting('bpjs_ketenagakerjaan', 'jp_salary_cap'));
        $jhtBase = max($baseSalary, $jpMinBase);

        $employeeItems = [
            ['name' => 'BPJS Kesehatan', 'amount' => round($healthBase * $this->references->decimalSetting('bpjs_kesehatan', 'employee_rate'), 2)],
            ['name' => 'JHT', 'amount' => round($jhtBase * $this->references->decimalSetting('bpjs_ketenagakerjaan', 'jht_employee_rate'), 2)],
            ['name' => 'JP', 'amount' => round($jpBase * $this->references->decimalSetting('bpjs_ketenagakerjaan', 'jp_employee_rate'), 2)],
        ];

        $employerItems = [
            ['name' => 'BPJS Kesehatan Perusahaan', 'amount' => round($healthBase * $this->references->decimalSetting('bpjs_kesehatan', 'employer_rate'), 2)],
            ['name' => 'JHT Perusahaan', 'amount' => round($jhtBase * $this->references->decimalSetting('bpjs_ketenagakerjaan', 'jht_employer_rate'), 2)],
            ['name' => 'JP Perusahaan', 'amount' => round($jpBase * $this->references->decimalSetting('bpjs_ketenagakerjaan', 'jp_employer_rate'), 2)],
            ['name' => 'JKK Perusahaan', 'amount' => round($jhtBase * $this->references->decimalSetting('bpjs_ketenagakerjaan', 'jkk_rate'), 2)],
            ['name' => 'JKM Perusahaan', 'amount' => round($jhtBase * $this->references->decimalSetting('bpjs_ketenagakerjaan', 'jkm_rate'), 2)],
        ];

        return [
            'employee_items' => $employeeItems,
            'employee_total' => round(collect($employeeItems)->sum('amount'), 2),
            'employer_items' => $employerItems,
            'employer_total' => round(collect($employerItems)->sum('amount'), 2),
        ];
    }
}
