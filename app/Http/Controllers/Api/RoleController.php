<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Auth\RoleService;
use App\Services\HelperServices\DataTableService;

class RoleController extends Controller
{
    protected $dataTableService;
    protected $roleService;

    public function __construct(DataTableService $dataTableService, RoleService $roleService)
    {
        $this->dataTableService = $dataTableService;
        $this->roleService = $roleService;
    }

    public function index()
    {
        return view("role.index");
    }

    /**
     * Get all roles data for DataTable
     */
    public function getRolesData(Request $request)
    {

        $query = $this->roleService->getAllRoles();
        $columns = [
            ['name' => 'name', 'searchable' => true],
            ['name' => 'action', 'searchable' => false]
        ];

        return $this->dataTableService->getDataTableData(
            $request,
            $query,
            $columns
        );
    }

    public function getAllPermissions()
    {
        $permissions = $this->roleService->getAllPermissions();
        return response()->json($permissions);
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $result = $this->roleService->createRole($request->all());

        if ($result['success']) {
            return response()->json(['message' => $result['message']], 200);
        } else {
            return response()->json(['message' => $result['message']], 422);
        }
    }

    /**
     * Get role by ID for editing
     */
    public function getRole($id)
    {
        $result = $this->roleService->getRole($id);

        if ($result['success']) {
            return response()->json([
                'name' => $result['role']->name,
                'permissions' => $result['permissions']
            ], 200);
        } else {
            return response()->json(['message' => $result['message']], 422);
        }
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:roles,name,' . $id,
                'permissions' => 'nullable|array',
                'permissions.*' => 'exists:permissions,id'
            ]);

            // Make sure permissions is an array even if empty
            $data = $request->all();
            if (!isset($data['permissions'])) {
                $data['permissions'] = [];
            }

            $result = $this->roleService->updateRole($id, $data);

            if ($result['success']) {
                return response()->json(['message' => $result['message']], 200);
            } else {
                return response()->json(['message' => $result['message']], 422);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating role: ' . $e->getMessage()], 500);
        }
    }
}
