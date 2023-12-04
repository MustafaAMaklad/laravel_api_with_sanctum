<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Rules\SortAttribute;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

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
        'errors' => $validator->errors()
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
        'message' => 'Account\'s status was set to ' . $status . ' successfully',
        'data' => $user
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
        'errors' => $validator->errors()
      ]);
    }

    if (!$request->query()) {
      $clients = User::has('client')->with('client')->get();
      $stores = User::has('store')->with('store')->get();

      return response()->json([
        'status' => true,
        'message' => 'All users',
        'data' => [
          'clients' => $clients,
          'stores' => $stores
        ]
      ]);
    } else {
      $clients = User::has('client')->when($request->has('role'), function ($query) use ($request) {
        $query->where('role', $request['role']);
      })->when($request->has('status'), function ($query) use ($request) {
        $query->where('status', $request['status']);
      })->with('client')->get();

      $stores = User::has('store')->when($request['role'], function ($query) use ($request) {
        $query->where('role', $request['role']);
      })->when($request['status'], function ($query) use ($request) {
        $query->where('status', $request['status']);
      })->with('store')->get();

      if ($request->has('sortbyname')) {
        $sort = ($request->sortbyname === 'desc') ? 'sortByDesc' : 'sortBy';
        $clients = $clients->$sort('client.first_name')->values();
        $stores = $stores->$sort('store.name')->values();
      }
      if ($request->has('sortbydate')) {
        $sort = ($request->sortbydate === 'desc') ? 'sortByDesc' : 'sortBy';
        $clients = $clients->$sort('client.created_at')->values();
        $stores = $stores->$sort('store.created_at')->values();
      }

      return response()->json([
        'status' => true,
        'message' => 'Filtered users',
        'data' => [
          'clients' => $clients,
          'stores' => $stores,
        ]
      ]);
    }
  }
}
