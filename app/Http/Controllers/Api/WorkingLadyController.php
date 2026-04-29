<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Services\WorkingLadyService;

class WorkingLadyController extends Controller
{
    protected $workingLadyService;
    public function __construct(WorkingLadyService $workingLadyService)
    {
        $this->workingLadyService = $workingLadyService;
    }

    // List with pagination + search
    public function index(Request $request): JsonResponse
    {
        try {
            $query = $this->workingLadyService->getQuery();

            if ($request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('first_name', 'like', "%{$request->search}%")
                        ->orWhere('last_name',  'like', "%{$request->search}%")
                        ->orWhere('email',       'like', "%{$request->search}%")
                        ->orWhere('phone_number', 'like', "%{$request->search}%");
                });
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            if ($request->case_type) {
                $query->where('case_type', $request->case_type);
            }

            $perPage = $request->per_page ?? 10;
            $ladies  = $query->latest()->paginate($perPage);

            return response()->json($ladies);
        } catch (\Exception $e) {
            Log::error('Working ladies index error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to load working ladies'], 500);
        }
    }

    // Store new working lady
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'first_name'   => 'required|string|max:255',
                'last_name'    => 'required|string|max:255',
                'designation'  => 'required|string|max:255',
                'company_name' => 'required|string|max:255',
                'place_of_work' => 'required|string|max:255',
                'email'        => 'required|email|unique:working_ladies,email',
                'phone_number' => 'required|string|max:255',
                'remarks'      => 'nullable|string',
                'case_type'    => 'required|in:normal,critical',
                'status'       => 'nullable|string|in:Approved,Disapproved,Pending',
            ]);

            $result = $this->workingLadyService->create($validated);

            if ($result['success']) {
                return response()->json(['message' => $result['message'], 'data' => $result['data'] ?? null], 201);
            }

            return response()->json(['message' => $result['message']], 422);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Working lady store error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create working lady'], 500);
        }
    }

    // Get single working lady
    public function show(string $id): JsonResponse
    {
        try {
            $result = $this->workingLadyService->find($id);

            if (!$result['success']) {
                return response()->json(['message' => $result['message']], 404);
            }

            return response()->json(['data' => $result['data']]);
        } catch (\Exception $e) {
            Log::error('Working lady show error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to load working lady'], 500);
        }
    }

    // Update working lady
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'first_name'   => 'required|string|max:255',
                'last_name'    => 'required|string|max:255',
                'designation'  => 'required|string|max:255',
                'company_name' => 'required|string|max:255',
                'place_of_work' => 'required|string|max:255',
                'email'        => 'required|email|unique:working_ladies,email,' . $id,
                'phone_number' => 'required|string|max:255',
                'remarks'      => 'nullable|string',
                'case_type'    => 'required|in:normal,critical',
                'status'       => 'nullable|string|in:Approved,Disapproved,Pending',
            ]);

            $result = $this->workingLadyService->update($id, $validated);

            if ($result['success']) {
                return response()->json(['message' => $result['message'], 'data' => $result['data'] ?? null]);
            }

            return response()->json(['message' => $result['message']], 422);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Working lady update error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update working lady'], 500);
        }
    }

    // Delete working lady
    public function destroy(string $id): JsonResponse
    {
        try {
            $result = $this->workingLadyService->delete($id);

            if ($result['success']) {
                return response()->json(['message' => $result['message']]);
            }

            return response()->json(['message' => $result['message']], 422);
        } catch (\Exception $e) {
            Log::error('Working lady destroy error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete working lady'], 500);
        }
    }

    // Update status
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'required|string|in:Approved,Disapproved,Pending',
            ]);

            $result = $this->workingLadyService->updateStatus($id, $validated['status']);

            if ($result['success']) {
                return response()->json(['message' => $result['message']]);
            }

            return response()->json(['message' => $result['message']], 422);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Working lady status update error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update status'], 500);
        }
    }
}
