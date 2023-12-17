<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Category extends Model
{
    use HasFactory;

    /**
     * Append attributes to product model
     */
    protected $appends = [
        'name',
    ];

    /**
     * Set name accessor
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->{'name_' . app()->getLocale()},
        );
    }
}
