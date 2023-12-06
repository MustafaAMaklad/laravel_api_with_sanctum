<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;
use App\Models\CommercialImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthStoreController extends Controller
{
    public function register(Request $request)
    {
        $valdiator  = Validator::make($request->input(), [
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

        if ($valdiator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $valdiator->errors()
            ]);
        }

        $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'city_id' => $request->city_id,
            'role' => 'store',
            'status' => 'pending',
            'profile_image' => $request->profile_image ?? 'https://www.w3schools.com/howto/img_avatar.png'
        ]);

        $store = new Store;

        $store->name = $request->name;
        $store->commercial_number = $request->commercial_number;
        $store->user_id = $user->id;
        $store->save();

        foreach ($request->commercial_images as $commercialImage) {
            $store->commercialImages()
                ->save(new CommercialImage(['image_path' => $commercialImage]));
        }

        $store->refresh();

        return response()->json([
            'status' => true,
            'message' => 'Store account was created successfully.',
            'data' => $store
        ]);
    }
    public function login(Request $request)
    {
        $valdiator  = Validator::make($request->input(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string'
        ]);

        if ($valdiator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $valdiator->errors()
            ]);
        }

        $user = User::where('email', $request->email)->first();

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'error' => 'Incorrect password'
            ]);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;


        return response()->json([
            'status' => true,
            'message' => 'User logged in',
            'data' => [
                'user' => $user,
                'toekn' => $token
            ]
        ]);
    }
}
