<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Store extends Model
{
    use HasFactory;

    /**
     * Append over all rating attribute to store model
     */
    protected $appends = [
        'over_all_rating',
    ];
    
    /**
     * Get the products associated with the store
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'store_id');
    }

    /**
     * Get the commercial images associated with the store
     */
    public function commercialImages(): HasMany
    {
        return $this->hasMany(CommercialImage::class, 'store_id');
    }

    /**
     * Get the user associated with the store
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Set over all rating accessor
     */
    protected function overAllRating(): Attribute
    {
        return Attribute::make(
            get: fn () => round(Order::where('store_id', 1)
                ->where('rating', '!=', null)->average('rating'), 2)
        );
    }
}
