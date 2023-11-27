<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Copon;

class CoponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Copon::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'usage_number' => 'required',
            'discount_percent' => 'required'
        ]);

        $copon = new Copon;
        $copon->code = $request->code;
        $copon->usage_number = $request->usage_number;
        $copon->discount_percent = $request->discount_percent;
        $copon->save();

        return response()->json([
            'copon' => $copon
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $copon = Copon::findOrFail($id);

        return response()->json($copon, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'usage_number' => 'required'
        ]);

        $copon = Copon::findOrFail($id);
        $copon->usage_number = $request->usage_number;
        $copon->save();

        return response()->json([
            'status' => true,
            'message' => 'Copon was updated successfully',
            'date' => $copon
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $isDeleted = Copon::where('id', $id)->delete();
        if ($isDeleted) {
            return response()->json([
                'status' => true,
                'message' => 'Copon was deleted successfully'
            ], 200);
        } else {
            return response()->json([
                'status' => true,
                'message' => 'Failed to delete copon'
            ], 200);
        }
    }
}
