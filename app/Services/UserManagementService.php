<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Hash;

class UserManagementService
{
    /**
     * Get paginated list of users with optional filtering and sorting.
     */
    public function getPaginatedUsers(
        string $searchTerm = '',
        string $filterRole = '',
        string $sortField = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10
    ): Paginator {
        return User::when($searchTerm, function ($query) use ($searchTerm) {
            return $query->where(function ($q) use ($searchTerm) {
                $q->where('first_name', 'like', "%{$searchTerm}%")
                    ->orWhere('middle_name', 'like', "%{$searchTerm}%")
                    ->orWhere('last_name', 'like', "%{$searchTerm}%")
                    ->orWhere('identity_id', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        })
            ->when($filterRole, function ($query) use ($filterRole) {
                return $query->where('role', $filterRole);
            })
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);
    }

    /**
     * Create a new user.
     */
    public function createUser(array $data): User
    {
        return User::create([
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'identity_id' => $data['identity_id'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'student',
        ]);
    }

    /**
     * Update an existing user.
     */
    public function updateUser(User $user, array $data): User
    {
        $updateData = [
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'identity_id' => $data['identity_id'],
            'email' => $data['email'],
            'role' => $data['role'] ?? $user->role,
        ];

        if (isset($data['password']) && ! empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        return $user;
    }

    /**
     * Delete a user.
     */
    public function deleteUser(User $user): bool
    {
        // Prevent deleting the authenticated user
        if ($user->id === auth()->id()) {
            throw new \Exception('Cannot delete your own account');
        }

        return $user->delete();
    }

    /**
     * Get user by ID.
     */
    public function getUserById(int $id): User
    {
        return User::findOrFail($id);
    }

    /**
     * Check if email exists (excluding a specific user).
     */
    public function emailExists(string $email, ?int $excludeUserId = null): bool
    {
        $query = User::where('email', $email);

        if ($excludeUserId) {
            $query->where('id', '!=', $excludeUserId);
        }

        return $query->exists();
    }

    /**
     * Check if identity ID exists (excluding a specific user).
     */
    public function identityIdExists(string $identityId, ?int $excludeUserId = null): bool
    {
        $query = User::where('identity_id', $identityId);

        if ($excludeUserId) {
            $query->where('id', '!=', $excludeUserId);
        }

        return $query->exists();
    }

    /**
     * Get users by role.
     */
    public function getUsersByRole(string $role)
    {
        return User::where('role', $role)->get();
    }

    /**
     * Get count of users by role.
     */
    public function getUserCountByRole(): array
    {
        return [
            'admin' => User::where('role', 'admin')->count(),
            'faculty' => User::where('role', 'faculty')->count(),
            'student' => User::where('role', 'student')->count(),
        ];
    }
}
