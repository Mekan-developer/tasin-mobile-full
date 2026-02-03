<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRestaurantActive
{
    /**
     * Block owners and employees if their restaurant is inactive.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Super-admin всегда проходит
        if ($user->hasRole('super-admin')) {
            return $next($request);
        }

        // Если есть ресторан и он не активен — блокируем
        $restaurant = $user->restaurant;
        // Блокируем, если статус неактивен (0/false/null)
        if ($restaurant && !$restaurant->active) {
            return response()->json([
                'success' => false,
                'message' => 'Ресторан отключён. Обратитесь к администратору.',
            ], 403);
        }

        return $next($request);
    }
}

