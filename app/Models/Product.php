<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class Product extends Model
{
    use HasFactory;

    /**
     * Append attributes to product model
     */
    protected $appends = [
        'image_path',
        'in_wishlist',
        'in_cart'
    ];

    /**
     * Get the store associated with the product
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the category associated with the product
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Set image path accessor
     */
    protected function imagePath(): Attribute
    {
        return Attribute::make(
            get: fn () => url($this->image),
        );
    }
    protected function inWishlist(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (request()->bearerToken()) {
                    $user = Auth::guard('sanctum')->user();
                    if ($user) {
                        $clientId = Client::where('user_id', $user->id)->value('id');
                        $WishlistProductId = Wishlist::where('client_id', $clientId)->where('product_id', $this->id)->value('product_id');
                        if ($this->id === $WishlistProductId) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                }
            },
        );
    }
    protected function inCart(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (request()->bearerToken()) {
                    $user = Auth::guard('sanctum')->user();
                    if ($user) {
                        $clientId = Client::where('user_id', $user->id)->value('id');
                        $cartId = Cart::where('client_id', $clientId)->value('id');
                        $cartProductId = CartItem::where('cart_id', $cartId)->where('product_id', $this->id)->value('product_id');
                        if ($this->id === $cartProductId) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                }
            },
        );
    }
}
