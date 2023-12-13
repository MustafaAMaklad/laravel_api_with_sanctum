<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Cart extends Model
{
    use HasFactory;

/**
     * Append full name attribute to client model
     */
    protected $appends = [
        'cart_total_price',
    ];

    /**
     * Get the client associated with the cart.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the cart items associated with the cart
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }

    /**
     * Make cart total price accessor
     */
    protected function cartTotalPrice(): Attribute
    {
        return Attribute::make(
            get: function () {
                $total_price = 0;
                foreach ($this->cartItems as $cartItem) {
                    $total_price += $cartItem->product->price * $cartItem->cart_product_quantity;
                }

                return $total_price;
            },
        );
    }

    /**
     * Fetch cart or create cart if it does not exist
     */
    public static function getCartOrCreate(string $clientId) : Cart
    {
        $cart = Cart::where('client_id', $clientId)->first();

        if ($cart) {

            return $cart;
        } else {
            $cart = new Cart;
            $cart->client_id = $clientId;
            $cart->total_price = 0;
            $cart->save();

            $cart = Cart::find($cart->id);

            return $cart;
        }
    }
}
