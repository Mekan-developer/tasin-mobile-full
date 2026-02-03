<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService
{
    public function __construct(
        protected UserRepository $repository
    ) {}

    /**
     * Get all users with filtering by restaurant_id.
     *
     * @param User $currentUser
     * @param int|null $restaurantId
     * @return array
     */
    public function getAllUsers(User $currentUser, ?int $restaurantId = null): array
    {
        $query = User::with(['roles', 'permissions', 'restaurant']);

        // Только owner может управлять сотрудниками
        if ($currentUser->hasRole('owner') && $currentUser->restaurant_id) {
            $users = $query->where('restaurant_id', $currentUser->restaurant_id)
                          ->where('id', '!=', $currentUser->id) // Exclude owner
                          ->get();
        }
        // Other roles see only their restaurant users
        elseif ($currentUser->restaurant_id) {
            $users = $query->where('restaurant_id', $currentUser->restaurant_id)->get();
        }
        else {
            return [
                'success' => false,
                'error' => 'Unauthorized',
            ];
        }

        return [
            'success' => true,
            'data' => $users,
        ];
    }

    /**
     * Create employee for restaurant.
     * Only owner can create employees for their restaurant.
     *
     * @param User $currentUser
     * @param array $data
     * @return array
     */
    public function createEmployee(User $currentUser, array $data): array
    {
        try {
            // Check if user is owner
            if (!$currentUser->hasRole('owner')) {
                return [
                    'success' => false,
                    'error' => 'Only restaurant owners can create employees',
                ];
            }

            // Check if owner has restaurant
            if (!$currentUser->restaurant_id) {
                return [
                    'success' => false,
                    'error' => 'Owner does not have a restaurant',
                ];
            }

            // Validate role - owner can create only admin, manager, waiter
            $allowedRoles = ['admin', 'manager', 'waiter'];
            if (!in_array($data['role'], $allowedRoles)) {
                return [
                    'success' => false,
                    'error' => 'Invalid role. Allowed roles: ' . implode(', ', $allowedRoles),
                ];
            }

            // Create user
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'restaurant_id' => $currentUser->restaurant_id,
            ]);

            // Assign role
            $user->assignRole($data['role']);

            return [
                'success' => true,
                'message' => 'Employee created successfully',
                'data' => $user->load('roles', 'permissions', 'restaurant'),
            ];
        } catch (\Exception $e) {
            Log::error('Error creating employee: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => 'Failed to create employee: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get user by ID.
     */
    public function getUserById(User $currentUser, int $userId): array
    {
        $user = User::with(['roles', 'permissions', 'restaurant'])->find($userId);

        if (!$user) {
            return [
                'success' => false,
                'error' => 'User not found',
            ];
        }

        // Check access rights
        if ($currentUser->hasRole('owner') &&
            $user->restaurant_id === $currentUser->restaurant_id) {
            return [
                'success' => true,
                'data' => $user,
            ];
        }

        return [
            'success' => false,
            'error' => 'Unauthorized',
        ];
    }

    /**
     * Update user.
     */
    public function updateUser(User $currentUser, User $user, array $data): array
    {
        try {
            // Only owner can update employees in their restaurant
            if (!($currentUser->hasRole('owner') &&
                  $user->restaurant_id === $currentUser->restaurant_id)) {
                return [
                    'success' => false,
                    'error' => 'Unauthorized',
                ];
            }

            // Update user
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $user->update($data);

            // Update role if provided
            if (isset($data['role'])) {
                $user->syncRoles([$data['role']]);
            }

            return [
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user->fresh(['roles', 'permissions', 'restaurant']),
            ];
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => 'Failed to update user: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Delete user.
     */
    public function deleteUser(User $currentUser, User $user): array
    {
        try {
            // Only owner can delete employees in their restaurant
            if (!($currentUser->hasRole('owner') &&
                  $user->restaurant_id === $currentUser->restaurant_id)) {
                return [
                    'success' => false,
                    'error' => 'Unauthorized',
                ];
            }
            // Owner cannot delete themselves
            if ($user->id === $currentUser->id) {
                return [
                    'success' => false,
                    'error' => 'Cannot delete yourself',
                ];
            }

            $user->delete();

            return [
                'success' => true,
                'message' => 'User deleted successfully',
            ];
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => 'Failed to delete user: ' . $e->getMessage(),
            ];
        }
    }
}

