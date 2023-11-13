<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function show(string $id) {
        $user = User::findOrFail($id);

        return response()->json([
            'status' => true,
            'user' => $user,
        ], 200);
    }
}
