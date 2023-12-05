<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Number;
use NumberFormatter;

class Copon extends Model
{
    use HasFactory;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'discount_percent',
    ];

    protected $appends = [
        'discount_percentage',
    ];

    protected function discountPercentage(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format(round($this->discount_percent * 100)) . '%',
        );
    }
}
