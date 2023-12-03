<?php

namespace App\Http\Middleware;

use App\Models\Store as StoreModel;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Store
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user->role !== 'store') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        if ($user->status === 'pending') {
            return response()->json([
                'status' => false,
                'message' => 'Your account is currently pending, contact the admin to activate your account'
            ], 401);
        }
        if ($user->status === 'blocked') {
            return response()->json([
                'status' => false,
                'message' => 'Your account is currently blocked'
            ], 401);
        }

        return $next($request);
    }
}
