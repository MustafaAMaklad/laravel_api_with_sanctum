<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\UserHelper;

class AdminController extends Controller
{
    public function updateAccountStatus(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'status' => 'required|in:active,blocked'
        ]);

        if ($request->status === 'active') {
            return $this->setAccounttatus($request->id, 'active');
        }
        if ($request->status === 'blocked') {
            return $this->setAccounttatus($request->id, 'blocked');
        }
    }
    private function setAccounttatus(string $id, string $status)
    {
        $user = UserHelper::getUserForRole($id);

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
}
