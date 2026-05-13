<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Services\WorkingLadyService;
use App\Models\Token;

class WorkingLadyController extends Controller
{
    protected $workingLadyService;
    public function __construct(WorkingLadyService $workingLadyService)
    {
        $this->workingLadyService = $workingLadyService;
    }

    // List all working ladies
    public function index(Request $request): JsonResponse
    {
        try {
            $query = $this->workingLadyService->getQuery();

            if ($request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('first_name',   'like', "%{$request->search}%")
                      ->orWhere('last_name',  'like', "%{$request->search}%")
                      ->orWhere('email',       'like', "%{$request->search}%")
                      ->orWhere('phone_number','like', "%{$request->search}%");
                });
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            if ($request->case_type) {
                $query->where('case_type', $request->case_type);
            }

            $ladies = $query->latest()->get();

            return response()->json(['data' => $ladies]);
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

    // QR scan lookup — returns working lady + token history
    public function scan(string $id): JsonResponse
    {
        try {
            $result = $this->workingLadyService->find($id);

            if (!$result['success']) {
                return response()->json(['found' => false, 'message' => 'Working lady not found'], 404);
            }

            $workingLady = $result['data'];

            $tokens = Token::with('venue')
                ->where('phone_number', $workingLady->phone_number)
                ->where('user_type', 'working_lady')
                ->latest()
                ->get()
                ->map(function ($token) {
                    return [
                        'id'               => $token->id,
                        'token_code'       => $token->token_code,
                        'token_number'     => $token->token_number,
                        'service_type'     => $token->service_type,
                        'status'           => $token->status,
                        'venue_name'       => $token->venue ? $token->venue->venue_name : null,
                        'user_image_url'   => $token->user_image_path
                            ? asset('storage/' . $token->user_image_path)
                            : null,
                        'checked_in_count' => $token->checked_in_count ?? 0,
                        'created_at'       => $token->created_at?->toDateTimeString(),
                    ];
                });

            return response()->json([
                'found'        => true,
                'working_lady' => $workingLady,
                'tokens'       => $tokens,
                'total_visits' => $tokens->sum('checked_in_count'),
            ]);
        } catch (\Exception $e) {
            Log::error('Working lady scan error: ' . $e->getMessage());
            return response()->json(['found' => false, 'message' => 'Scan lookup failed'], 500);
        }
    }

    // Upload / replace profile image
    public function uploadImage(Request $request, string $id): JsonResponse
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            ]);

            $result = $this->workingLadyService->uploadProfileImage($id, $request->file('image'));

            if ($result['success']) {
                return response()->json(['success' => true, 'url' => $result['url']]);
            }

            return response()->json(['message' => $result['message']], 422);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Working lady image upload error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to upload image'], 500);
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
