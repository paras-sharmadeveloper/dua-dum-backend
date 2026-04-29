<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    // List users with pagination + search
    public function index(Request $request): JsonResponse
    {
        try {
            $query = $this->userService->getQuery();

            if ($request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%")
                        ->orWhere('email', 'like', "%{$request->search}%");
                });
            }

            if ($request->role) {
                $query->whereHas('roles', fn($q) => $q->where('name', $request->role));
            }

            if ($request->status !== null) {
                $query->where('status', $request->status);
            }

            $perPage = $request->per_page ?? 10;
            $users   = $query->latest()->paginate($perPage);

            return response()->json($users);
        } catch (\Exception $e) {
            Log::error('Users index error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to load users'], 500);
        }
    }

    // Get all roles (for dropdowns)
    public function getAllRoles(): JsonResponse
    {
        try {
            $result = $this->userService->getAllRoles();
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Get roles error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to load roles'], 500);
        }
    }

    // Create new user
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'role'     => 'required|string|exists:roles,name',
                'status'   => 'nullable|boolean',
            ]);

            $result = $this->userService->create($validated);

            if ($result['success']) {
                return response()->json(['message' => $result['message'], 'data' => $result['data'] ?? null], 201);
            }

            return response()->json(['message' => $result['message']], 422);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('User store error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create user'], 500);
        }
    }

    // Get single user
    public function show(string $id): JsonResponse
    {
        try {
            $result = $this->userService->find($id);

            if (!$result['success']) {
                return response()->json(['message' => $result['message']], 404);
            }

            return response()->json(['data' => $result['data']]);
        } catch (\Exception $e) {
            Log::error('User show error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to load user'], 500);
        }
    }

    // Update user
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name'   => 'required|string|max:255',
                'email'  => 'required|email|unique:users,email,' . $id,
                'role'   => 'required|string|exists:roles,name',
                'status' => 'nullable|boolean',
            ]);

            $result = $this->userService->update($id, $validated);

            if ($result['success']) {
                return response()->json(['message' => $result['message'], 'data' => $result['data'] ?? null]);
            }

            return response()->json(['message' => $result['message']], 422);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('User update error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update user'], 500);
        }
    }

    // Toggle user active/inactive status
    public function toggleStatus(string $id): JsonResponse
    {
        try {
            $result = $this->userService->toggleStatus($id);

            if ($result['success']) {
                return response()->json(['message' => $result['message'], 'data' => $result['data'] ?? null]);
            }

            return response()->json(['message' => $result['message']], 422);
        } catch (\Exception $e) {
            Log::error('User toggle status error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to toggle user status'], 500);
        }
    }

    // Reset user password
    public function resetPassword(Request $request, string $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);

            $result = $this->userService->resetPassword($id, $validated['password']);

            if ($result['success']) {
                return response()->json(['message' => $result['message']]);
            }

            return response()->json(['message' => $result['message']], 422);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('User reset password error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to reset password'], 500);
        }
    }

    // Delete user
    public function destroy(string $id): JsonResponse
    {
        try {
            $result = $this->userService->delete($id);

            if ($result['success']) {
                return response()->json(['message' => $result['message']]);
            }

            return response()->json(['message' => $result['message']], 422);
        } catch (\Exception $e) {
            Log::error('User destroy error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete user'], 500);
        }
    }
}
