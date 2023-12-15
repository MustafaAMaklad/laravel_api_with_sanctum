<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Client;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Add items to cart
     */
    public function addToCart(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|gt:0'
        ]);
        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
        // Get required models
        $product = Product::find($request->product_id);
        $clientId = Client::where('user_id', $request->user()->id)->value('id');
        $cart = Cart::findOrCreate($clientId);
        // Custom validation
        $errors = [];
        // Validate if product's store from a different city
        $productCityId = User::whereHas('store', function ($query) use ($request) {
            $query->whereHas('products', function ($query) use ($request) {
                $query->where('id', $request->product_id);
            });
        })->value('city_id');
        if ($productCityId !== $request->user()->city_id) {
            $errors['store_location'] = 'Store is unavialable in your location.';
        }
        // Validate if quantity is unavailable
        if ($product->quantity < $request->quantity) {
            $errors['quantity'] = 'Quntity requested for the product is unavailable.';
        }
        // Validate if carts contains products from different stores
        $prevoiusProductId = CartItem::where('cart_id', $cart->id)
            ->value('product_id');
        if ($prevoiusProductId) {
            $prevoiusStoreId = Product::where('id', $prevoiusProductId)->first()->store_id;
            if ($product->store_id !== $prevoiusStoreId) {
                $errors['cart'] = 'Cart can not contain products from different stores.';
            }
        }
        // Validate if product already exits in cart items
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();
        if ($cartItem) {

            return response()->json([
                'status' => false,
                'errors' => [
                    'product' => 'Product already exists in cart'
                ]
            ]);
        }

        if ($errors) {
            return response()->json([
                'status' => false,
                'errors' => $errors
            ]);
        }

        DB::beginTransaction();
        try {
            // Create new cart item
            $cartItem = new CartItem;
            $cartItem->cart_id = $cart->id;
            $cartItem->product_id = $product->id;
            $cartItem->cart_product_quantity = $request->quantity;
            $cartItem->save();
            // Update cart total price
            $cart->total_price += $product->price * $cartItem->cart_product_quantity;
            $cart->save();


            DB::commit();
        } catch (Exception) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'errors' => [
                    'cart' => 'Failed to add product to cart.'
                ]
            ]);
        }
        $cart = Cart::with('cartItems.product')->find($cart->id);
        return response()->json([
            'status' => true,
            'message' => 'Item was added to cart successfully.',
            'data' => $cart
        ]);
    }

    /**
     * Remove all items from cart
     */
    public function removeAllFromCart(Request $request)
    {
        $clientId = Client::where('user_id', $request->user()->id)->value('id');
        $isDeleted = Cart::Where('client_id', $clientId)->delete() === 1;

        return response()->json([
            'status' => $isDeleted,
            'message' => $isDeleted ? 'Removed all items from cart successfully.' : 'Cart has no items to remove',
            'data' => null
        ]);
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'bail|required|exists:cart_items,id|item_belong_to_client'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
        $clientId = Client::where('user_id', $request->user()->id)->value('id');
        $cart = Cart::where('client_id', $clientId)->with('cartItems.product')->first();
        if (!$cart) {
            return response()->json([
                'status' => false,
                'errors' => [
                    'cart' => 'Cart is empty'
                ]
            ]);
        }
        $cartItemsCount = CartItem::where('cart_id', $cart->id)->count();
        if ($cartItemsCount > 1) {
            DB::beginTransaction();
            try {
                $cartItem = CartItem::find($request->item_id);
                $product = Product::find($cartItem->product_id);
                $priceDifference = $cartItem->cart_product_quantity * $product->price;
                $cartItem->delete();
                $cart->total_price -= $priceDifference;
                $cart->save();
                DB::commit();

                $cart = Cart::where('client_id', $clientId)->with('cartItems.product')->first();

                return response()->json([
                    'status' => true,
                    'message' => 'Item was removed from cart successfully.',
                    'data' => $cart
                ]);
            } catch (Exception) {
                DB::rollBack();

                return response()->json([
                    'status' => false,
                    'errors' => [
                        'cart' => 'Failed to remove item from cart.'
                    ]
                ]);
            }
        }
        if ($cartItemsCount === 1) {
            $isDeleted = Cart::where('client_id', $clientId)->delete() === 1;
        }
        return response()->json([
            'status' => $isDeleted,
            'message' => $isDeleted ? 'Item was removed from cart successfully.' : 'Cart does not contain an item with the given ID.',
            'data' => null
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function updateInCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'bail|required|exists:cart_items,id|item_belong_to_client',
            'quantity' => 'required|integer|gt:0'
        ]);
        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
        $clientId = Client::where('user_id', $request->user()->id)->value('id');
        $cart = Cart::with('cartItems.product')->where('client_id', $clientId)->first();
        $cartItem = CartItem::find($request->item_id);
        $product = Product::find($cartItem->product_id);
        // Validate if quantity is unavailable
        if ($product->quantity < $request->quantity) {

            return response()->json([
                'status' => false,
                'errors' => [
                    'quantity' => 'Unavailable product\'s quantity.'
                ],
            ]);
        } else {
            $priceDifference = ($request->quantity - $cartItem->cart_product_quantity) * $product->price;

            $cartItem->cart_product_quantity = $request->quantity;
            $cartItem->save();

            $cart->total_price += $priceDifference;
            $cart->save();

            $cart = Cart::where('client_id', $clientId)->with('cartItems.product')->first();

            return response()->json([
                'status' => true,
                'message' => 'Cart item\'s quantity was updated successfully.',
                'data' => $cart
            ]);
        }
    }

    /**
     * Show cart to client
     */
    public function showCart(Request $request)
    {
        $cart =  Cart::where('client_id', Client::where('user_id', $request->user()->id)->first()->id)
            ->with('cartItems.product')
            ->first();

        return response()->json([
            'status' => true,
            'message' => 'Show cart',
            'data' => $cart
        ]);
    }
}
