<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Store extends Model
{
    use HasFactory;

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
    public function city(): HasOne
    {
        return $this->hasOne(City::class);
    }
    public function commercialImages(): HasMany
    {
        return $this->hasMany(CommercialImage::class, 'store_id');
    }
}
