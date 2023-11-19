<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ClientCartProduct extends Model
{
    use HasFactory;

    public function client() : HasOne
    {
        return $this->hasOne(Client::class);
    }
    public function cart() : HasOne
    {
        return $this->hasOne(Cart::class);
    }
    public function product() : HasOne
    {
        return $this->hasOne(Product::class);
    }
}
