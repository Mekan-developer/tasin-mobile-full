<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        if (auth()->check() && auth()->user()->can($permission)) {
            return $next($request);
        }

        return response()->json([
            'message' => 'У вас нет прав для выполнения этого действия'
        ], 403);
    }
}
