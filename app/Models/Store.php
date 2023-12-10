<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasFactory;

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'store_id');
    }
    public function commercialImages(): HasMany
    {
        return $this->hasMany(CommercialImage::class, 'store_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
