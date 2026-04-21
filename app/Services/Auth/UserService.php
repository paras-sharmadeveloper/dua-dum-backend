<?php
namespace App\Services\Auth;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getAllUsers()
    {
        try {
            $users = User::with('roles');
            return $users;
        } catch (\Throwable $th) {
            return [];
        }
    }

    public function getAllRoles()
    {
        return Role::all();
    }

    public function createUser($data)
    {
        try {
            DB::beginTransaction();

            // Create user
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'status' => $data['status'] ?? 'Active'
            ]);

            // Assign role if specified
            if (isset($data['role_id']) && !empty($data['role_id'])) {
                $role = Role::findById($data['role_id']);
                $user->assignRole($role);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'User created successfully'
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Error creating user: ' . $th->getMessage()
            ];
        }
    }

    public function getUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $userRole = $user->roles->pluck('id')->first();

            return [
                'success' => true,
                'user' => $user,
                'role_id' => $userRole
            ];
        } catch (\Throwable $th) {
            return [
                'success' => false,
                'message' => 'Error fetching user: ' . $th->getMessage()
            ];
        }
    }

    public function updateUser($id, $data)
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($id);

            // Update basic info
            $updateData = [
                'name' => $data['name'],
                'email' => $data['email']
            ];

            // Update password only if provided
            if (isset($data['password']) && !empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            // Update status if provided
            if (isset($data['status'])) {
                $updateData['status'] = $data['status'];
            }

            $user->update($updateData);

            // Update role if specified
            if (isset($data['role_id'])) {
                // Remove current roles
                $user->roles()->detach();

                // Assign new role
                if (!empty($data['role_id'])) {
                    $role = Role::findById($data['role_id']);
                    $user->assignRole($role);
                }
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'User updated successfully'
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Error updating user: ' . $th->getMessage()
            ];
        }
    }

    public function toggleUserStatus($id)
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($id);
            $newStatus = ($user->status === 'Active') ? 'In Active' : 'Active';

            $user->update([
                'status' => $newStatus
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'User status changed to ' . $newStatus,
                'new_status' => $newStatus
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Error toggling user status: ' . $th->getMessage()
            ];
        }
    }

    public function deleteUser($id)
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($id);
            $user->delete();

            DB::commit();

            return [
                'success' => true,
                'message' => 'User deleted successfully'
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Error deleting user: ' . $th->getMessage()
            ];
        }
    }
}