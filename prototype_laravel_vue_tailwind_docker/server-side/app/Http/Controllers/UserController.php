<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateEmployeeRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct(
        protected UserService $service
    ) {}

    /**
     * Get all users (filtered by restaurant_id for owners).
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        $restaurantId = $request->query('restaurant_id');

        $result = $this->service->getAllUsers(
            $user,
            $restaurantId ? (int) $restaurantId : null
        );

        return response()->json($result);
    }

    /**
     * Create employee (only for restaurant owners).
     */
    public function store(CreateEmployeeRequest $request): JsonResponse
    {
        $user = Auth::user();

        $result = $this->service->createEmployee($user, $request->validated());

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        return response()->json($result, 201);
    }

    /**
     * Get user by ID.
     */
    public function show(User $user): JsonResponse
    {
        $currentUser = Auth::user();

        $result = $this->service->getUserById($currentUser, $user->id);

        if (!$result['success']) {
            return response()->json($result, $result['error'] === 'Unauthorized' ? 403 : 404);
        }

        return response()->json($result);
    }

    /**
     * Update user.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $currentUser = Auth::user();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|nullable|string|min:8',
            'role' => 'sometimes|required|string|in:admin,manager,waiter',
        ]);

        $result = $this->service->updateUser($currentUser, $user, $validated);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        return response()->json($result);
    }

    /**
     * Delete user.
     */
    public function destroy(User $user): JsonResponse
    {
        $currentUser = Auth::user();

        $result = $this->service->deleteUser($currentUser, $user);

        if (!$result['success']) {
            return response()->json($result, $result['error'] === 'Unauthorized' ? 403 : 400);
        }

        return response()->json($result);
    }
}

