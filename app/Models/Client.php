<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends Model
{
    use HasFactory;


    protected $appends = [
        'full_name'
    ];

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }
    public function wishlist(): HasOne
    {
        return $this->hasOne(Wishlist::class);
    }
    public function city(): HasOne
    {
        return $this->hasOne(City::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->first_name . ' ' . $this->last_name,
        );
    }
}
