<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\HelperServices\DataTableService;
use App\Services\VenueService;
use App\DTOs\Venue\VenueDTO;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

class VenueController extends Controller
{
    protected $venueService;
    protected $dataTableService;

    public function formatDateTime($date)
    {
        if (!$date) {
            return null;
        }
        return date('Y-m-d H:i:s', strtotime($date));
    }



    public function __construct(VenueService $venueService, DataTableService $dataTableService)
    {

        $this->venueService = $venueService;
        $this->dataTableService = $dataTableService;
    }
    public function index()
    {
        return view('venue.index');
    }
    public function create()
    {
        try {
            $fieldAdmins = $this->venueService->getVenueAdmins();
            $venueTypes = $this->venueService->getAllLocationGroups();
            return view('venue.create', compact('fieldAdmins', 'venueTypes'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Failed to get field admins');
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'venue_name' => 'required|string|max:255',
                'venue_code' => 'nullable|string|unique:venues,venue_code',
                'user_id' => 'required|integer',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'location_group_id' => 'required|string',
                'general_dua_token' => 'required|integer|min:0',
                'general_dum_token' => 'required|integer|min:0',
                'working_lady_dua_token' => 'required|integer|min:0',
                'venue_address_eng' => 'required|string',
                'venue_address_urdu' => 'required|string',
                'status_page_note_eng' => 'required|string',
                'status_page_note_urdu' => 'required|string',
                'dua_reason' => 'nullable|string',
                'dum_reason' => 'nullable|string',
            ]);

            $venueData = array_merge($validated, [
                'id' => (string) Str::uuid(),
                'status' => 'Active'
            ]);



            $venueDTO = VenueDTO::fromArray($venueData);
            $venue = $this->venueService->createVenue($venueDTO);

            return response()->json([
                'status' => 'success',
                'message' => 'Venue created successfully',
                'data' => $venue
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Venue creation failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create venue'
            ], 500);
        }
    }

    public function getAllVenues(Request $request)
    {
        try {
            $query = $this->venueService->getAllVenues();

            $columns = VenueDTO::getColumns();

            return $this->dataTableService->getDataTableData(
                $request,
                $query,
                $columns
            );
        } catch (\Exception $e) {
            Log::error('Error fetching venues: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Error fetching venues: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $venue = $this->venueService->getVenueById($id);
            $fieldAdmins = $this->venueService->getVenueAdmins();
            $venueTypes = $this->venueService->getAllLocationGroups();
            return view('venue.edit', compact('venue', 'fieldAdmins', 'venueTypes'));
        } catch (\Exception $e) {
            Log::error('Error fetching venue for edit: ' . $e->getMessage());
            return redirect()->route('venue.index')->with('error', 'Could not fetch venue details');
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|integer',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'location_group_id' => 'required|string',
                'general_dua_token' => 'nullable|integer|min:0',
                'general_dum_token' => 'nullable|integer|min:0',
                'working_lady_dua_token' => 'nullable|integer|min:0',
                'venue_address_eng' => 'required|string',
                'venue_address_urdu' => 'required|string',
                'status_page_note_eng' => 'required|string',
                'status_page_note_urdu' => 'required|string',
                'dua_reason' => 'nullable|string',
                'dum_reason' => 'nullable|string',
            ]);
            // Set default 0 if null
            $validated['general_dua_token'] = $validated['general_dua_token'] ?? 0;
            $validated['general_dum_token'] = $validated['general_dum_token'] ?? 0;
            $validated['working_lady_dua_token'] = $validated['working_lady_dua_token'] ?? 0;

            Log::info($validated);

            $venueData = array_merge($validated, [
                'status' => 'active'
            ]);

            $venueDTO = VenueDTO::fromArray($venueData);
            $venue = $this->venueService->updateVenue($id, $venueDTO);

            return response()->json([
                'status' => 'success',
                'message' => 'Venue updated successfully',
                'data' => $venue
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Venue update failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update venue'
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'required|string|in:Active,In Active'
            ]);

            $venue = $this->venueService->updateVenueStatus($id, $validated['status']);

            return response()->json([
                'status' => 'success',
                'message' => 'Venue status updated successfully',
                'data' => $venue
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in updateStatus: ' . json_encode($e->errors()));
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Venue status update failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update venue status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getVenueDetails($id): JsonResponse
    {
        try {
            $venue = $this->venueService->getVenueById($id);

            if (!$venue) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Venue not found'
                ], 404);
            }

            // Get used token counts from the service
            $usedTokens = $this->venueService->getUsedTokenCounts($id);

            // Format dates


            return response()->json([
                'status' => 'success',
                'data' => [
                    'general_dua_token' => $venue->general_dua_token,
                    'used_general_dua_tokens' => $usedTokens['general_dua'] ?? 0,
                    'working_lady_dua_token' => $venue->working_lady_dua_token,
                    'used_working_lady_dua_tokens' => $usedTokens['working_lady_dua'] ?? 0,
                    'general_dum_token' => $venue->general_dum_token,
                    'used_general_dum_tokens' => $usedTokens['general_dum'] ?? 0,
                    'location_name' => $venue->locationGroup->name ?? 'N/A',
                    'user_name' => $venue->user->name ?? 'N/A',
                    'formatted_start_date' => $this->formatDateTime($venue->start_date),
                    'formatted_end_date' => $this->formatDateTime($venue->end_date),
                    'status' => $venue->status,
                    'address' => $venue->venue_address_eng ?? 'N/A'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching venue details: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load venue details'
            ], 500);
        }
    }
}