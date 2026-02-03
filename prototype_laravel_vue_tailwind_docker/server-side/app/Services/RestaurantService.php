<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\User;
use App\Repositories\RestaurantRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RestaurantService
{
    public function __construct(
        protected RestaurantRepository $repository
    ) {}

    /**
     * Create restaurant with owner.
     *
     * @param array $restaurantData
     * @param array $ownerData
     * @return array
     */
    public function createRestaurantWithOwner(array $restaurantData, array $ownerData): array
    {
        try {
            DB::beginTransaction();

            // Create owner user
            $owner = User::create([
                'name' => $ownerData['name'],
                'email' => $ownerData['email'],
                'password' => Hash::make($ownerData['password']),
            ]);

            // Assign owner role
            $owner->assignRole('owner');

            // Create restaurant
            $restaurant = $this->repository->create([
                'name' => $restaurantData['name'],
                'description' => $restaurantData['description'] ?? null,
                'address' => $restaurantData['address'],
                'phone' => $restaurantData['phone'] ?? null,
                'active' => $restaurantData['active'] ?? true,
                'owner_id' => $owner->id,
            ]);

            // Link owner to restaurant
            $owner->restaurant_id = $restaurant->id;
            $owner->save();

            DB::commit();

            return [
                'success' => true,
                'restaurant' => $restaurant->load('owner'),
                'owner' => $owner->load('roles'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating restaurant with owner: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => 'Failed to create restaurant: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get all restaurants (only for super-admin).
     */
    public function getAllRestaurants(): array
    {
        $restaurants = $this->repository->getAll();

        return [
            'success' => true,
            'data' => $restaurants,
        ];
    }

    /**
     * Get restaurant by ID.
     */
    public function getRestaurantById(int $id): array
    {
        $restaurant = $this->repository->findById($id);

        if (!$restaurant) {
            return [
                'success' => false,
                'error' => 'Restaurant not found',
            ];
        }

        return [
            'success' => true,
            'data' => $restaurant,
        ];
    }

    /**
     * Get restaurant for current user (owner).
     */
    public function getMyRestaurant(User $user): array
    {
        if (!$user->hasRole('owner')) {
            return [
                'success' => false,
                'error' => 'User is not an owner',
            ];
        }

        $restaurant = $this->repository->findByOwnerId($user->id);

        if (!$restaurant) {
            return [
                'success' => false,
                'error' => 'Restaurant not found for this owner',
            ];
        }

        return [
            'success' => true,
            'data' => $restaurant->load('users'),
        ];
    }

    /**
     * Update restaurant.
     */
    public function updateRestaurant(Restaurant $restaurant, array $data): array
    {
        try {
            $this->repository->update($restaurant, $data);

            return [
                'success' => true,
                'data' => $restaurant->fresh(['owner', 'users']),
            ];
        } catch (\Exception $e) {
            Log::error('Error updating restaurant: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => 'Failed to update restaurant: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Delete restaurant.
     */
    public function deleteRestaurant(Restaurant $restaurant): array
    {
        try {
            $this->repository->delete($restaurant);

            return [
                'success' => true,
                'message' => 'Restaurant deleted successfully',
            ];
        } catch (\Exception $e) {
            Log::error('Error deleting restaurant: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => 'Failed to delete restaurant: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get employees of a restaurant.
     */
    public function getRestaurantEmployees(int $restaurantId): array
    {
        $employees = $this->repository->getRestaurantUsers($restaurantId);

        return [
            'success' => true,
            'data' => $employees,
        ];
    }
}

