<?php

namespace App\Repositories;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class RestaurantRepository
{
    /**
     * Create a new restaurant.
     */
    public function create(array $data): Restaurant
    {
        return Restaurant::create($data);
    }

    /**
     * Get all restaurants.
     */
    public function getAll(): Collection
    {
        return Restaurant::with([
            'owner',
            'users.roles',
            'users.permissions',
        ])->get();
    }

    /**
     * Get restaurant by ID.
     */
    public function findById(int $id): ?Restaurant
    {
        return Restaurant::with([
            'owner',
            'users.roles',
            'users.permissions',
        ])->find($id);
    }

    /**
     * Get restaurant by owner ID.
     */
    public function findByOwnerId(int $ownerId): ?Restaurant
    {
        return Restaurant::where('owner_id', $ownerId)->with([
            'owner',
            'users.roles',
            'users.permissions',
        ])->first();
    }

    /**
     * Update restaurant.
     */
    public function update(Restaurant $restaurant, array $data): bool
    {
        return $restaurant->update($data);
    }

    /**
     * Delete restaurant.
     */
    public function delete(Restaurant $restaurant): bool
    {
        return $restaurant->delete();
    }

    /**
     * Get users (employees) of a restaurant.
     */
    public function getRestaurantUsers(int $restaurantId): Collection
    {
        return User::where('restaurant_id', $restaurantId)
            ->with('roles', 'permissions')
            ->get();
    }
}

