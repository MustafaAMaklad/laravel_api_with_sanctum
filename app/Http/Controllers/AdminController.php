<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRoleActionException;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use App\Models\Store;

class AdminController extends Controller
{
    public function activate(string $id)
    {
        $user = $this->user($id);

        if ($user->status === 'blocked' || $user->status === 'pending') {
            $user->status = 'active';
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Account was activated successfully'
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Account is already activated'
            ], 200);
        }
    }
    public function block(string $id)
    {
        $user = $this->user($id);
        
        if ($user->status === 'active') {
            $user->status = 'blocked';
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Account was blocked successfully'
            ], 200);
        }
        if ($user->status === 'pending') {

            return response()->json([
                'status' => false,
                'message' => 'Account is currently pending'
            ], 200);
        }
        if ($user->status === 'blocked') {

            return response()->json([
                'status' => false,
                'message' => 'Account is already blocked'
            ], 200);
        }
    }
    protected function user(string $id)
    {
        $role = User::where('id', $id)->firstOrFail()->role;
        if ($role === 'client') {
            $user = Client::where('user_id', $id)->firstOrFail();
            return $user;
        }
        if ($role === 'store') {
            $user = Store::where('user_id', $id)->firstOrFail();
            return $user;
        }

        throw new InvalidRoleActionException;
    }
}
