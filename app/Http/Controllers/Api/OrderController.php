<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Client;
use App\Models\Store;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Cart;
use App\Models\OrderItem;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected array $errors = []; // store custom errors

    /**
     * Make order for client
     */
    public function make(Request $request)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'coupon_id' => 'exists:coupons,id'
        ]);
        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
        // Get required models
        $clientId = Client::where('user_id', $request->user()->id)->value('id');
        $cart = Cart::where('client_id', $clientId)->with('cartItems.product')->first();
        $products = []; // store products objects
        // Validate if cart is empty
        $cart === null ? $this->errors['cart'] = ['items' => 'No items to order.'] : $this->errors;
        if ($cart) {
            // Validate if products quantity is still availabe
            foreach ($cart->cartItems as $cartItem) {
                $product = Product::with('store')->find($cartItem->product_id);
                if ($product->quantity < $cartItem->cart_product_quantity) {
                    $this->errors['product'] =
                        [
                            'quantity' => 'Product '
                                . $product->name_en .
                                ' quantity is currently unavailabe in '
                                . $product->store->name  . '.'
                        ];
                } else {
                    $products[$cartItem->product_id] = $product;
                }
            }
        }
        // Validate if client can use coupon if existed
        if ($request->has('coupon_id')) {
            // validate if client used coupon before
            Order::where('client_id', $clientId)
                ->where('coupon_id', $request->coupon_id)
                ->first() ?
                $this->errors['coupon'] = ['used' => 'Coupon is unavailabe for client.'] : $this->errors;
            // validate if coupon is exhausted
            Coupon::find($request->coupon_id)
                ->usage_number === 0 ?
                $this->errors['coupon'] = ['usage_number' => 'Coupon is exhausted.'] : $this->errors;
        }
        if ($this->errors) {

            return response()->json([
                'status' => false,
                'errors' => $this->errors
            ]);
        }
        DB::beginTransaction();
        try {
            // Store order
            $order = new Order;
            $order->order_number = 'ORD_' . $clientId . '_' . now()->format('Ymd-His');
            $order->client_id = $clientId;
            $order->store_id = $cart->cartItems[0]->product->store_id;
            $order->total_price = $cart->cart_total_price;
            if ($request->has('coupon_id')) {
                // Apply coupon
                $coupon = Coupon::find($request->coupon_id);
                $order->is_coupon_applied = true;
                $order->coupon_id = $coupon->id;
                $order->total_price_after_copon_applied =
                    $order->total_price - $order->total_price * ($coupon->discount_percent / 100);
                // Update coupon
                $coupon->usage_number -= 1;
                $coupon->save();
            } else {
                $order->total_price_after_copon_applied = $order->total_price;
            }
            $order->save(); // Store order

            foreach ($cart->cartItems as $cartItem) {
                // Update product
                $products[$cartItem->product_id]->quantity -= $cartItem->cart_product_quantity;
                $products[$cartItem->product_id]->save();
                // Store order item
                $orderItem = new OrderItem;
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $products[$cartItem->product_id]->id;
                $orderItem->product_price = $products[$cartItem->product_id]->price;
                $orderItem->ordered_quantity = $cartItem->cart_product_quantity;
                $orderItem->save();
            }
            // Delete cart
            $cart->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'errors' => [
                    'order' => 'Failed to make order.',
                ]
            ]);
        }
        // Get the order was made
        $order = Order::with('orderItems.product')->find($order->id);

        return response()->json([
            'status' => true,
            'message' => 'Order was made successfully',
            'data' => $order
        ]);
    }

    /**
     * Accept order
     */
    public function accept(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
        ]);
        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $storetId = Store::where('user_id', $request->user()->id)->value('id');
        $order = Order::with('orderItems.product')
            ->where('id', $request->order_id)
            ->where('store_id', $storetId)
            ->first();
        if (!$order) {
            // Validate if order belong to store
            $this->errors['order'] = [
                'store' => 'Store does not have an order with the given ID.'
            ];
        } elseif ($order->status !== 'new' || $order->status !== 'rejected') {
            // Validate if order status can be accepted
            $this->errors['order'] = [
                'status' => 'Status ' . $order->status . 'can not be marked as accepted.'
            ];
        }

        if ($this->errors) {

            return response()->json([
                'status' => false,
                'errors' => $this->errors
            ]);
        }

        $order->status = 'accepted';
        $order->save();
        $order->refresh();

        return response()->json([
            'status' => true,
            'message' => 'Order was marked as accepted successfully',
            'data' => $order
        ]);
    }

    /**
     * Reject order
     */
    public function reject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
        ]);
        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $storetId = Store::where('user_id', $request->user()->id)->value('id');
        $order = Order::with('orderItems.product')
            ->where('id', $request->order_id)
            ->where('store_id', $storetId)
            ->first();
        if (!$order) {
            // Validate if order belong to store
            $this->errors['order'] = [
                'store' => 'Store does not have an order with the given ID.'
            ];
        } elseif ($order->status !== 'new' || $order->status !== 'accepted') {
            // Validate if order status can be rejected
            $this->errors['order'] = [
                'status' => 'Status ' . $order->status . 'can not be marked as rejected.'
            ];
        }

        if ($this->errors) {

            return response()->json([
                'status' => false,
                'errors' => $this->errors
            ]);
        }

        $order->status = 'rejected';
        $order->save();
        $order->refresh();

        return response()->json([
            'status' => true,
            'message' => 'Order was marked as rejected successfully',
            'data' => $order
        ]);
    }

    /**
     * Finish order
     */
    public function finish(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
        ]);
        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $clientId = Client::where('user_id', $request->user()->id)->value('id');
        $order = Order::with('orderItems.product')
            ->where('id', $request->order_id)
            ->where('client_id', $clientId)
            ->first();
        if (!$order) {
            // Validate if order belong to client
            $this->errors['order'] = [
                'client' => 'Client does not have an order with the given ID.'
            ];
        } elseif ($order->status === 'new' || $order->status === 'rejected' || $order->status === 'finished') {
            // Validate if order status can be accepted
            $this->errors['order'] = [
                'status' => 'Status ' . $order->status . 'can not be marked as finished.'
            ];
        }

        if ($this->errors) {

            return response()->json([
                'status' => false,
                'errors' => $this->errors
            ]);
        }

        $order->status = 'finished';
        $order->save();
        $order->refresh();

        return response()->json([
            'status' => true,
            'message' => 'Order was marked as finished successfully',
            'data' => $order
        ]);
    }
}
