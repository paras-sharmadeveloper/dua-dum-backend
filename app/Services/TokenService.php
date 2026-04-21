<?php

namespace App\Services;

use App\Models\Venue;
use App\Models\Token;
use App\Models\VenueCategory;
use App\Models\VenueCategoryCounter;
use App\Models\VenueCategoryGroup;
use App\Models\VenueCategoryRange;
use App\Models\City;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\HelperServices\DatatableService;
use App\Services\Location\LocationService;
use App\Services\FaceRecognitionService;

class TokenService
{
    protected $locationService;
    protected $faceRecognitionService;

    public function __construct(LocationService $locationService, FaceRecognitionService $faceRecognitionService)
    {
        $this->locationService = $locationService;
        $this->faceRecognitionService = $faceRecognitionService;
    }

    public function getAvailableVenues(?string $userIp = null, ?string $testCity = null)
    {
        try {
            $query = Venue::where('status', 'Active')
                ->select(['id','venue_name','venue_code','venue_address_eng','venue_address_urdu','general_dua_token','general_dum_token','working_lady_dua_token','location_group_id']);

            $userCityName = null;

            // Check if test city is provided (for localhost testing)
            if ($testCity) {
                $userCityName = $testCity;
                Log::info('Using test city parameter', ['city' => $testCity]);
            }
            // If user IP provided, filter by location
            elseif ($userIp) {
                $userLocation = $this->locationService->getUserLocation($userIp);
                
                if ($userLocation && isset($userLocation['city'])) {
                    $userCityName = $userLocation['city'];
                    Log::info('Got city from IP geolocation', [
                        'ip' => $userIp,
                        'city' => $userCityName
                    ]);
                } else {
                    Log::info('No location data for IP, showing all venues', ['ip' => $userIp]);
                }
            }

            // If we have a city name from either test or API, filter venues
            if ($userCityName) {
                $city = City::where('city_name', 'LIKE', $userCityName)->first();
                
                if ($city) {
                    $cityId = $city->Id;
                    
                    Log::info('Filtering venues by city', [
                        'city_name' => $userCityName,
                        'city_id' => $cityId
                    ]);

                    // Filter venues based on location_groups that contain the user's city ID
                    $query->whereHas('locationGroup', function($q) use ($cityId) {
                        $q->where('status', 'Active')
                          ->where(function($subQuery) use ($cityId) {
                              // Check if cities column contains the city ID
                              // Using FIND_IN_SET for comma-separated values
                              $subQuery->whereRaw("FIND_IN_SET(?, cities) > 0", [$cityId]);
                          });
                    });
                } else {
                    Log::warning('City not found in database', [
                        'city_name' => $userCityName,
                        'showing' => 'all venues'
                    ]);
                }
            }

            $venues = $query->orderBy('venue_name')->get();
            
            Log::info('Venues retrieved', [
                'count' => $venues->count(),
                'with_ip_filter' => $userIp !== null
            ]);

            return ['success' => true,'venues' => $venues,'message' => 'Venues retrieved successfully'];
        } catch (\Exception $e) {
            Log::error('Failed to retrieve venues: ' . $e->getMessage());
            return ['success' => false,'venues' => [],'message' => 'Failed to retrieve venues'];
        }
    }

    public function processUserImage($imageData)
    {
        try {
            if (!$imageData) throw new \Exception('No image data provided');
            $image = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
            $image = str_replace(' ', '+', $image);
            $imageName = 'user_' . time() . '_' . uniqid() . '.png';
            $path = 'user_images/' . $imageName;
            Storage::disk('public')->put($path, base64_decode($image));
            return ['success' => true,'image_path' => $path,'image_name' => $imageName,'message' => 'Image processed successfully'];
        } catch (\Exception $e) {
            Log::error('Failed to process user image: ' . $e->getMessage());
            return ['success' => false,'image_path' => null,'message' => 'Failed to process image'];
        }
    }

