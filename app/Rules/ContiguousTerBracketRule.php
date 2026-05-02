<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ContiguousTerBracketRule implements ValidationRule
{
    protected array $issues = [];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $this->issues = $this->inspect(is_array($value) ? $value : []);

        if ($this->issues !== []) {
            $fail('Bracket TER mengandung gap atau overlap.');
        }
    }

    public function issues(): array
    {
        return $this->issues;
    }

    protected function inspect(array $rows): array
    {
        $issues = [];

        $grouped = collect($rows)
            ->map(fn (array $row, string $id) => [
                'id' => (string) $id,
                'category' => $row['category'] ?? '',
                'lower_bound' => (int) ($row['lower_bound'] !== '' ? $row['lower_bound'] : 0),
                'upper_bound' => $row['upper_bound'] !== '' ? (int) $row['upper_bound'] : null,
                'rate' => $row['rate'] ?? '',
            ])
            ->groupBy('category');

        foreach ($grouped as $category => $brackets) {
            $sorted = $brackets->sortBy('lower_bound')->values();

            foreach ($sorted as $index => $bracket) {
                if ($bracket['upper_bound'] !== null && $bracket['upper_bound'] < $bracket['lower_bound']) {
                    $issues[] = [
                        'id' => $bracket['id'],
                        'field' => 'upper_bound',
                        'message' => "Kategori {$category} baris ".($index + 1).' punya batas atas lebih kecil dari batas bawah.',
                    ];
                }

                if ($index === 0) {
                    if ($bracket['lower_bound'] !== 0) {
                        $issues[] = [
                            'id' => $bracket['id'],
                            'field' => 'lower_bound',
                            'message' => "Kategori {$category} harus dimulai dari 0 pada baris pertama.",
                        ];
                    }

                    continue;
                }

                $previous = $sorted[$index - 1];
                $expectedLowerBound = $previous['upper_bound'] !== null ? $previous['upper_bound'] + 1 : null;

                if ($expectedLowerBound === null) {
                    $issues[] = [
                        'id' => $bracket['id'],
                        'field' => 'lower_bound',
                        'message' => "Kategori {$category} memiliki baris setelah batas akhir terbuka pada baris ".($index).'.',
                    ];
                    continue;
                }

                if ($bracket['lower_bound'] !== $expectedLowerBound) {
                    $issues[] = [
                        'id' => $bracket['id'],
                        'field' => 'lower_bound',
                        'message' => "Kategori {$category} baris ".($index + 1)." harus dimulai dari {$expectedLowerBound}.",
                    ];
                }
            }
        }

        return $issues;
    }
}
