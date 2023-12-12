<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coupons = Coupon::all();
        return response()->json([
            'status' => true,
            'message' => 'All Coupons',
            'data' => $coupons
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:coupons,code|min:8',
            'usage_number' => 'required|integer|gt:0',
            'discount_percent' => 'required|numeric|between:1,100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $coupon = new Coupon;
        $coupon->code = $request->code;
        $coupon->usage_number = $request->usage_number;
        $coupon->discount_percent = $request->discount_percent;
        $coupon->save();

        return response()->json([
            'status' => true,
            'message' => 'Coupon was added successfully.',
            'data' => $coupon
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        // check if no param is given return all
        if (!$request->query()) {
            $coupons = Coupon::all();
            return response()->json([
                'status' => true,
                'message' => 'All Coupons',
                'data' => $coupons
            ]);
        }
        // validate
        $validator = Validator::make($request->query(), [
            'coupon_id' => 'exists:coupons,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
        // send coupon in response
        $coupon = Coupon::find($request->coupon_id);

        return response()->json([
            'status' => true,
            'message' => 'Show Coupon.',
            'data' => $coupon
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coupon_id' => 'required|exists:coupons,id',
            'code' => 'string|unique:coupons,code|min:8',
            'usage_number' => 'integer|gt:0',
            'discount_percent' => 'numeric|between:1,100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $coupon = Coupon::find($request->coupon_id);

        if ($request->has('coupon_code')) {
            $coupon->code = $request->coupon_code;
        }

        if ($request->has('usage_number')) {
            $coupon->usage_number = $request->usage_number;
        }

        if ($request->has('discount_percent')) {
            $coupon->discount_percent = $request->discount_percent;
        }

        $coupon->save();

        return response()->json([
            'status' => true,
            'message' => $coupon->wasChanged() ?  'Coupon was updated successfully.' : 'Coupon was not updated.',
            'data' => $coupon
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coupon_id' => 'required|exists:coupons,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $isDeleted = Coupon::where('id', $request->coupon_id)->delete();

        if ($isDeleted) {

            return response()->json([
                'status' => true,
                'message' => 'Coupon was deleted successfully',
                'data' => null
            ]);
        } else {

            return response()->json([
                'status' => false,
                'message' => 'Failed tp delete coupon'
            ]);
        }

    }

    /**
     * Check coupon is valid
     */
    public function check(Request $request)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required|exists:coupons,code'
        ]);
        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $coupon = Coupon::where('code', $request->coupon_id)->first();
        $errors = [];
        // Validate usage number
        $coupon->usage_number === 0;
    }
}

