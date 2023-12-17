<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Wishlist extends Model
{
    use HasFactory;
    protected $fillable = [
        'client_id'
    ];
    /**
     * Get the client associated with the wishlist
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
    /**
     * Get the wishlist items associated with the wishlist
     */
    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    /**
     * Get wishlist of client or null if does not exist
     */
    public static function getIfExistsOrNull(string $clientId): array | null
    {
        $Wishlist = Wishlist::with('product.category')
        ->where('client_id', $clientId)
        ->get()->toArray();

        return   empty($Wishlist)? null : $Wishlist;
    }
}
