<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Rules\SortAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Client;
use App\Models\Store;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function updateAccountStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:active,blocked'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $user = User::find($request->user_id);
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
        $validator = Validator::make($request->all(), [
            'role' => 'in:client,store',
            'status' => 'in:active,blocked,pending',
            'sortbyname' => [
                'in:desc,asc',
                new SortAttribute
            ],
            'sortbydate' => [
                'in:desc,asc',
                new SortAttribute
            ]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

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
            $clients = Client::whereHas('user', function ($query) use ($request) {
                $query->when($request['role'], function ($query) use ($request) {
                    $query->where('role', $request['role']);
                })->when($request['status'], function ($query) use ($request) {
                    $query->where('status', $request['status']);
                });
            })->when($request['sortbyname'], function ($query) use ($request) {
                $query->orderBy('first_name', $request['sortbyname'] === 'desc' ? 'desc' : 'asc');
            })->when($request['sortbydate'], function ($query) use ($request) {
                $query->orderBy('created_at', $request['sortbydate'] === 'desc' ? 'desc' : 'asc');
            })->with('user')->get();

            $stores = Store::whereHas('user', function ($query) use ($request) {
                $query->when($request['role'], function ($query) use ($request) {
                    $query->where('role', $request['role']);
                });
                $query->when($request['status'], function ($query) use ($request) {
                    $query->where('status', $request['status']);
                });
            })->when($request['sortbyname'], function ($query) use ($request) {
                $query->orderBy('name', $request['sortbyname'] === 'desc' ? 'desc' : 'asc');
            })->when($request['sortbydate'], function ($query) use ($request) {
                $query->orderBy('created_at', $request['sortbydate'] === 'desc' ? 'desc' : 'asc');
            })->with('user')->get();

            return response()->json([
                'status' => true,
                'data' => [
                    'clients' => $clients,
                    'stores' => $stores,
                ]
            ]);
        }
    }
}
