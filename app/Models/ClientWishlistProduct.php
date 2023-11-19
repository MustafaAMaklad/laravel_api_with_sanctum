<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
class ClientWishlistProduct extends Model
{
    use HasFactory;

    public function client() : HasOne
    {
        return $this->hasOne(Client::class);
    }
    public function wishlist() : HasOne
    {
        return $this->hasOne(Wishlist::class);
    }
    public function product() : HasOne
    {
        return $this->hasOne(Product::class);
    }
}
