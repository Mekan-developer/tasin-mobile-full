<?php

namespace App\Services;

use App\Http\Resources\User\IndexResource;
use App\Models\User;
use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function __construct(protected AuthRepository $repository) {}

    public function createUser($dto)
    {
        $dto->password = \Illuminate\Support\Facades\Hash::make($dto->password);

        $user = $this->repository->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => $dto->password,
        ]);

        // Создаем access token
        $accessToken = JWTAuth::fromUser($user);

        // Создаем refresh token с увеличенным TTL (30 дней)
        JWTAuth::factory()->setTTL(43200);
        $refreshToken = JWTAuth::claims(['typ' => 'refresh'])->fromUser($user);
        // Вернем стандартный TTL для access токенов
        JWTAuth::factory()->setTTL((int) config('jwt.ttl'));

        // Вернем структуру для контроллера
        return $this->respondWithToken($accessToken, $refreshToken, $user);
    }

    public function loginUser(Object $dto)
    {
        $credentials = [
            'email' => $dto->email,
            'password' => $dto->password,
        ];

        if (!$token = JWTAuth::attempt($credentials)) {
            return [
                'error' => 'Unauthorized',
                'status' => 401,
            ];
        }

        $user = Auth::user();
        if (!$user) {
            return [
                'error' => 'User not found',
                'status' => 500
            ];
        }

        // Блокируем вход владельца/сотрудников, если ресторан отключён


        // Создаем refresh token с увеличенным TTL (30 дней)
        JWTAuth::factory()->setTTL(43200); // 30 дней в минутах
        $refreshToken = JWTAuth::claims(['typ' => 'refresh'])->fromUser($user);
        // Восстановим стандартный TTL для access-токенов
        JWTAuth::factory()->setTTL((int) config('jwt.ttl'));

        return $this->respondWithToken($token, $refreshToken, $user);
    }

    public function refreshToken($userId)
    {
        try {
            $user = User::find($userId);

            if (!$user) {
                return [
                    'error' => 'Пользователь не найден',
                    'status' => 404,
                ];
            }

            // Получаем нового access token
            $newAccessToken = JWTAuth::fromUser($user);

            // Создаем новый refresh token с увеличенным TTL (30 дней)
            JWTAuth::factory()->setTTL(43200); // 30 дней в минутах
            $newRefreshToken = JWTAuth::claims(['typ' => 'refresh'])->fromUser($user);
            // Восстановим стандартный TTL для access-токенов
            JWTAuth::factory()->setTTL((int) config('jwt.ttl'));

            return $this->respondWithToken($newAccessToken, $newRefreshToken, $user);
        } catch (\Exception $e) {
            return [
                'error' => 'Ошибка обновления токена',
                'message' => $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function authUserWithRole()
    {
        $result = $this->repository->getAuthUserWithRole();
        return $result;
    }

    public function logoutAuthUser(): array
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return ['message' => 'Выход выполнен'];
        } catch (JWTException $e) {
            return ['error' => 'Ошибка выхода'];
        }
    }

    protected function respondWithToken($newAccessToken, $newRefreshToken = null, $user)
    {
        return [
            'body' => [
                'success' => true,
                'data' => [
                    'user' => new IndexResource($user),
                    'token' => $newAccessToken,
                    'token_type' => 'bearer',
                    'expires_in' => JWTAuth::factory()->getTTL() * 60 // в секундах
                ]
            ],
            'cookie' => cookie('refresh_token', $newRefreshToken, 43200, '/', null, true, true, false, 'None')
        ];
    }
}
