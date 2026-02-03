<?php

namespace App\Repositories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    /**
     * Get role by ID.
     */
    public function getRoleById(int $roleId): ?Role
    {
        return Role::find($roleId);
    }

    /**
     * Create user.
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Get users by restaurant ID.
     */
    public function getByRestaurantId(int $restaurantId): Collection
    {
        return User::where('restaurant_id', $restaurantId)
            ->with(['roles', 'permissions', 'restaurant'])
            ->get();
    }

    /**
     * Find user by ID.
     */
    public function findById(int $id): ?User
    {
        return User::with(['roles', 'permissions', 'restaurant'])->find($id);
    }

    /**
     * Update user.
     */
    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }

    /**
     * Delete user.
     */
    public function delete(User $user): bool
    {
        return $user->delete();
    }
}
