<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();

        return response()->json([
            'status' => true,
            'message' => 'All categories.',
            'data' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_name_en' => 'required|string|regex:/^[A-Za-z]+$/',
            'category_name_ar' => 'required|string|regex:/\p{Arabic}/u',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $category = new Category;
        $category->name_en = $request->category_name_en;
        $category->name_ar = $request->category_name_ar;

        $category->save();

        return response()->json([
            'status' => true,
            'message' => 'Category was created successfully.',
            'data' => $category
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        // validate
        $validator = Validator::make($request->query(), [
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
        // send category in response
        $category = Category::find($request->category_id);

        return response()->json([
            'status' => true,
            'message' => 'Show category.',
            'data' => $category
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'category_name_en' => 'string|regex:/^[A-Za-z]+$/',
            'category_name_ar' => 'string|regex:/\p{Arabic}/u',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $category = Category::find($request->category_id);

        if ($request->has('category_name_en')) {
            $category->name_en = $request->category_name_en;
        }

        if ($request->has('category_name_ar')) {
            $category->name_ar = $request->category_name_ar;
        }


        $category->save();

        return response()->json([
            'status' => true,
            'message' => $category->wasChanged() ?  'Category was updated successfully.' : 'Category was not updated.',
            'data' => $category
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
        $isDeleted = Category::where('id', $request->category_id)->delete();

        if ($isDeleted) {

            return response()->json([
                'status' => true,
                'message' => 'Category was deleted successfully',
                'data' => null
            ]);
        } else {

            return response()->json([
                'status' => false,
                'message' => 'Failed tp delete category'
            ]);
        }
    }
}
