<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    /**
     * Get the client associated with the order
     */
    public function client() : BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the store associated with the order
     */
    public function store() : BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the order items associated with the order
     */
    public function orderItems() : HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
