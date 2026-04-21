<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use App\Models\LocationGroup;
use Illuminate\Http\Request;
use App\Services\Location\LocationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\HelperServices\DataTableService;
use App\Models\LocationGroupDetail;
use App\Models\Country;

class LocationController extends Controller
{
    protected $locationService;
    protected $dataTableService;
    public function __construct(LocationService $locationService, DataTableService $dataTableService)
    {

        $this->locationService = $locationService;
        $this->dataTableService = $dataTableService;
    }
    public function index()
    {
        return view('location.index');
    }

    public function create()
    {
        return view('location.create');
    }

    public function getCitiesByCountry(Request $request)
    {
        Log::info('Request data:', $request->all());
        $request->validate([
            'country_code' => 'required|string|size:2'
        ]);

        try {
            // Find the country by its code
            $countryID = $this->locationService->getCountryByISO($request->country_code);

            if (!$countryID) {
                return response()->json([
                    'success' => false,
                    'message' => 'Country not found'
                ], 404);
            }

            $cities = $this->locationService->getCitiesByCountry($countryID);
            log::info($cities);
            // Format cities for Tagify
            $formattedCities = $cities->map(function ($city) {
                return [
                    'value' => $city->city_name,  // Use city name as value
                    'id' => $city->Id        // Keep ID for reference
                ];
            });

            return response()->json([
                'success' => true,
                'cities' => $formattedCities
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getCitiesByCountry:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error fetching cities: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createLocationGroup(Request $request)
    {
        $validated = $request->validate([
            'country_code' => 'required|string|size:2|exists:countries,iso',
            'cities' => 'required|array|min:1',
            'cities.*' => 'required|integer|exists:cities,id',
            'grouping_name' => 'required|string|max:255'
        ]);

        try {
            $locationGroup = $this->locationService->createLocationGroup(
                $validated['country_code'],
                $validated['cities'],
                $validated['grouping_name'],
            );

            return response()->json([
                'success' => true,
                'message' => 'Location group created successfully',
                'data' => [
                    'location_group_id' => $locationGroup->id
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Location group creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create location group'
            ], 500);
        }
    }

    public function getLocationsData(Request $request)
    {
        $query = $this->locationService->getAllLocations();
        $columns = LocationGroup::getColumns();

        // Use the service to get formatted datatable response
        return $this->dataTableService->getDataTableData(
            $request,
            $query,
            $columns
        );
    }

    public function edit($id)
    {
        try {
            $locationGroup = $this->locationService->getLocationGroupById($id);
            $country = $this->locationService->getCountryById($locationGroup->country_id);
            $countries = $this->locationService->getAllCountries();

            // Get cities for the country
            $cities = $this->locationService->getCitiesByCountry($locationGroup->country_id);

            // Format cities for Tagify
            $formattedCities = $cities->map(function ($city) {
                return [
                    'value' => $city->city_name,
                    'id' => $city->Id
                ];
            });

            $selectedCityIds = explode(',', $locationGroup->cities);

            return view('location.edit', compact('locationGroup', 'country', 'countries', 'formattedCities', 'selectedCityIds'));
        } catch (\Exception $e) {
            Log::error('Error fetching location group for edit: ' . $e->getMessage());
            return redirect()->route('location.index')->with('error', 'Could not fetch location group details');
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:Active,In Active',
            'country_code' => 'required|string|size:2|exists:countries,iso',
            'cities' => 'required|array|min:1',
            'cities.*' => 'required|integer|exists:cities,id'
        ]);

        try {
            $locationGroup = $this->locationService->updateLocationGroup(
                $id,
                $validated['name'],
                $validated['status'],
                $validated['country_code'],
                $validated['cities']
            );

            return response()->json([
                'success' => true,
                'message' => 'Location group updated successfully',
                'data' => [
                    'location_group_id' => $locationGroup->id
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Location group update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update location group'
            ], 500);
        }
    }
}