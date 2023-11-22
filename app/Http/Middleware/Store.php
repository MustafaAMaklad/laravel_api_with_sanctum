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
        if (!$user || $user->role !== 'store') {
            return response()->json(['message' => 'Unauthorized'], 403);
        } else {
            $status = StoreModel::where('user_id', $user->id)->value('status');
            if ($status === 'pending') {
                return response()->json(['message'=> 'Your account is currently pending, contact the admin to activate your account'], 403);
            }
            if ($status === 'blocked') {
                return response()->json(['message'=> 'Your account is currently blocked'], 403);
            }
        }
        return $next($request);
    }
}
