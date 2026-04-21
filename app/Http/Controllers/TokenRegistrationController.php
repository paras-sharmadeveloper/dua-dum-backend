<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\HelperServices\DeviceIdentificationService;
use App\Services\TokenRegistrationService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;


class TokenRegistrationController extends Controller
{
    protected $deviceIdentificationService;
    protected $tokenRegistrationService;

    public function __construct(
        DeviceIdentificationService $deviceIdentificationService,
        TokenRegistrationService $tokenRegistrationService
    ) {
        $this->deviceIdentificationService = $deviceIdentificationService;
        $this->tokenRegistrationService = $tokenRegistrationService;
    }

    public function index(Request $request, $locale = '')
    {
        $isMobile = $this->deviceIdentificationService->isMobileDevice($request);
        if ($locale) {
            App::setLocale($locale);
        } else {
            App::setLocale('en');
        }
        if (!$isMobile) {
            return view('token-registration.error');
        }
        return view('token-registration.index', compact('locale'));
    }

    /**
     * Get available venues for token registration
     */
    public function getVenues(Request $request)
    {
        try {
            $result = $this->tokenRegistrationService->getAvailableVenues();

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'venues' => $result['venues'],
                    'message' => $result['message']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'venues' => [],
                    'message' => $result['message']
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error fetching venues: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'venues' => [],
                'message' => 'Failed to fetch venues'
            ], 500);
        }
    }

    /**
     * Generate token for user registration
     */
    public function generateToken(Request $request)
    {
        try {
            $result = $this->tokenRegistrationService->generateToken($request);

            if ($result['success']) {
                return redirect()->back()->with([
                    'success' => true,
                    'message' => $result['message'],
                    'token_data' => $result['token_data']
                ]);
            } else {
                return redirect()->back()->withErrors([
                    'error' => $result['message']
                ])->withInput();
            }
        } catch (\Exception $e) {
            Log::error('Token generation error: ' . $e->getMessage());
            return redirect()->back()->withErrors([
                'error' => 'Token generation failed. Please try again.'
            ])->withInput();
        }
    }

    /**
     * Check token availability for specific venue and service type
     */
    public function checkAvailability(Request $request)
    {
        try {
            $request->validate([
                'venue_id' => 'required|exists:venue,id',
                'user_type' => 'required|in:normal_person,working_lady',
                'service_type' => 'required|in:dua,dum'
            ]);

            $result = $this->tokenRegistrationService->checkTokenAvailability(
                $request->venue_id,
                $request->user_type,
                $request->service_type
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error checking availability: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'available' => false,
                'message' => 'Failed to check availability'
            ], 500);
        }
    }

    /**
     * Legacy save method - keeping for backward compatibility
     */
    public function save(Request $request)
    {
        $data = $request->all();
        Log::info($data);
        if (isset($data['user_image'])) {
            $image = $data['user_image'];
            // Remove the "data:image/png;base64," part
            $image = preg_replace('/^data:image\/\w+;base64,/', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = 'user_' . time() . '.png';

            // Save to storage/app/public/user_images
            Storage::disk('public')->put('user_images/' . $imageName, base64_decode($image));

            // Optionally, save $imageName or path to DB
            // $user->image = 'user_images/' . $imageName;
            // $user->save();
        }

        // Continue with your logic (redirect, etc.)
        return redirect()->back()->with('success', 'Image saved successfully!');
    }
}