<?php

namespace App\Http\Controllers\Auth;

use App\DTOs\Auth\PermissionDTO;
use App\Http\Controllers\Controller;
use App\Services\Auth\PermissionService;
use App\Services\HelperServices\DataTableService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{

    protected $dataTableService;
    protected $permissionService;

    public function __construct(DataTableService $dataTableService, PermissionService $permissionService)
    {
        $this->dataTableService = $dataTableService;
        $this->permissionService = $permissionService;
    }

    public function index()
    {
        // Just return the view, no data
        return view('permission.index');
    }

    public function getPermissionsData(Request $request)
    {
        $query = $this->permissionService->getAllPermissions();
        $columns = PermissionDTO::getColumns();

        // Use the service to get formatted datatable response
        return $this->dataTableService->getDataTableData(
            $request,
            $query,
            $columns
        );
    }

    public function createPermission(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'status' => 'sometimes|string|in:active,inactive'
            ]);

            // Create the permission
            $permission = $this->permissionService->createPermission($validated);

            return response()->json([
                'success' => true,
                'message' => 'Permission created successfully',
                'data' => $permission
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create permission',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function getPermissionForEdit(string $id)
    {

        $permission = $this->permissionService->getPermissionForEdit($id); // Or your model name

        if (!$permission) {

            return response()->json(['error' => 'Permission not found'], 404);
        }

        return response()->json($permission);
    }


    public function updatePermission(Request $request, string $id)
    {
        try {
            // Validate the request data
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:permissions,name,' . $id,
            ]);

            // Find the permission
            $result = $this->permissionService->updatePermission($validated, $id); // Or your model name
            return response()->json([
                'success' => true,
                'message' => 'Permission updated successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            // Handle other errors
            return response()->json([
                'success' => false,
                'message' => 'Failed to update permission',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }


}