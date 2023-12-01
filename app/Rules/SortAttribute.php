<?php

namespace App\Rules;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Contracts\Validation\ValidationRule;

class SortAttribute implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $sortParamsCount = 0;
        foreach (request()->query() as $param => $value) {
            if (Str::startsWith($param, 'sort')) {
                $sortParamsCount++;
            }
            if ($sortParamsCount > 1) {
                $fail($this->message());
            }
        }
    }
    public function message()
    {
        return 'You can not sort by more than one attribute';
    }
}
