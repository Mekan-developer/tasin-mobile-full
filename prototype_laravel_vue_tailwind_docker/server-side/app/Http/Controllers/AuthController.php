<?php

namespace App\Http\Controllers;

use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct(protected AuthService $service) {}

    /**
     * Get application status
     */
    public function status(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'API is working',
            'timestamp' => now()->toISOString(),
            'version' => '1.0.0'
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $dto = new RegisterDTO($request->validated());
        $result = $this->service->createUser($dto);
        return response()->json($result['body'])->withCookie($result['cookie']);
    }

    public function login(LoginRequest $request)
    {
        $dto = new LoginDTO($request->validated());
        $result = $this->service->loginUser($dto);

        if (isset($result['status']) && $result['status'] !== 200) {
            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'Unknown error',
                'status' => $result['status'] ?? 'Unknown error',
            ], $result['status']);
        }

        Log::info('Login result: ' . json_encode($result));
        return response()->json($result['body'])->withCookie($result['cookie']);
    }

    // 🔁 Refresh - теперь использует middleware для валидации
    public function refresh(Request $request)
    {
        // Payload уже проверен в middleware CheckRefreshToken
        $payload = $request->get('refresh_payload');
        $userId = $payload['sub'];

        $result = $this->service->refreshToken($userId);
        if (isset($result['status']) && $result['status'] !== 200) {
            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'Unknown error',
                'message' => $result['message'] ?? null,
            ], $result['status']);
        }

        return response()->json($result['body'])->withCookie($result['cookie']);
    }

    public function me(AuthService $service)
    {
        $result = $service->authUserWithRole();
        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    public function profile(AuthService $service)
    {
        $result = $service->authUserWithRole();
        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    public function logout(AuthService $service)
    {
        $result = $service->logoutAuthUser();

        return response()->json($result)->withCookie(
            cookie()->forget('refresh_token')
        );
    }
}
