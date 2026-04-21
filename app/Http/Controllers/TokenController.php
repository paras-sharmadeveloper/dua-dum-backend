<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use App\Services\HelperServices\DatatableService;
use App\Services\HelperServices\DeviceIdentificationService;
use App\Services\TokenService;
use App\Models\Token;

class TokenController extends Controller
{
    protected $deviceIdentificationService;
    protected $tokenService;
    protected $dataTableService;

    public function __construct(
        DeviceIdentificationService $deviceIdentificationService,
        TokenService $tokenService,
        DatatableService $dataTableService
    ) {
        $this->deviceIdentificationService = $deviceIdentificationService;
        $this->tokenService = $tokenService;
        $this->dataTableService = $dataTableService;
    }

    // Registration stepper view
    public function index(Request $request, $locale = '')
    {
        $isMobile = $this->deviceIdentificationService->isMobileDevice($request);
        if ($locale) {
            App::setLocale($locale);
        } else {
            App::setLocale('en');
        }
        if (!$isMobile) {
            return view('token.error');
        }
        return view('token.index', compact('locale'));
    }

    // Tokens list view
    public function tokensIndex()
    {
        // Render tokens listing view (moved under resources/views/token)
        return view('token.token');
    }

    // JSON data for tokens datatable
    public function tokensData(Request $request)
    {
        try {
            Log::info('Tokens data request received', [
                'filter_type' => $request->input('filter_type'),
                'date_range' => $request->input('date_range'),
                'search' => $request->input('search'),
            ]);

            $request->validate([
                'filter_type' => 'nullable|in:all,token_applications,approved_applications',
                'date_range' => 'nullable|string',
            ]);

            return $this->tokenService->getTokensData($request, $this->dataTableService);
        } catch (\Exception $e) {
            Log::error('Error in tokensData: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ], 500);
        }
    }

    public function updateStatus(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:Approved,Disapproved,Pending',
        ]);

        try {
            $updated = TokenService::updateStatus($id, $validated['status']);
            if (!$updated) {
                return response()->json(['message' => 'Token not found'], 404);
            }

            return response()->json(['message' => 'Token status updated successfully']);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to update token status',
            ], 500);
        }
    }

    // Check if a phone number has already booked a token today
    public function checkPhone(Request $request)
    {
        $data = $request->validate([
            'phone_number' => 'required|string'
        ]);
        $exists = $this->tokenService->checkPhone($data['phone_number']);
        return response()->json(['exists' => $exists]);
    }

    // Get available venues for token registration
    public function getVenues(Request $request)
    {
        try {
            // Get user's IP address
            $userIp = $request->ip();

            // Allow testing with test_city parameter (e.g., ?test_city=Dubai)
            $testCity = $request->input('test_city');

            // Pass IP and test city to service for location-based filtering
            $result = $this->tokenService->getAvailableVenues($userIp, $testCity);

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

    // Get available options for a selected venue
    public function getVenueAvailability(Request $request)
    {
        $data = $request->validate([
            'venue_id' => 'required|exists:venues,id',
            'user_type' => 'nullable|in:normal_person,working_lady'
        ]);

        try {
            $result = $this->tokenService->getVenueAvailability($data['venue_id'], $data['user_type'] ?? null);
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error fetching venue availability: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch venue availability'
            ], 500);
        }
    }

    // Generate token for user registration
    public function generateToken(Request $request)
    {
        try {
            $result = $this->tokenService->generateToken($request);

            // Check if request expects JSON (AJAX request)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => $result['success'],
                    'message' => $result['message'],
                    'token_data' => $result['token_data'] ?? null
                ]);
            }

            // Traditional form submission - redirect
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

            // Check if request expects JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token generation failed. Please try again.'
                ], 500);
            }

            return redirect()->back()->withErrors([
                'error' => 'Token generation failed. Please try again.'
            ])->withInput();
        }
    }

    // Check token availability for specific venue and service type
    public function checkAvailability(Request $request)
    {
        try {
            $request->validate([
                'venue_id' => 'required|exists:venue,id',
                'user_type' => 'required|in:normal_person,working_lady',
                'service_type' => 'required|in:dua,dum'
            ]);

            $result = $this->tokenService->checkTokenAvailability(
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

    // Validate working lady from QR code
    public function validateWorkingLady(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|string'
            ]);

            $result = $this->tokenService->validateWorkingLadyStatus($request->id);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error validating working lady: ' . $e->getMessage());
            return response()->json([
                'exists' => false,
                'status' => null,
                'message' => 'Failed to validate working lady'
            ], 500);
        }
    }

    // Decode QR code image (optional fallback)
    public function decodeQR(Request $request)
    {
        try {
            $request->validate([
                'qr_code' => 'required|file|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $result = $this->tokenService->decodeQRCode($request->file('qr_code'));

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error decoding QR: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'id' => null,
                'message' => 'Failed to decode QR code'
            ], 500);
        }
    }

    // Legacy save method - keeping for backward compatibility
    public function save(Request $request)
    {
        $data = $request->all();
        Log::info($data);
        if (isset($data['user_image'])) {
            $image = $data['user_image'];
            $image = preg_replace('/^data:image\/\w+;base64,/', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = 'user_' . time() . '.png';
            Storage::disk('public')->put('user_images/' . $imageName, base64_decode($image));
        }
        return redirect()->back()->with('success', 'Image saved successfully!');
    }

    // Display print/scan page
    public function printPage()
    {
        return view('token.print');
    }

    // Search token by ID (for QR code scanning)
    public function searchToken(Request $request)
    {
        try {
            $request->validate([
                'token_id' => 'required|string'
            ]);

            $tokenId = $request->input('token_id');

            // Search for token by ID
            $token = Token::with('venue')->find($tokenId);

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token not found with the scanned ID'
                ], 404);
            }

            // Increment checked_in_count
            $token->increment('checked_in_count');
            $token->refresh();

            return response()->json([
                'success' => true,
                'data' => $token,
                'message' => 'Token found successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token ID provided'
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error searching token: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while searching for the token'
            ], 500);
        }
    }

    // Update print count when token is printed
    public function updatePrintCount(Request $request)
    {
        try {
            $request->validate([
                'token_id' => 'required|string'
            ]);

            $tokenId = $request->input('token_id');

            // Find the token
            $token = Token::find($tokenId);

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token not found'
                ], 404);
            }

            // Increment print_count
            $token->increment('print_count');

            return response()->json([
                'success' => true,
                'message' => 'Print count updated successfully',
                'print_count' => $token->print_count
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token ID provided'
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating print count: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating print count'
            ], 500);
        }
    }
}
