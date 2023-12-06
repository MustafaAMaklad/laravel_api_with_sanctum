<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function show(string $id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'status' => true,
            'user' => $user,

        ], 200);
    }


}
