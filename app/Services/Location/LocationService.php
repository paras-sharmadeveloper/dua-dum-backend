<?php

namespace App\Services\Location;


use App\Models\Country;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\LocationGroup;
use App\Models\LocationGroupDetail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class LocationService
{
    public function getCountryByISO(string $countryCode)
    {
        return $country = Country::where('iso', strtoupper($countryCode))->value('id');
    }
    public function getCitiesByCountry(string $countryID)
    {
        $cities = City::where('country_id', $countryID)->get();
        return $cities;
    }

    public function createLocationGroup(string $countryCode, array $cityIds, string $groupingName): LocationGroup
    {
        DB::beginTransaction();

        try {
            $locationGroup = LocationGroup::create([
                'id' => Str::uuid(),
                'name' => $groupingName, // Dynamic name
                'country_id' => $this->getCountryByISO($countryCode),
                'cities' => implode(',', $cityIds), // Convert array to comma-separated string
                'status' => 'Active',
            ]);

            Log::info($locationGroup);

            DB::commit();

            return $locationGroup;

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Location group creation failed: {$e->getMessage()}");
            throw new \Exception("Could not create location group");
        }
    }

    public function updateLocationGroup(string $id, string $name, string $status, string $countryCode, array $cityIds): LocationGroup
    {
        DB::beginTransaction();

        try {
            $locationGroup = LocationGroup::findOrFail($id);

            // Update location group
            $locationGroup->update([
                'name' => $name,
                'status' => $status,
                'country_id' => $this->getCountryByISO($countryCode),
                'cities' => implode(',', $cityIds),
            ]);
            DB::commit();

            return $locationGroup;

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Location group update failed: {$e->getMessage()}");
            throw new \Exception("Could not update location group");
        }
    }

    public function getAllLocations()
    {
        try {
            $locations = LocationGroup::query();
            return $locations;
        } catch (\Throwable $th) {
            return [];
        }
    }

    public function getLocationGroupById(string $id)
    {
        return LocationGroup::findOrFail($id);
    }

    public function getCountryById(string $id)
    {
        return Country::where('id', $id)->first();
    }

    public function getAllCountries()
    {
        return Country::all();
    }

    /**
     * Get user's location based on IP address using apiip.net API
     *
     * @param string $ip User's IP address
     * @return array|null Returns ['city' => string, 'country' => string] or null on failure
     */
    public function getUserLocation(string $ip): ?array
    {
        try {
            $apiKey = env('APIIP_KEY');
            
            if (empty($apiKey)) {
                Log::warning('APIIP_KEY not configured');
                return null;
            }

            // Handle localhost/private IPs - return null for local development
            if ($this->isPrivateIp($ip)) {
                Log::info("Private IP detected: {$ip}. Skipping API call.");
                return null;
            }

            $response = Http::timeout(5)->get("https://apiip.net/api/check", [
                'ip' => $ip,
                'accessKey' => $apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Location API Response', [
                    'ip' => $ip,
                    'city' => $data['city'] ?? null,
                    'country' => $data['countryName'] ?? null
                ]);

                return [
                    'city' => $data['city'] ?? null,
                    'country' => $data['countryName'] ?? null,
                    'country_code' => $data['countryCode'] ?? null
                ];
            }

            Log::error('Location API failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Location service error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if IP is private/localhost
     *
     * @param string $ip
     * @return bool
     */
    private function isPrivateIp(string $ip): bool
    {
        // Check for localhost
        if (in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
            return true;
        }

        // Check for private IP ranges
        $private_ranges = [
            '10.0.0.0|10.255.255.255',
            '172.16.0.0|172.31.255.255',
            '192.168.0.0|192.168.255.255'
        ];

        $ip_long = ip2long($ip);
        if ($ip_long === false) {
            return true; // Invalid IP, treat as private
        }

        foreach ($private_ranges as $range) {
            list($start, $end) = explode('|', $range);
            if ($ip_long >= ip2long($start) && $ip_long <= ip2long($end)) {
                return true;
            }
        }

        return false;
    }


}