<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Services\TokenService;
use App\Models\Token;

class TokenController extends Controller
{
    protected $tokenService;

    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    // List tokens with filters + pagination
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Token::with('venue');

            if ($request->status)     $query->where('status', $request->status);
            if ($request->user_type)  $query->where('user_type', $request->user_type);
            if ($request->service)    $query->where('service_type', $request->service);
            if ($request->token_code) $query->where('token_code', 'like', "%{$request->token_code}%");
            if ($request->token_num)  $query->where('token_number', $request->token_num);
            if ($request->name)       $query->where('name', 'like', "%{$request->name}%");
            if ($request->city)       $query->where('city', 'like', "%{$request->city}%");
            if ($request->phone)      $query->where('phone_number', 'like', "%{$request->phone}%");
            if ($request->venue)      $query->whereHas('venue', fn($q) => $q->where('venue_name', 'like', "%{$request->venue}%"));
            if ($request->from)       $query->whereDate('created_at', '>=', $request->from);
            if ($request->to)         $query->whereDate('created_at', '<=', $request->to);

            $perPage = $request->per_page ?? 10;
            $tokens  = $query->latest()->paginate($perPage);

            return response()->json($tokens);
        } catch (\Exception $e) {
            Log::error('Tokens index error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to load tokens'], 500);
        }
    }

    // Approve token
    public function approve(string $id): JsonResponse
    {
        try {
            $token = Token::findOrFail($id);
            $token->update(['status' => 'Approved']);
            return response()->json(['message' => 'Token approved successfully', 'data' => $token]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json(['message' => 'Token not found'], 404);
        } catch (\Exception $e) {
            Log::error('Token approve error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to approve token'], 500);
        }
    }

    // Reject token
    public function reject(Request $request, string $id): JsonResponse
    {
        try {
            $request->validate(['reason' => 'nullable|string|max:500']);

            $token = Token::findOrFail($id);
            $token->update([
                'status'           => 'Disapproved',
                'rejection_reason' => $request->reason,
            ]);

            return response()->json(['message' => 'Token rejected successfully', 'data' => $token]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json(['message' => 'Token not found'], 404);
        } catch (\Exception $e) {
            Log::error('Token reject error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to reject token'], 500);
        }
    }

    // Search token by ID (QR scan)
    public function search(Request $request): JsonResponse
    {
        try {
            $request->validate(['token_id' => 'required|string']);

            $token = Token::with('venue')->find($request->token_id);

            if (!$token) {
                return response()->json(['success' => false, 'message' => 'Token not found'], 404);
            }

            $token->increment('checked_in_count');
            $token->refresh();

            return response()->json(['success' => true, 'data' => $token]);
        } catch (\Exception $e) {
            Log::error('Token search error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to search token'], 500);
        }
    }

    // Update print count
    public function updatePrintCount(Request $request, string $id): JsonResponse
    {
        try {
            $token = Token::findOrFail($id);
            $token->increment('print_count');

            return response()->json([
                'success'     => true,
                'message'     => 'Print count updated',
                'print_count' => $token->print_count,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json(['message' => 'Token not found'], 404);
        } catch (\Exception $e) {
            Log::error('Print count update error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update print count'], 500);
        }
    }

    // Get available venues
    public function getVenues(Request $request): JsonResponse
    {
        try {
            $result = $this->tokenService->getAvailableVenues($request->ip(), $request->test_city);

            return response()->json($result, $result['success'] ? 200 : 500);
        } catch (\Exception $e) {
            Log::error('Get venues error: ' . $e->getMessage());
            return response()->json(['success' => false, 'venues' => [], 'message' => 'Failed to fetch venues'], 500);
        }
    }

    // Check phone
    public function checkPhone(Request $request): JsonResponse
    {
        $data = $request->validate(['phone_number' => 'required|string']);
        $exists = $this->tokenService->checkPhone($data['phone_number']);
        return response()->json(['exists' => $exists]);
    }

    // Validate working lady
    public function validateWorkingLady(Request $request): JsonResponse
    {
        try {
            $request->validate(['id' => 'required|string']);
            $result = $this->tokenService->validateWorkingLadyStatus($request->id);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['exists' => false, 'message' => 'Validation failed'], 500);
        }
    }

    // Generate token
    public function generateToken(Request $request): JsonResponse
    {
        try {
            $result = $this->tokenService->generateToken($request);
            return response()->json([
                'success'    => $result['success'],
                'message'    => $result['message'],
                'token_data' => $result['token_data'] ?? null,
            ], $result['success'] ? 200 : 422);
        } catch (\Exception $e) {
            Log::error('Token generation error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Token generation failed'], 500);
        }
    }

    // Venue availability
    public function venueAvailability(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'venue_id'  => 'required|exists:venues,id',
                'user_type' => 'nullable|in:normal_person,working_lady',
            ]);
            $result = $this->tokenService->getVenueAvailability($data['venue_id'], $data['user_type'] ?? null);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch venue availability'], 500);
        }
    }
}
