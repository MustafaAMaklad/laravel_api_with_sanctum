<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\UserHelper;
use App\Models\User;
use App\Models\Client;
use App\Models\Store;

class AdminController extends Controller
{
    public function updateAccountStatus(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'status' => 'required|in:active,blocked'
        ]);

        $user = User::where('id', $request->id)->firstOrfail();
        $status = $request->status;

        if ($user->status === $status) {

            return response()->json([
                'status' => false,
                'message' => 'Account is already ' . $status
            ], 422);
        } else if ($user->status === 'pending' && $status === 'blocked') {

            return response()->json([
                'status' => false,
                'message' => 'Account is currently pending'
            ], 422);
        } else {
            $user->status = $status;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Account\'s status was set to ' . $status . ' successfully'
            ], 200);
        }
    }

    public function showUsers(Request $request)
    {
        $request->validate([
            'status' => 'in:active,blocked,pending',
            'role' => 'in:client,store'
        ]);

        if (!$request->query()) {
            $clients = Client::with('user')->get();
            $stores = Store::with('user')->get();

            return response()->json([
                'status' => true,
                'data' => [
                    'clients' => $clients,
                    'stores' => $stores
                ]
            ]);
        } else {
            $query = User::query();
            foreach ($request->query() as $key => $value) {
                $query->where($key, $value);
            }

            $users = $query->get(['id', 'role']);
            $data = [];
            foreach ($users as $user) {
                if ($user->role === 'client') {
                    $data[] = Client::with('user')->where('user_id', $user->id)->get();
                } else {
                    $data[] = Store::with('user')->where('user_id', $user->id)->get();
                }
            }

            return response()->json([
                'status' => true,
                'data' => $data
            ]);
        }
    }
}
