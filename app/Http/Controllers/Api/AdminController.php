<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use App\Models\Client;
use App\Models\Store;

class AdminController extends Controller
{
    public function updateAccountStatus(Request $request)
    {
        $request->validate([
            'id' => 'required', // add rule to check if exists
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
            'role' => 'in:client,store',
            'sortbyname' => 'in:1,0',
            'sortbydate' => 'in:1,0'
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
                if ($key === 'status' || $key === 'role') {
                    $query->where($key, $value);
                }
            }

            $ids = $query->pluck('id')->values()->toArray();

            $clients = Client::with('user')->whereIn('user_id', $ids);
            $stores = Store::with('user')->whereIn('user_id', $ids);

            if (isset($request->query()['sortbyname'])) {
                if ($request->query()['sortbyname'] === '0') {
                    $clients->orderBy('first_name', 'desc');
                    $stores->orderBy('name', 'desc');
                } else {
                    $clients->orderBy('first_name', 'asc');
                    $stores->orderBy('name', 'asc');
                }
            }
            if (isset($request->query()['sortbydate'])) {
                if ($request->query()['sortbydate'] === '0') {
                    $clients->orderBy('created_at', 'desc');
                    $stores->orderBy('created_at', 'desc');
                } else {
                    $clients->orderBy('created_at', 'asc');
                    $stores->orderBy('created_at', 'asc');
                }
            }


            return response()->json([
                'status' => true,
                'data' => [
                    'clients' => $clients->get(),
                    'stores' => $stores->get()
                ]
            ]);
        }
    }
}
