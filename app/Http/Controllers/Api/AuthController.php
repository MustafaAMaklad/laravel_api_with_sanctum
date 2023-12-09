<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $validator  = Validator::make($request->input(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'min:8',
                'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',
                'confirmed'
            ],
            'city_id' => 'required|integer|exists:cities,id',
            'profile_image' => 'string',
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
            $user->role = 'client';
            $user->status = 'active';
            $user->profile_image = $request->profile_image ?? 'https://www.w3schools.com/howto/img_avatar.png';
            $user->save();

            $client = new Client;
            $client->first_name = $request->first_name;
            $client->last_name = $request->last_name;
            $client->user_id = $user->id;
            $client->save();

            DB::commit();
        } catch (Exception) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'errors' => 'Failed to create user account.',
            ]);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;
        $client = $user::where('id', $user->id)->with('client')->first();

        return response()->json([
            'status' => true,
            'message' => 'Client account was created successfully.',
            'data' => [
                'user' => $client,
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

        $user = User::where('email', $request->email)->with('client')->first();

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
