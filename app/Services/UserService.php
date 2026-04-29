<?php

namespace App\Services;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService
{
    /**
     * Get base query for API pagination/filtering
     */
    public function getQuery()
    {
        return User::with('roles')->select([
            'id',
            'name',
            'email',
            'status',
            'created_at',
        ]);
    }

    /**
     * Get all roles (for dropdowns)
     */
    public function getAllRoles()
    {
        return Role::select('id', 'name')->get();
    }

    /**
     * Create a new user
     */
    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'status'   => $data['status'] ?? 'Active',
            ]);

            if (!empty($data['role'])) {
                $user->assignRole($data['role']);
            }

            DB::commit();

            return ['success' => true, 'data' => $user->load('roles'), 'message' => 'User created successfully'];
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('UserService::create - ' . $th->getMessage());
            return ['success' => false, 'message' => 'Error creating user: ' . $th->getMessage()];
        }
    }

    /**
     * Find a user by ID
     */
    public function find(string $id)
    {
        try {
            $user = User::with('roles')->findOrFail($id);
            return ['success' => true, 'data' => $user];
        } catch (\Throwable $th) {
            Log::error('UserService::find - ' . $th->getMessage());
            return ['success' => false, 'message' => 'User not found'];
        }
    }

    /**
     * Update a user
     */
    public function update(string $id, array $data)
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($id);

            $updateData = [
                'name'  => $data['name'],
                'email' => $data['email'],
            ];

            if (isset($data['status'])) {
                $updateData['status'] = $data['status'];
            }

            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $user->update($updateData);

            if (!empty($data['role'])) {
                $user->syncRoles([$data['role']]);
            }

            DB::commit();

            return ['success' => true, 'data' => $user->load('roles'), 'message' => 'User updated successfully'];
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('UserService::update - ' . $th->getMessage());
            return ['success' => false, 'message' => 'Error updating user: ' . $th->getMessage()];
        }
    }

    /**
     * Toggle user Active/Inactive status
     */
    public function toggleStatus(string $id)
    {
        try {
            DB::beginTransaction();

            $user      = User::findOrFail($id);
            $newStatus = ($user->status === 'Active') ? 'In Active' : 'Active';
            $user->update(['status' => $newStatus]);

            DB::commit();

            return [
                'success' => true,
                'data'    => $user,
                'message' => 'User status changed to ' . $newStatus,
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('UserService::toggleStatus - ' . $th->getMessage());
            return ['success' => false, 'message' => 'Error toggling user status: ' . $th->getMessage()];
        }
    }

    /**
     * Reset user password
     */
    public function resetPassword(string $id, string $password)
    {
        try {
            $user = User::findOrFail($id);
            $user->update(['password' => Hash::make($password)]);

            return ['success' => true, 'message' => 'Password reset successfully'];
        } catch (\Throwable $th) {
            Log::error('UserService::resetPassword - ' . $th->getMessage());
            return ['success' => false, 'message' => 'Error resetting password: ' . $th->getMessage()];
        }
    }

    /**
     * Delete a user
     */
    public function delete(string $id)
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($id);
            $user->delete();

            DB::commit();

            return ['success' => true, 'message' => 'User deleted successfully'];
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('UserService::delete - ' . $th->getMessage());
            return ['success' => false, 'message' => 'Error deleting user: ' . $th->getMessage()];
        }
    }
}
