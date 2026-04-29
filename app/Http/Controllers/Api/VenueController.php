<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\VenueService;
use App\DTOs\Venue\VenueDTO;

class VenueController extends Controller
{
    protected $venueService;

    public function __construct(VenueService $venueService)
    {
        $this->venueService = $venueService;
    }

    // List all venues with pagination
    public function index(Request $request): JsonResponse
    {
        try {
            $query = $this->venueService->getAllVenues();

            if ($request->search) {
                $query->where('venue_name', 'like', "%{$request->search}%");
            }
            if ($request->status) {
                $query->where('status', $request->status);
            }

            $perPage = $request->per_page ?? 10;
            $venues  = $query->latest()->paginate($perPage);

            return response()->json($venues);
        } catch (\Exception $e) {
            Log::error('Venues index error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to load venues'], 500);
        }
    }

    // Get location groups for create/edit dropdowns
    public function formData(): JsonResponse
    {
        try {
            $fieldAdmins  = $this->venueService->getVenueAdmins();
            $locationGroups = $this->venueService->getAllLocationGroups();

            return response()->json([
                'field_admins'    => $fieldAdmins,
                'location_groups' => $locationGroups,
            ]);
        } catch (\Exception $e) {
            Log::error('Venue form data error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to load form data'], 500);
        }
    }

    // Create venue
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'venue_name'              => 'required|string|max:255',
                'venue_code'              => 'nullable|string|unique:venues,venue_code',
                'user_id'                 => 'required|integer',
                'start_date'              => 'required|date',
                'end_date'                => 'required|date|after:start_date',
                'location_group_id'       => 'required|string',
                'general_dua_token'       => 'required|integer|min:0',
                'general_dum_token'       => 'required|integer|min:0',
                'working_lady_dua_token'  => 'required|integer|min:0',
                'venue_address_eng'       => 'required|string',
                'venue_address_urdu'      => 'required|string',
                'status_page_note_eng'    => 'required|string',
                'status_page_note_urdu'   => 'required|string',
                'dua_reason'              => 'nullable|string',
                'dum_reason'              => 'nullable|string',
            ]);

            $venueDTO = VenueDTO::fromArray(array_merge($validated, [
                'id'     => (string) Str::uuid(),
                'status' => 'Active',
            ]));

            $venue = $this->venueService->createVenue($venueDTO);

            return response()->json([
                'message' => 'Venue created successfully',
                'data'    => $venue,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Venue creation failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create venue'], 500);
        }
    }

    // Get single venue
    public function show(string $id): JsonResponse
    {
        try {
            $venue = $this->venueService->getVenueById($id);

            if (!$venue) {
                return response()->json(['message' => 'Venue not found'], 404);
            }

            $usedTokens = $this->venueService->getUsedTokenCounts($id);

            return response()->json([
                'data' => array_merge($venue->toArray(), [
                    'used_general_dua_tokens'       => $usedTokens['general_dua'] ?? 0,
                    'used_working_lady_dua_tokens'  => $usedTokens['working_lady_dua'] ?? 0,
                    'used_general_dum_tokens'       => $usedTokens['general_dum'] ?? 0,
                    'location_name'                 => $venue->locationGroup->name ?? 'N/A',
                    'user_name'                     => $venue->user->name ?? 'N/A',
                    'formatted_start_date'          => $venue->start_date ? date('Y-m-d H:i:s', strtotime($venue->start_date)) : null,
                    'formatted_end_date'            => $venue->end_date ? date('Y-m-d H:i:s', strtotime($venue->end_date)) : null,
                ]),
            ]);
        } catch (\Exception $e) {
            Log::error('Venue show error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to load venue'], 500);
        }
    }

    // Update venue
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id'                 => 'required|integer',
                'start_date'              => 'required|date',
                'end_date'                => 'required|date|after:start_date',
                'location_group_id'       => 'required|string',
                'general_dua_token'       => 'nullable|integer|min:0',
                'general_dum_token'       => 'nullable|integer|min:0',
                'working_lady_dua_token'  => 'nullable|integer|min:0',
                'venue_address_eng'       => 'required|string',
                'venue_address_urdu'      => 'required|string',
                'status_page_note_eng'    => 'required|string',
                'status_page_note_urdu'   => 'required|string',
                'dua_reason'              => 'nullable|string',
                'dum_reason'              => 'nullable|string',
            ]);

            $validated['general_dua_token']      = $validated['general_dua_token'] ?? 0;
            $validated['general_dum_token']      = $validated['general_dum_token'] ?? 0;
            $validated['working_lady_dua_token'] = $validated['working_lady_dua_token'] ?? 0;

            $venueDTO = VenueDTO::fromArray(array_merge($validated, ['status' => 'Active']));
            $venue    = $this->venueService->updateVenue($id, $venueDTO);

            return response()->json(['message' => 'Venue updated successfully', 'data' => $venue]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Venue update failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update venue'], 500);
        }
    }

    // Update status only
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'required|string|in:Active,In Active',
            ]);

            $venue = $this->venueService->updateVenueStatus($id, $validated['status']);

            return response()->json(['message' => 'Venue status updated successfully', 'data' => $venue]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Venue status update failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update venue status'], 500);
        }
    }
}
