<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Auth\UserService;
use Illuminate\Support\Facades\Log;
use App\Services\HelperServices\DataTableService;
class UserController extends Controller
{
    protected $dataTableService;
    protected $userService;

    public function __construct(UserService $userService, DataTableService $dataTableService)
    {
        $this->userService = $userService;
        $this->dataTableService = $dataTableService;
    }

    /**
     * Display users listing
     */
    public function index()
    {
        return view("user.index");
    }

    /**
     * Get all users data for DataTable
     */
    public function getUsersData(Request $request)
    {
        $query = $this->userService->getAllUsers()->with('roles');
        $columns = [
            ['name' => 'id', 'searchable' => true],
            ['name' => 'name', 'searchable' => true],
            ['name' => 'email', 'searchable' => true],
            ['name' => 'status', 'searchable' => true],
        ];

        return $this->dataTableService->getDataTableData(
            $request,
            $query,
            $columns
        );
    }

    /**
     * Get all roles for user assignment
     */
    public function getAllRoles()
    {
        $roles = $this->userService->getAllRoles();
        return response()->json($roles);
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'nullable|exists:roles,id',
            'status' => 'nullable|in:Active,Inactive'
        ]);

        $result = $this->userService->createUser($request->all());

        if ($result['success']) {
            return response()->json(['message' => $result['message']], 200);
        } else {
            return response()->json(['message' => $result['message']], 422);
        }
    }

    /**
     * Get user by ID for editing
     */
    public function getUser($id)
    {
        $result = $this->userService->getUser($id);

        if ($result['success']) {
            return response()->json([
                'name' => $result['user']->name,
                'email' => $result['user']->email,
                'status' => $result['user']->status,
                'role_id' => $result['role_id']
            ], 200);
        } else {
            return response()->json(['message' => $result['message']], 422);
        }
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        try {
            $validationRules = [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $id,
                'role_id' => 'nullable|exists:roles,id',
                'status' => 'nullable|in:Active,Inactive'
            ];

            // Only validate password if it's provided
            if ($request->filled('password')) {
                $validationRules['password'] = 'string|min:8|confirmed';
            }

            $validated = $request->validate($validationRules);

            $result = $this->userService->updateUser($id, $request->all());

            if ($result['success']) {
                return response()->json(['message' => $result['message']], 200);
            } else {
                return response()->json(['message' => $result['message']], 422);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating user: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Toggle user status (Active/Inactive)
     */
    public function toggleStatus($id)
    {
        try {
            $result = $this->userService->toggleUserStatus($id);

            if ($result['success']) {
                return response()->json([
                    'message' => $result['message'],
                    'new_status' => $result['new_status']
                ], 200);
            } else {
                return response()->json(['message' => $result['message']], 422);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error toggling user status: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy($id)
    {
        $result = $this->userService->deleteUser($id);

        if ($result['success']) {
            return response()->json(['message' => $result['message']], 200);
        } else {
            return response()->json(['message' => $result['message']], 422);
        }
    }
}