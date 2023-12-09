<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class CommercialImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'image_path'
    ];

    protected $appends = [
        'full_image_path'
    ];

    protected function fullImagePath(): Attribute
    {
        return Attribute::make(
            get: fn () => url($this->image_path),
        );
    }
}
