<?php

use App\Http\Controllers\Location\LocationController;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\WorkingLadyController;
use App\Http\Controllers\FacialRecognitionController;
use Illuminate\Support\Facades\Route;



// Route::get('/test-apiip', function () {
//     $ip = "67.250.186.196"; // Replace with dynamic IP if needed
//     $access_key = env('APIIP_KEY');
//     ; // Replace with your real API key

//     // dd($access_key);

//     $response = Http::get("https://apiip.net/api/check", [
//         'ip' => $ip,
//         'accessKey' => $access_key
//     ]);

//     return $response->json(); // Shows the API response
// });




Route::get('/dashboard', function () {
    return view('pages.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/location', [LocationController::class, 'index'])->name('location.index');
Route::get('/location/create', [LocationController::class, 'create'])->name('location.create');
Route::post('/location/get-cities', [LocationController::class, 'getCitiesByCountry'])->name('location.get-cities');
Route::post('/location/store', [LocationController::class, 'createLocationGroup'])->name('location.store');
Route::get('/location/{id}/edit', [LocationController::class, 'edit'])->name('location.edit');
Route::put('/location/{id}', [LocationController::class, 'update'])->name('location.update');
Route::post('/get-locations', [LocationController::class, 'getLocationsData'])->name('location.get-locations');


Route::prefix('venue')->group(function () {
    Route::get('/', [VenueController::class, 'index'])->name('venue.index');
    Route::get('/create', [VenueController::class, 'create'])->name('venue.create');
    Route::post('/store', [VenueController::class, 'store'])->name('venue.store');
    Route::post('/get-venues', [VenueController::class, 'getAllVenues'])->name('venue.get-venues');
    Route::get('/{id}/edit', [VenueController::class, 'edit'])->name('venue.edit');
    Route::put('/{id}', [VenueController::class, 'update'])->name('venue.update');
    Route::put('/{id}/status', [VenueController::class, 'updateStatus'])->name('venue.update-status');
    Route::get('/{id}/details', [VenueController::class, 'getVenueDetails'])->name('venue.get-venue-details');
});

Route::prefix('token-registration')->group(function () {
    Route::get('/get-venues', [TokenController::class, 'getVenues'])->name('token-registration.get-venues');
    Route::post('/venue-availability', [TokenController::class, 'getVenueAvailability'])->name('token-registration.venue-availability');
    Route::post('/save', [TokenController::class, 'save'])->name('token-registration.save');
    Route::post('/generate-token', [TokenController::class, 'generateToken'])->name('token-registration.generate-token');
    Route::post('/check-availability', [TokenController::class, 'checkAvailability'])->name('token-registration.check-availability');
    Route::post('/check-phone', [TokenController::class, 'checkPhone'])->name('token-registration.check-phone');
    Route::post('/validate-working-lady', [TokenController::class, 'validateWorkingLady'])->name('token-registration.validate-working-lady');
    Route::post('/decode-qr', [TokenController::class, 'decodeQR'])->name('token-registration.decode-qr');
    Route::get('/{locale?}', [TokenController::class, 'index'])->name('token-registration');
});

// Tokens listing
Route::prefix('tokens')->group(function () {
    Route::get('/', [TokenController::class, 'tokensIndex'])->name('tokens.index');
    Route::post('/data', [TokenController::class, 'tokensData'])->name('tokens.data');
    Route::post('/{id}/status', [TokenController::class, 'updateStatus'])->name('tokens.update-status');
    Route::get('/print', [TokenController::class, 'printPage'])->name('tokens.print');
    Route::post('/search', [TokenController::class, 'searchToken'])->name('tokens.search');
    Route::post('/update-print-count', [TokenController::class, 'updatePrintCount'])->name('tokens.update-print-count');
});

// Working Ladies
Route::prefix('working-lady')->group(function () {
    Route::get('/', [WorkingLadyController::class, 'index'])->name('working-lady.index');
    Route::post('/data', [WorkingLadyController::class, 'getData'])->name('working-lady.data');
    Route::get('/create', [WorkingLadyController::class, 'create'])->name('working-lady.create');
    Route::post('/store', [WorkingLadyController::class, 'store'])->name('working-lady.store');
    Route::get('/{id}/edit', [WorkingLadyController::class, 'edit'])->name('working-lady.edit');
    Route::put('/{id}', [WorkingLadyController::class, 'update'])->name('working-lady.update');
    Route::delete('/{id}', [WorkingLadyController::class, 'destroy'])->name('working-lady.destroy');
    Route::put('/{id}/status', [WorkingLadyController::class, 'updateStatus'])->name('working-lady.update-status');
});

// Facial Recognition
Route::prefix('facial-recognition')->group(function () {
    Route::get('/users', [FacialRecognitionController::class, 'users'])->name('facial-recognition.users');
    Route::get('/manual-mappings', [FacialRecognitionController::class, 'manualMappings'])->name('facial-recognition.manual-mappings');
    Route::get('/users/data', [FacialRecognitionController::class, 'getUsersData'])->name('facial-recognition.users.data');
});


require __DIR__ . '/auth.php';