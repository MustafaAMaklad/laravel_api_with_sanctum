<?php

namespace App\Rules;

use Closure;
use GuzzleHttp\Psr7\Request;
use Illuminate\Contracts\Validation\ValidationRule;

class PriceFilter implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
    }
    public static function passes() {
        if (!(request()->has('price_from') && request()->has('price_to'))) {
            return false;
        }
        return true;
    }
}
