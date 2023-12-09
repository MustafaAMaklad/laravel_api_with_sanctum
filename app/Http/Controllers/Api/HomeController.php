<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function showProducts(Request $request)
    {
        $validator = Validator::make($request->query(), [
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
                $query->where('city_id', $request->user()->city_id);
            });
        })->with(['store', 'category']);

        if (!$request->query()) {
            $products = $products->get();

            return response()->json([
                'status' => true,
                'message' => 'Show all products for client',
                'data' => $products
            ]);
        }

        $products = $products->when($request->has('product_name_en'), function ($query) use ($request) {
            $query->where('name_en', 'like',  '%' . $request->product_name_en . '%');
        })->when($request->has('product_name_ar'), function ($query) use ($request) {
            $query->where('name_ar', 'like',  '%' . $request->product_name_ar . '%');
        })->when($request->has('price_from') && $request->has('price_from'), function ($query) use ($request) {
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
}
