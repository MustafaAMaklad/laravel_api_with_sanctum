<?php

namespace App\Rules;

use App\Models\Product;
use App\Models\Store;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ProductBelongToStore implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
    }
    public static function passes(mixed $value) {

        if (Product::find($value)->store_id !== Store::where('user_id', request()->user()->id)->value('id')) {
            return false;
        }
        return true;
    }
}
