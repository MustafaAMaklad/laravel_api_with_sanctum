<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\CartItem;
use App\Models\Client;

class ItemBelongToClient implements ValidationRule
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

        if (CartItem::find($value)->client_id !== Client::where('user_id', request()->user()->id)->value('id')) {
            return false;
        }
        return true;
    }
}
