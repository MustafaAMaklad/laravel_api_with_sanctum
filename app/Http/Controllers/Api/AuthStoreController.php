<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;
use App\Models\CommercialImage;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthStoreController extends Controller
{
    public function register(Request $request)
    {
        $validator  = Validator::make($request->input(), [
            'name' => 'required|string|unique:stores,name',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'min:8',
                'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',
                'confirmed'
            ],
            'commercial_number' => 'required|unique:stores,commercial_number|regex:/(01)[0-9]{9}/',
            'city_id' => 'required|integer|exists:cities,id',
            'profile_image' => 'string',
            'commercial_images' => 'required|array|min:2',
            'commercial_images.*' => 'required|regex:/^[a-zA-Z0-9\/._-]*$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        DB::beginTransaction();
        try {
            $user = new User;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->city_id = $request->city_id;
            $user->role = 'store';
            $user->status = 'pending';
            $user->profile_image = $request->profile_image ?? 'https://www.w3schools.com/howto/img_avatar.png';
            $user->save();

            $store = new Store;
            $store->name = $request->name;
            $store->commercial_number = $request->commercial_number;
            $store->user_id = $user->id;
            $store->save();

            foreach ($request->commercial_images as $commercialImage) {
                $store->commercialImages()
                    ->save(new CommercialImage(['image_path' => $commercialImage]));
            }

            DB::commit();
        } catch (Exception) {
            DB::rollBack();
            return response()->json([

                'status' => false,
                'errors' => 'Failed to create user account.',
            ]);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;
        $store = $user::where('id', $user->id)->with(['store' =>  function($query){
            $query->with('commercialImages');
        }])->first();

        return response()->json([
            'status' => true,
            'message' => 'Store account was created successfully.',
            'data' => [
                'user' => $store,
                'token' => $token
            ]
        ]);
    }
    public function login(Request $request)
    {
        $validator  = Validator::make($request->input(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $user = User::where('email', $request->email)->with(['store' =>  function($q){
            $q->with('commercialImages');
        }])->first();

        if (!Hash::check($request->password, $user->password)) {

            return response()->json([
                'status' => false,
                'errors' => [
                    'password' => [
                        'Incorrect password.'
                    ]
                ]
            ]);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'User logged in',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ]);
    }
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'User logged out'
        ]);
    }
}
