<?php

namespace App\Services;

use App\Models\Venue;
use App\Models\Token;
use App\Models\VenueCategory;
use App\Models\VenueCategoryCounter;
use App\Models\VenueCategoryGroup;
use App\Models\VenueCategoryRange;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TokenRegistrationService
{
    /**
     * Get all active venues for token registration
     */
    public function getAvailableVenues()
    {
        try {
            $venues = Venue::where('status', 'Active')
                ->select([
                    'id',
                    'venue_name',
                    'venue_code',
                    'venue_address_eng',
                    'venue_address_urdu',
                    'general_dua_token',
                    'general_dum_token',
                    'working_lady_dua_token'
                ])
                ->orderBy('venue_name')
                ->get();
            return [
                'success' => true,
                'venues' => $venues,
                'message' => 'Venues retrieved successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to retrieve venues: ' . $e->getMessage());
            return [
                'success' => false,
                'venues' => [],
                'message' => 'Failed to retrieve venues'
            ];
        }
    }

    /**
     * Process and save user image
     */
    public function processUserImage($imageData)
    {
        try {
            if (!$imageData) {
                throw new \Exception('No image data provided');
            }

            // Remove the "data:image/png;base64," part
            $image = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
            $image = str_replace(' ', '+', $image);
            $imageName = 'user_' . time() . '_' . uniqid() . '.png';

            // Save to storage/app/public/user_images
            $path = 'user_images/' . $imageName;
            Storage::disk('public')->put($path, base64_decode($image));

            return [
                'success' => true,
                'image_path' => $path,
                'image_name' => $imageName,
                'message' => 'Image processed successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to process user image: ' . $e->getMessage());
            return [
                'success' => false,
                'image_path' => null,
                'message' => 'Failed to process image'
            ];
        }
    }

    /**
     * Process and save QR code image for working ladies
     */
    public function processQRCode($qrFile)
    {
        try {
            if (!$qrFile || !$qrFile->isValid()) {
                throw new \Exception('Invalid QR code file');
            }

            $qrName = 'qr_' . time() . '_' . uniqid() . '.' . $qrFile->getClientOriginalExtension();
            $path = 'qr_codes/' . $qrName;

            Storage::disk('public')->put($path, file_get_contents($qrFile));

            return [
                'success' => true,
                'qr_path' => $path,
                'qr_name' => $qrName,
                'message' => 'QR code processed successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to process QR code: ' . $e->getMessage());
            return [
                'success' => false,
                'qr_path' => null,
                'message' => 'Failed to process QR code'
            ];
        }
    }

    /**
     * Generate token for user
     */
    public function generateToken(Request $request)
    {
        try {


            // Basic validation first
            $validatedData = $request->validate([
                'user_image' => 'required|string',
                'user_type' => 'required|in:normal_person,working_lady',
                'venue_id' => 'required|exists:venues,id',
                'service_type' => 'required|in:dua,dum',
                'phone_number' => 'required|string|min:10'
            ]);

            // Additional validation for working lady only
            if ($request->input('user_type') === 'working_lady') {
                $request->validate([
                    'qr_code' => 'required|file|image|mimes:jpeg,png,jpg,gif|max:2048'
                ]);
            }

            // Get venue details
            $venue = Venue::findOrFail($validatedData['venue_id']);

            // Process user image
            $imageResult = $this->processUserImage($validatedData['user_image']);
            if (!$imageResult['success']) {
                throw new \Exception($imageResult['message']);
            }

            // Process QR code if working lady
            $qrResult = null;
            if ($request->input('user_type') === 'working_lady') {
                if ($request->hasFile('qr_code')) {
                    $qrResult = $this->processQRCode($request->file('qr_code'));
                    if (!$qrResult['success']) {
                        throw new \Exception($qrResult['message']);
                    }
                } else {
                    throw new \Exception('QR code is required for working lady');
                }
            }

            // Create token in database within a transaction
            DB::beginTransaction();

            // Generate unique token number and code inside transaction (with row locks)
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
                    'phone_number' => $validatedData['phone_number'],
                    'user_image_path' => $imageResult['image_path'],
                    'qr_code_path' => $qrResult ? $qrResult['qr_path'] : null,
                    'status' => 'pending'
                ]);

                // Increment assigned token count for the corresponding counter
                $counter = VenueCategoryCounter::where('venue_id', $validatedData['venue_id'])
                    ->where('venue_category_group_id', $tokenMeta['group_id'])
                    ->where('venue_category_id', $tokenMeta['category_id'])
                    ->lockForUpdate()
                    ->first();

                if ($counter) {
                    $counter->increment('assigned_token_count');
                }



                DB::commit();

                // Prepare response data
                $tokenData = [
                    'id' => $token->id,
                    'token_number' => (string) $tokenMeta['number'],
                    'token_code' => $tokenMeta['code'],
                    'user_type' => $validatedData['user_type'],
                    'venue_id' => $validatedData['venue_id'],
                    'venue_name' => $venue->venue_name,
                    'service_type' => $validatedData['service_type'],
                    'phone_number' => $validatedData['phone_number'],
                    'user_image_path' => $imageResult['image_path'],
                    'qr_code_path' => $qrResult ? $qrResult['qr_path'] : null,
                    'status' => 'pending',
                    'created_at' => $token->created_at->format('Y-m-d H:i:s'),
                ];

                // Log the token generation
                Log::info('Token saved to database successfully', $tokenData);
            } catch (\Exception $dbError) {
                DB::rollBack();
                throw new \Exception('Failed to save token to database: ' . $dbError->getMessage());
            }

            return [
                'success' => true,
                'token_data' => $tokenData,
                'message' => 'Token generated successfully! You will receive further instructions on your provided phone number.'
            ];

        } catch (\Exception $e) {
            Log::error('Token generation failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Token generation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generate unique token number based on venue and service type
     */
    private function generateTokenMeta($venue, $userType, $serviceType)
    {
        // Get group code and info only once
        $groupCode = $userType === 'working_lady' ? 'WL' : 'NP';
        $group = VenueCategoryGroup::where('code', $groupCode)->first();
        if (!$group) {
            throw new \Exception("VenueCategoryGroup with code $groupCode not found.");
        }

        $serviceType = strtoupper($serviceType);
        $category = VenueCategory::where('name', $serviceType)->first();
        if (!$category) {
            throw new \Exception("VenueCategory with name $serviceType not found.");
        }

        // Fetch and lock the counter row for issuing
        $counter = VenueCategoryCounter::where('venue_id', $venue->id)
            ->where('venue_category_group_id', $group->id)
            ->where('venue_category_id', $category->id)
            ->lockForUpdate()
            ->first();
        if (!$counter) {
            throw new \Exception("VenueCategoryCounter not found for this combination.");
        }
        // Range boundaries
        $range = VenueCategoryRange::where('venue_id', $venue->id)
            ->where('venue_category_group_id', $group->id)
            ->where('venue_category_id', $category->id)
            ->first();

        if (!$range) {
            throw new \Exception("VenueCategoryRange not found for this combination.");
        }

        // last_issued_no stores the absolute last issued number (0 when none issued yet)
        // Compute the next absolute token number
        $nextNumber = ($counter->last_issued_no && $counter->last_issued_no > 0)
            ? ($counter->last_issued_no + 1)
            : $range->range_start;

        // Capacity check
        if ($nextNumber > $range->range_end) {
            throw new \Exception("No more tokens available for this venue and service type.");
        }

        // Persist the new last issued absolute number
        $counter->last_issued_no = $nextNumber;
        $counter->save();

        // Build token code
        $date = date('Ymd');
        $tokenCode = "{$venue->venue_code}-{$group->code}-{$serviceType}-{$nextNumber}-{$date}";

        return [
            'number' => $nextNumber,
            'code' => $tokenCode,
            'group_id' => $group->id,
            'category_id' => $category->id,
        ];
    }

    /**
     * Check token availability for venue and service type
     */

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

            return [
                'success' => true,
                'available' => $availableTokens > 0,
                'count' => $availableTokens,
                'message' => $availableTokens > 0 ? 'Tokens available' : 'No tokens available'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to check token availability: ' . $e->getMessage());
            return [
                'success' => false,
                'available' => false,
                'message' => 'Failed to check availability'
            ];
        }
    }
}