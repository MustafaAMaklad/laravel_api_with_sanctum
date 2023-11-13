<?php

namespace App\Http\Controllers;

use App\Exceptions\ProductNotFoundException;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Product::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required',
            'price' => 'required',
        ]);

        $product = new Product;
        $product->name = $request->name;
        $product->slug = $request->slug;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->user_id = auth()->id();

        $product->save();

        return response()->json([
            'status' => true,
            'message' => 'producted was created successfully',
            'product' => $product
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::findOrFail($id);


        return response()->json([
            'status' => true,
            'product' => $product,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $product = Product::findOrFail($id);
        $product->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Product was updated successfully',
            'product' => $product
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $res = Product::where('id', $id)->delete();
        if ($res) {
            return response()->json(['message' => 'Product was deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Failed to delete product'], 200);
        }
    }
    /**
     * Search the specified resource from storage.
     */
    public function search(string $name)
    {
        return Product::where('name', 'like', '%' . $name . '%')->get();
    }
}
