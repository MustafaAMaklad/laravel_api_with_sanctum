<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Show a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['store', 'category'])->get();
        return response()->json([
            'status' => true,
            'message' => 'All products.',
            'data' => $products
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->input(), [
            'product_name_en' => 'required|regex:/^[A-Za-z]+$/',
            'product_name_ar' => 'required|regex:/\p{Arabic}/u',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'category_id' => 'required|integer|exists:categories,id',
            'image' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $store = Store::where('user_id', $request->user()->id)->first();
        $product = new Product;
        $product->name_en = $request->product_name_en;
        $product->name_ar = $request->product_name_ar;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->store_id = $store->id;
        $product->image = $request->image;
        $product->save();

        $product = $product->where('id', $product->id)->with(['store', 'category'])->get();

        return response()->json([
            'status' => true,
            'message' => 'Product was created successfully.',
            'data' => $product
        ]);
    }

    /**
     * Show and filter the products based on city.
     */
    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city_id' => 'required|exists:cities,id',
            'product_name_en' => 'regex:/^[A-Za-z]+$/',
            'product_name_ar' => 'regex:/\p{Arabic}/u',
            'price_from' => 'numeric|price_filter',
            'price_to' => 'numeric|price_filter',
            'category_id' => 'integer|exists:categories,id',
            'store_id' => 'integer|exists:stores,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $products = Product::whereHas('store', function ($query) use ($request) {
            $query->whereHas('user', function ($query) use ($request) {
                $query->where('city_id', $request->city_id);
            });
        })->with(['store', 'category']);

        if (!$request->all()) {
            $products = $products->get();
            return response()->json([
                'status' => true,
                'message' => 'All products.',
                'data' => $products
            ]);
        }

        $products = $products->when($request->has('product_name_en'), function ($query) use ($request) {
            $query->where('name_en', 'like',  '%' . $request->product_name_en . '%');
        })->when($request->has('product_name_ar'), function ($query) use ($request) {
            $query->where('name_ar', 'like',  '%' . $request->product_name_ar . '%');
        })->when($request->has('price_from') && $request->has('price_to'), function ($query) use ($request) {
            $query->where('price', '>=', $request->price_from)->where('price', '<=', $request->price_to);
        })->when($request->has('category_id'), function ($query) use ($request) {
            $query->where('category_id',  $request->category_id);
        })->when($request->has('store_id'), function ($query) use ($request) {
            $query->where('store_id',  $request->store_id);
        })->get();

        return response()->json([
            'status' => true,
            'message' => 'Show filtered products',
            'data' => $products
        ]);
    }

    /**
     * Show products to store.
     */
    public function showForStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'integer|exists:products,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $products = Product::whereHas('store', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->with(['store', 'category']);

        if (!$request->query()) {
            $products = $products->get();

            return response()->json([
                'status' => true,
                'message' => 'Show all store\'s products.',
                'data' => $products
            ]);
        }
        $products = $products->where('id', $request->product_id)->get();

        return response()->json([
            'status' => true,
            'message' => 'Show store\'s product.',
            'data' => $products
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'bail|required|exists:products,id|product_belong_to_store',
            'product_name_en' => 'regex:/^[A-Za-z]+$/',
            'product_name_ar' => 'regex:/\p{Arabic}/u',
            'description' => 'string',
            'price' => 'numeric',
            'quantity' => 'integer',
            'category_id' => 'integer|exists:categories,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $product = Product::with('store', 'category')->find($request->product_id);

        if ($request->product_name_en) {
            $product->name_en = $request->product_name_en;
        }
        if ($request->product_name_ar) {
            $product->name_ar = $request->product_name_ar;
        }
        if ($request->description) {
            $product->description = $request->description;
        }
        if ($request->price) {
            $product->price = $request->price;
        }
        if ($request->quantity) {
            $product->quantity = $request->quantity;
        }
        if ($request->category_id) {
            $product->category_id = $request->category_id;
        }

        $product->save();
        $product->refresh();

        return response()->json([
            'status' => true,
            'message' => $product->wasChanged() ? 'Product was updated successfully.' : 'Product was not updated.',
            'data' => $product
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'bail|required|exists:products,id|product_belong_to_store',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
        $isDeleted = Product::where('id', $request->product_id)->delete() === 1;
        if ($isDeleted) {
            return response()->json([
                'status' => $isDeleted,
                'message' => $isDeleted ? 'Product was deleted successfully.' : 'Store does not have a product with the given ID',
                'data' => null
            ]);
        }
    }
}
