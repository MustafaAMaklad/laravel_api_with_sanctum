<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Product extends Model
{
    use HasFactory;

    protected $appends = [
        'image_path'
    ];
    public function store() : BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
    public function category() : BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    protected function imagePath(): Attribute
    {
        return Attribute::make(
            get: fn () => url($this->image),
        );
    }
}
