<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckRefreshToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $refreshToken = $request->cookie('refresh_token');
        Log::info('Refresh Token from cookie: ' . $refreshToken);

        if (!$refreshToken) {
            return response()->json([
                'error' => 'Refresh токен отсутствует',
                'message' => 'Необходимо повторно войти в систему'
            ], 401);
        }

        try {
            // Проверяем валидность refresh токена
            $payload = JWTAuth::manager()->decode(JWTAuth::setToken($refreshToken)->getToken());

            // Проверяем тип токена
            if (($payload['typ'] ?? null) !== 'refresh') {
                return response()->json([
                    'error' => 'Неверный тип токена',
                    'message' => 'Токен не является refresh токеном'
                ], 401);
            }

            // Добавляем payload в request для использования в контроллере
            $request->merge(['refresh_payload' => $payload]);

        } catch (TokenExpiredException $e) {
            return response()->json([
                'error' => 'Refresh токен истек',
                'message' => 'Необходимо повторно войти в систему'
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'error' => 'Неверный refresh токен',
                'message' => 'Токен поврежден или недействителен'
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Ошибка валидации refresh токена',
                'message' => $e->getMessage()
            ], 401);
        }

        return $next($request);
    }
}
