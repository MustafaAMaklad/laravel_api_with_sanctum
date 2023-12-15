<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WishlistController extends Controller
{
    /**
     * Show wishlist
     */
    public function show(Request $request)
    {
        $wishlist = Wishlist::getIfExistsOrNull(Client::where('user_id', $request->user()->id)->value('id'));
        return response()->json([
            'status' => true,
            'message' => 'Show wishlist.',
            'data' => $wishlist
        ]);
    }
    /**
     * Add or remove item from wishlist
     */
    public function wish(Request $request)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id'
        ]);
        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
        // Get wishlist item
        $clientId = Client::where('user_id', $request->user()->id)->value('id');
        $wishlist = Wishlist::where('client_id', $clientId)
            ->where('product_id', $request->product_id)->first();
        // Validate if item already exists
        if ($wishlist) {
            $wishlist->delete();
        } else {
            // Create the new item
            $wishlist = new Wishlist;
            $wishlist->client_id = $clientId;
            $wishlist->product_id = $request->product_id;
            $wishlist->save();
        }
        $wishlist = Wishlist::getIfExistsOrNull($clientId);
        return response()->json([
            'status' => true,
            'message' => 'Wishlist was updated successfully.',
            'data' => $wishlist
        ]);
    }
}
