<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class BanknotesValidator implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $expectedBanknotes = [1, 5, 10, 50, 100];

        foreach ($value as $banknote => $quantity) {
            if (!in_array($banknote, $expectedBanknotes)) {
                $fail('cash_machine.unknown.banknotes')->translate();
            }

            if ($quantity < 0) {
                $fail('cash_machine.no.banknotes.detected')->translate();
            }
        }

    }
}
