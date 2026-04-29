<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Services\Location\LocationService;

class LocationController extends Controller
{
    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    // List location groups with pagination + search
    public function index(Request $request): JsonResponse
    {
        try {
            $query = $this->locationService->getAllLocations();

            if ($request->search) {
                $query->where('name', 'like', "%{$request->search}%");
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $perPage   = $request->per_page ?? 10;
            $locations = $query->latest()->paginate($perPage);

            // Resolve country names and city names from IDs
            $locations->getCollection()->transform(function ($location) {
                // Country name
                $country = \App\Models\Country::find($location->country_id);
                $location->country_name = $country ? $country->name : null;
                // City names
                $cityIds = array_filter(explode(',', $location->cities ?? ''));
                if (!empty($cityIds)) {
                    $location->city_names = \App\Models\City::whereIn('id', $cityIds)
                        ->pluck('city_name')
                        ->toArray();
                } else {
                    $location->city_names = [];
                }

                return $location;
            });

            return response()->json($locations);
        } catch (\Exception $e) {
            Log::error('Locations index error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to load locations' . $e->getMessage()], 500);
        }
    }

    // Get all countries (for dropdowns)
    public function getCountries(): JsonResponse
    {
        try {
            $countries = $this->locationService->getAllCountries();
            return response()->json(['data' => $countries]);
        } catch (\Exception $e) {
            Log::error('Get countries error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to load countries'], 500);
        }
    }

    // Get cities by country ID
    public function getCitiesByCountry(Request $request): JsonResponse
    {
        try {
            $request->validate(['country_id' => 'required']);

            $cities = $this->locationService->getCitiesByCountry($request->country_id);

            return response()->json(['data' => $cities]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Get cities error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to load cities'], 500);
        }
    }

    // Create location group
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name'         => 'required|string|max:255',
                'country_code' => 'required|string|max:10',
                'city_ids'     => 'required|array|min:1',
                'city_ids.*'   => 'required|string',
            ]);

            $locationGroup = $this->locationService->createLocationGroup(
                $validated['country_code'],
                $validated['city_ids'],
                $validated['name'],
            );

            return response()->json([
                'message' => 'Location group created successfully',
                'data'    => $locationGroup,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Location store error: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // Get single location group
    public function show(string $id): JsonResponse
    {
        try {
            $location = $this->locationService->getLocationGroupById($id);
            return response()->json(['data' => $location]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json(['message' => 'Location not found'], 404);
        } catch (\Exception $e) {
            Log::error('Location show error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to load location'], 500);
        }
    }

    // Update location group
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name'         => 'required|string|max:255',
                'country_code' => 'required|string|max:10',
                'city_ids'     => 'required|array|min:1',
                'city_ids.*'   => 'required|string',
                'status'       => 'required|string|in:Active,Inactive',
            ]);

            $locationGroup = $this->locationService->updateLocationGroup(
                $id,
                $validated['name'],
                $validated['status'],
                $validated['country_code'],
                $validated['city_ids'],
            );

            return response()->json([
                'message' => 'Location group updated successfully',
                'data'    => $locationGroup,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json(['message' => 'Location not found'], 404);
        } catch (\Exception $e) {
            Log::error('Location update error: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // Delete location group
    public function destroy(string $id): JsonResponse
    {
        try {
            $location = $this->locationService->getLocationGroupById($id);
            $location->delete();

            return response()->json(['message' => 'Location group deleted successfully']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json(['message' => 'Location not found'], 404);
        } catch (\Exception $e) {
            Log::error('Location destroy error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete location'], 500);
        }
    }
}
