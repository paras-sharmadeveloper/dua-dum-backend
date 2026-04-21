<?php
namespace App\Services\Auth;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleService
{
    public function getAllRoles()
    {
        try {
            $roles = Role::query();
            return $roles;
        } catch (\Throwable $th) {
            return [];
        }
    }

    public function getAllPermissions()
    {
        return Permission::all();
    }

    public function createRole($data)
    {
        try {
            DB::beginTransaction();

            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => 'web'
            ]);

            // Process permissions if any exist
            $permissions = [];
            if (!empty($data['permissions'])) {
                $permissions = Permission::whereIn('id', $data['permissions'])->get();
            }

            // Sync permissions to the role
            $role->syncPermissions($permissions);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Role created successfully with ' . count($permissions) . ' permissions'
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Error creating role: ' . $th->getMessage()
            ];
        }
    }

    public function getRole($id)
    {
        try {
            $role = Role::findById($id);
            $rolePermissions = $role->permissions->pluck('id')->toArray();

            return [
                'success' => true,
                'role' => $role,
                'permissions' => $rolePermissions
            ];
        } catch (\Throwable $th) {
            return [
                'success' => false,
                'message' => 'Error fetching role: ' . $th->getMessage()
            ];
        }
    }

    public function updateRole($id, $data)
    {
        try {
            DB::beginTransaction();

            $role = Role::findById($id);
            $role->update([
                'name' => $data['name']
            ]);

            // Always sync permissions, even if empty (to clear existing permissions)
            // This ensures permissions are removed if none are selected
            $permissions = [];
            if (!empty($data['permissions'])) {
                $permissions = Permission::whereIn('id', $data['permissions'])->get();
            }

            // Sync permissions - this will remove any permissions not in the array
            $role->syncPermissions($permissions);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Role updated successfully with ' . count($permissions) . ' permissions'
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Error updating role: ' . $th->getMessage()
            ];
        }
    }

    public function deleteRole($id)
    {
        try {
            DB::beginTransaction();

            $role = Role::findById($id);
            $role->delete();

            DB::commit();

            return [
                'success' => true,
                'message' => 'Role deleted successfully'
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Error deleting role: ' . $th->getMessage()
            ];
        }
    }
}