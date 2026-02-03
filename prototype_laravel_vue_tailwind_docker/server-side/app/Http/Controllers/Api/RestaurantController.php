<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Restaurant\CreateRestaurantRequest;
use App\Http\Requests\Restaurant\UpdateRestaurantRequest;
use App\Models\Restaurant;
use App\Services\RestaurantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantController extends Controller
{
    public function __construct(
        protected RestaurantService $service
    ) {}

    /**
     * Create restaurant with owner.
     * Only super-admin can create restaurants.
     */
    public function store(CreateRestaurantRequest $request): JsonResponse
    {
        $result = $this->service->createRestaurantWithOwner(
            $request->input('restaurant'),
            $request->input('owner')
        );

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Restaurant and owner created successfully',
            'data' => $result,
        ], 201);
    }

    /**
     * Get all restaurants.
     * Only super-admin can see all restaurants.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();

        if (!$user->hasRole('super-admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only super-admin can view all restaurants.',
            ], 403);
        }

        $result = $this->service->getAllRestaurants();

        return response()->json($result);
    }

    /**
     * Get restaurant by ID.
     */
    public function show(Restaurant $restaurant): JsonResponse
    {
        $user = Auth::user();

        // Super-admin can see any restaurant
        if ($user->hasRole('super-admin')) {
            $result = $this->service->getRestaurantById($restaurant->id);
            return response()->json($result);
        }

        // Owner can see only their restaurant
        if ($user->hasRole('owner') && $user->id === $restaurant->owner_id) {
            $result = $this->service->getRestaurantById($restaurant->id);
            return response()->json($result);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized',
        ], 403);
    }

    /**
     * Get current user's restaurant (for owner).
     */
    public function myRestaurant(): JsonResponse
    {
        $user = Auth::user();

        if (!$user->hasRole('owner')) {
            return response()->json([
                'success' => false,
                'message' => 'Only restaurant owners can access this endpoint',
            ], 403);
        }

        $result = $this->service->getMyRestaurant($user);

        if (!$result['success']) {
            return response()->json($result, 404);
        }

        return response()->json($result);
    }

    /**
     * Update restaurant.
     */
    public function update(UpdateRestaurantRequest $request, Restaurant $restaurant): JsonResponse
    {
        $result = $this->service->updateRestaurant($restaurant, $request->validated());

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Restaurant updated successfully',
            'data' => $result['data'],
        ]);
    }

    /**
     * Delete restaurant.
     * Only super-admin can delete restaurants.
     */
    public function destroy(Restaurant $restaurant): JsonResponse
    {
        $user = Auth::user();

        if (!$user->hasRole('super-admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only super-admin can delete restaurants.',
            ], 403);
        }

        $result = $this->service->deleteRestaurant($restaurant);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        return response()->json($result);
    }

    /**
     * Get employees of a restaurant.
     */
    public function employees(Restaurant $restaurant): JsonResponse
    {
        $user = Auth::user();

        // Owner can see employees only of their restaurant
        if ($user->hasRole('owner') && $user->id === $restaurant->owner_id) {
            $result = $this->service->getRestaurantEmployees($restaurant->id);
            return response()->json($result);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized',
        ], 403);
    }
}

