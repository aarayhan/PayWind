<?php

namespace App\Services;

class TaxService
{
    public function __construct(
        protected PayrollReferenceService $references,
    ) {
    }

    public function calculateMonthlyPph21(float $grossIncome, string $ptkpStatus): array
    {
        $status = $this->references->ptkpStatus($ptkpStatus);
        $rate = 0.0;

        foreach ($this->references->terBrackets($status['ter_category']) as $bracket) {
            $upper = $bracket['upper_bound'];

            if ($grossIncome >= $bracket['lower_bound'] && ($upper === null || $grossIncome <= $upper)) {
                $rate = (float) $bracket['rate'];
                break;
            }
        }

        $amount = $grossIncome * $rate;
        $roundingMethod = $this->references->setting('payroll_global', 'tax_rounding_method');

        $amount = match ($roundingMethod) {
            'ceil' => ceil($amount),
            'floor' => floor($amount),
            default => round($amount, 2),
        };

        return [
            'ptkp_status' => $status['code'],
            'ter_category' => $status['ter_category'],
            'rate' => $rate,
            'amount' => $amount,
        ];
    }
}