    public function processQRCode($qrFile)
    {
        try {
            if (!$qrFile || !$qrFile->isValid()) throw new \Exception('Invalid QR code file');
            $qrName = 'qr_' . time() . '_' . uniqid() . '.' . $qrFile->getClientOriginalExtension();
            $path = 'qr_codes/' . $qrName;
            Storage::disk('public')->put($path, file_get_contents($qrFile));
            return ['success' => true,'qr_path' => $path,'qr_name' => $qrName,'message' => 'QR code processed successfully'];
        } catch (\Exception $e) {
            Log::error('Failed to process QR code: ' . $e->getMessage());
            return ['success' => false,'qr_path' => null,'message' => 'Failed to process QR code'];
        }
    }

    public function generateToken(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'user_image' => 'required|string',
                'user_type' => 'required|in:normal_person,working_lady',
                'venue_id' => 'required|exists:venues,id',
                'service_type' => 'required|in:dua,dum',
                'user_name' => 'required|string|min:2',
                'city' => 'required|string|min:2',
                'phone_number' => 'required|string|min:10'
            ]);

            if ($request->input('user_type') === 'working_lady') {
                $request->validate(['qr_code' => 'required|file|image|mimes:jpeg,png,jpg,gif|max:2048']);
            }

            $venue = Venue::findOrFail($validatedData['venue_id']);

            $imageResult = $this->processUserImage($validatedData['user_image']);
            if (!$imageResult['success']) throw new \Exception($imageResult['message']);

            $qrResult = null;
            if ($request->input('user_type') === 'working_lady') {
                if ($request->hasFile('qr_code')) {
                    $qrResult = $this->processQRCode($request->file('qr_code'));
                    if (!$qrResult['success']) throw new \Exception($qrResult['message']);
                } else {
                    throw new \Exception('QR code is required for working lady');
                }
            }

            DB::beginTransaction();

            $tokenMeta = $this->generateTokenMeta($venue, $validatedData['user_type'], $validatedData['service_type']);

            try {
                $token = Token::create([
                    'venue_id' => $validatedData['venue_id'],
                    'venue_category_group_id' => $tokenMeta['group_id'],
                    'venue_category_id' => $tokenMeta['category_id'],
                    'token_number' => (string) $tokenMeta['number'],
                    'token_code' => $tokenMeta['code'],
                    'user_type' => $validatedData['user_type'],
                    'service_type' => $validatedData['service_type'],
                    'user_name' => $validatedData['user_name'],
                    'city' => $validatedData['city'],
                    'phone_number' => $validatedData['phone_number'],
                    'user_image_path' => $imageResult['image_path'],
                    'qr_code_path' => $qrResult ? $qrResult['qr_path'] : null,
                    'status' => 'Pending'
                ]);

                $counter = VenueCategoryCounter::where('venue_id', $validatedData['venue_id'])
                    ->where('venue_category_group_id', $tokenMeta['group_id'])
                    ->where('venue_category_id', $tokenMeta['category_id'])
                    ->lockForUpdate()
                    ->first();

                if ($counter) {
                    $counter->increment('assigned_token_count');
                }

                DB::commit();

                $tokenData = [
                    'id' => $token->id,
                    'token_number' => (string) $tokenMeta['number'],
                    'token_code' => $tokenMeta['code'],
                    'user_type' => $validatedData['user_type'],
                    'venue_id' => $validatedData['venue_id'],
                    'venue_name' => $venue->venue_name,
                    'service_type' => $validatedData['service_type'],
                    'user_name' => $validatedData['user_name'],
                    'city' => $validatedData['city'],
                    'phone_number' => $validatedData['phone_number'],
                    'user_image_path' => $imageResult['image_path'],
                    'qr_code_path' => $qrResult ? $qrResult['qr_path'] : null,
                    'status' => 'pending',
                    'created_at' => now()->format('Y-m-d H:i:s'),
                ];

                Log::info('Token saved to database successfully', $tokenData);
                
                // Send image to face recognition API
                $this->processFaceRecognition($validatedData['user_image'], $validatedData['user_name'], $token->id);
                
            } catch (\Exception $dbError) {
                DB::rollBack();
                throw new \Exception('Failed to save token to database: ' . $dbError->getMessage());
            }

            return ['success' => true,'token_data' => $tokenData,'message' => 'Token generated successfully! You will receive further instructions on your provided phone number.'];

        } catch (\Exception $e) {
            Log::error('Token generation failed: ' . $e->getMessage());
            return ['success' => false,'message' => 'Token generation failed: ' . $e->getMessage()];
        }
    }

    private function generateTokenMeta($venue, $userType, $serviceType)
    {
        $groupCode = $userType === 'working_lady' ? 'WL' : 'NP';
        $group = VenueCategoryGroup::where('code', $groupCode)->first();
        if (!$group) throw new \Exception("VenueCategoryGroup with code $groupCode not found.");

        $serviceType = strtoupper($serviceType);
        $category = VenueCategory::where('name', $serviceType)->first();
        if (!$category) throw new \Exception("VenueCategory with name $serviceType not found.");

        $counter = VenueCategoryCounter::where('venue_id', $venue->id)
            ->where('venue_category_group_id', $group->id)
            ->where('venue_category_id', $category->id)
            ->lockForUpdate()
            ->first();
        if (!$counter) throw new \Exception("VenueCategoryCounter not found for this combination.");

        $range = VenueCategoryRange::where('venue_id', $venue->id)
            ->where('venue_category_group_id', $group->id)
            ->where('venue_category_id', $category->id)
            ->first();
        if (!$range) throw new \Exception("VenueCategoryRange not found for this combination.");

        $nextNumber = ($counter->last_issued_no && $counter->last_issued_no > 0)
            ? ($counter->last_issued_no + 1)
            : $range->range_start;

        if ($nextNumber > $range->range_end) throw new \Exception("No more tokens available for this venue and service type.");

        $counter->last_issued_no = $nextNumber;
        $counter->save();

        $date = date('Ymd');
        $tokenCode = "{$venue->venue_code}-{$group->code}-{$serviceType}-{$nextNumber}-{$date}";

        return ['number' => $nextNumber,'code' => $tokenCode,'group_id' => $group->id,'category_id' => $category->id];
    }

    public function checkTokenAvailability($venueId, $userType, $serviceType)
    {
        try {
            $venue = Venue::findOrFail($venueId);
            $availableTokens = 0;
            if ($userType === 'working_lady' && $serviceType === 'dua') {
                $availableTokens = $venue->working_lady_dua_token;
            } elseif ($userType === 'normal_person' && $serviceType === 'dua') {
                $availableTokens = $venue->general_dua_token;
            } elseif ($serviceType === 'dum') {
                $availableTokens = $venue->general_dum_token;
            }
            return ['success' => true,'available' => $availableTokens > 0,'count' => $availableTokens,'message' => $availableTokens > 0 ? 'Tokens available' : 'No tokens available'];
        } catch (\Exception $e) {
            Log::error('Failed to check token availability: ' . $e->getMessage());
            return ['success' => false,'available' => false,'message' => 'Failed to check availability'];
        }
    }

    /**
     * Get available user types and service types for a venue based on venue_category_counters
     */
    public function getVenueAvailability($venueId, $userType = null)
    {
        try {
            $query = VenueCategoryCounter::where('venue_id', $venueId)
                ->whereColumn('assigned_token_count', '<', 'requested_token_count')
                ->with(['venueCategoryGroup', 'venueCategory']);

            // If user type is provided, filter by it
            if ($userType) {
                $groupCode = $userType === 'normal_person' ? 'NP' : 'WL';
                $query->whereHas('venueCategoryGroup', function($q) use ($groupCode) {
                    $q->where('code', $groupCode);
                });
            }

            $counters = $query->get();

            Log::info('Venue availability check', [
                'venue_id' => $venueId,
                'user_type' => $userType,
                'counters_count' => $counters->count(),
                'counters' => $counters->map(function($c) {
                    return [
                        'group_code' => $c->venueCategoryGroup ? $c->venueCategoryGroup->code : null,
                        'group_name' => $c->venueCategoryGroup ? $c->venueCategoryGroup->name : null,
                        'category_name' => $c->venueCategory ? $c->venueCategory->name : null,
                        'requested' => $c->requested_token_count,
                        'assigned' => $c->assigned_token_count
                    ];
                })
            ]);

            $availableUserTypes = [];
            $availableServiceTypes = [];

            foreach ($counters as $counter) {
                // Get user type from venue_category_group using code
                if ($counter->venueCategoryGroup) {
                    $code = $counter->venueCategoryGroup->code;
                    if ($code === 'NP') {
                        $availableUserTypes['normal_person'] = true;
                    } elseif ($code === 'WL') {
                        $availableUserTypes['working_lady'] = true;
                    }
                }

                // Get service type from venue_category using name
                if ($counter->venueCategory) {
                    $name = strtoupper($counter->venueCategory->name);
                    if ($name === 'DUA') {
                        $availableServiceTypes['dua'] = true;
                    } elseif ($name === 'DUM') {
                        $availableServiceTypes['dum'] = true;
                    }
                }
            }

            $result = [
                'success' => true,
                'user_types' => [
                    'normal_person' => isset($availableUserTypes['normal_person']),
                    'working_lady' => isset($availableUserTypes['working_lady'])
                ],
                'service_types' => [
                    'dua' => isset($availableServiceTypes['dua']),
                    'dum' => isset($availableServiceTypes['dum'])
                ]
            ];

            Log::info('Venue availability result', $result);

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to get venue availability: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to get venue availability'
            ];
        }
    }

    public static function updateStatus(string $id, string $status): bool
    {
        $token = Token::find($id);
        if (!$token) {
            return false;
        }
        $token->status = $status;
        $token->save();
        return true;
    }

    /**
     * Build tokens datatable data via service (controller remains thin)
     */
    public function getTokensData(Request $request, DatatableService $dataTableService)
    {
        try {
            $query = DB::table('tokens')
                ->leftJoin('venues', 'tokens.venue_id', '=', 'venues.id')
                ->select([
                    'tokens.id',
                    'tokens.token_code',
                    'tokens.token_number',
                    'venues.venue_name as venue',
                    'tokens.user_type',
                    'tokens.service_type',
                    'tokens.user_name',
                    'tokens.city',
                    'tokens.user_image_path',
                    'tokens.phone_number',
                    DB::raw('(SELECT MAX(t2.created_at) FROM tokens t2 WHERE t2.phone_number = tokens.phone_number) as last_phone_date'),
                    'tokens.status',
                    DB::raw('CAST(tokens.created_at AS CHAR) as created_at'),
                ])->orderBy('tokens.created_at', 'desc');
            
            // Apply status filter based on filter_type
            $filterType = $request->input('filter_type', 'all');
            Log::info('Filter type: ' . $filterType);
            
            if ($filterType === 'token_applications') {
                $query->whereIn('tokens.status', ['Pending', 'Disapproved']);
            } elseif ($filterType === 'approved_applications') {
                $query->where('tokens.status', 'Approved');
            }
            
            // Apply date range filter
            $dateRange = $request->input('date_range');
            if ($dateRange) {
                Log::info('Date range filter: ' . $dateRange);
                $dates = explode(' - ', $dateRange);
                if (count($dates) === 2) {
                    $startDate = trim($dates[0]);
                    $endDate = trim($dates[1]);
                    $query->whereBetween('tokens.created_at', [$startDate, $endDate]);
                }
            }

            $columns = [
                ['name' => 'tokens.id', 'searchable' => true],
                ['name' => 'tokens.token_code', 'searchable' => true],
                ['name' => 'tokens.token_number', 'searchable' => true],
                ['name' => 'venues.venue_name', 'searchable' => true],
                ['name' => 'tokens.user_type', 'searchable' => true],
                ['name' => 'tokens.service_type', 'searchable' => true],
                ['name' => 'tokens.user_name', 'searchable' => true],
                ['name' => 'tokens.city', 'searchable' => true],
                ['name' => 'user_image_path', 'searchable' => false],
                ['name' => 'tokens.phone_number', 'searchable' => true],
                ['name' => 'last_phone_date', 'searchable' => false],
                ['name' => 'tokens.status', 'searchable' => true],
                ['name' => 'tokens.created_at', 'searchable' => false],
            ];

            return $dataTableService->getDataTableData($request, $query, $columns);
        } catch (\Exception $e) {
            Log::error('Error in getTokensData: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Check if a phone has a token on current date
     */
    public function checkPhone(string $phone): bool
    {
        return Token::where('phone_number', $phone)
            ->whereDate('created_at', now()->toDateString())
            ->exists();
    }

    /**
     * Validate working lady status by ID
     */
    public function validateWorkingLadyStatus(string $id)
    {
        try {
            $workingLady = \App\Models\WorkingLady::find($id);

            if (!$workingLady) {
                return [
                    'exists' => false,
                    'status' => null,
                    'message' => 'Working lady not found'
                ];
            }

            return [
                'exists' => true,
                'status' => $workingLady->status,
                'message' => 'Working lady found',
                'data' => [
                    'id' => $workingLady->id,
                    'name' => trim($workingLady->first_name . ' ' . $workingLady->last_name),
                    'phone_number' => $workingLady->phone_number,
                    'designation' => $workingLady->designation,
                    'company_name' => $workingLady->company_name
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Failed to validate working lady: ' . $e->getMessage());
            return [
                'exists' => false,
                'status' => null,
                'message' => 'Error validating working lady'
            ];
        }
    }

    /**
     * Decode QR code image and extract ID
     */
    public function decodeQRCode($qrFile)
    {
        try {
            if (!$qrFile || !$qrFile->isValid()) {
                throw new \Exception('Invalid QR code file');
            }

            // Store QR temporarily for processing
            $tempPath = $qrFile->store('temp', 'public');
            $fullPath = Storage::disk('public')->path($tempPath);

            // Try to decode using Zxing PHP library
            $decoded = $this->extractIdFromQR($fullPath);

            // Clean up temp file
            Storage::disk('public')->delete($tempPath);

            if ($decoded) {
                return [
                    'success' => true,
                    'id' => $decoded,
                    'message' => 'QR code decoded successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'id' => null,
                    'message' => 'Could not decode QR code'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Failed to decode QR code: ' . $e->getMessage());
            return [
                'success' => false,
                'id' => null,
                'message' => 'Error decoding QR code: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Extract ID from QR code image using Zxing library
     */
    private function extractIdFromQR($imagePath)
    {
        try {
            // Check if Zxing library is available
            if (class_exists('\Zxing\QrReader')) {
                $qrcode = new \Zxing\QrReader($imagePath);
                $text = $qrcode->text();
                return $text ?: null;
            }
            
            // Fallback: Try to use command line tool if available
            if (function_exists('shell_exec')) {
                // Try using zbarimg if installed
                $output = shell_exec("zbarimg -q --raw " . escapeshellarg($imagePath) . " 2>&1");
                if ($output && trim($output)) {
                    return trim($output);
                }
            }
            
            Log::warning('No QR decoder library available. Please install zxing/zxing or khanamiryan/qrcode-detector-decoder');
            return null;
            
        } catch (\Exception $e) {
            Log::error('Error extracting ID from QR: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Process face recognition for the token
     * 
     * @param string $userImage - Base64 encoded image or file path
     * @param string $userName - Name from token
     * @param string $tokenId - Token UUID
     */
    protected function processFaceRecognition($userImage, $userName, $tokenId)
    {
        try {
            // Always use userImage as imagePath
            $imagePath = $userImage;
            
            // Extract base64 if image is stored as data URI
            $imageBase64 = $userImage;
            if (strpos($userImage, 'data:image') === 0) {
                $imageBase64 = explode(',', $userImage)[1];
            } elseif (file_exists(storage_path('app/public/' . $userImage))) {
                // If it's a file path, read and encode it
                $imageContent = file_get_contents(storage_path('app/public/' . $userImage));
                $imageBase64 = base64_encode($imageContent);
            } else {
                // Assume it's already a path stored in DB
                if (file_exists(public_path($userImage))) {
                    $imageContent = file_get_contents(public_path($userImage));
                    $imageBase64 = base64_encode($imageContent);
                }
            }

            // Call face recognition service with image path
            $this->faceRecognitionService->recognizeFace($imageBase64, $userName, $tokenId, $imagePath);
            
        } catch (\Exception $e) {
            Log::error('Face recognition processing failed: ' . $e->getMessage());
            // Don't throw - face recognition failure shouldn't stop token creation
        }
    }
}