<?php
namespace App\Services\Auth;

use App\DTOs\Auth\PermissionDTO;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class PermissionService
{
    public function getAllPermissions()
    {
        try {
            $permissions = Permission::query();
            return $permissions;
        } catch (\Throwable $th) {
            return [];
        }
    }

    public function createPermission(array $data): Permission
    {
        try {
            return Permission::create([
                'name' => $data['name'],
                'status' => $data['status'] ?? 'Active'
            ]);
        } catch (\Exception $e) {
            // Log::error('Failed to create permission: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getPermissionForEdit(string $id): Permission
    {
        try {
            $permission = Permission::find($id);

            return $permission;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function updatePermission($validated, string $id)
    {
        try {
            $permission = Permission::find($id);

            $permission->update([
                'name' => $validated['name'],
            ]);
            return $permission;
        } catch (\Exception $e) {
            throw $e;
        }
    }


}