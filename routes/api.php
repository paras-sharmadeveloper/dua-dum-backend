<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VenueController;
use App\Http\Controllers\Api\TokenController;
use App\Http\Controllers\Api\WorkingLadyController;
use App\Http\Controllers\Api\SavedFilterController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LocationController;

// ─── PUBLIC ──────────────────────────────────────────────
Route::post('/login', [AuthController::class, 'login']);

// ─── PROTECTED (Sanctum) ─────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout',          [AuthController::class, 'logout']);
    Route::get('/me',               [AuthController::class, 'me']);
    Route::get('/dashboard/stats',  [AuthController::class, 'dashboardStats']);

    // Venues
    Route::prefix('venues')->group(function () {
        Route::get('/',                 [VenueController::class, 'index']);
        Route::post('/',                [VenueController::class, 'store']);
        Route::get('/form-data',        [VenueController::class, 'formData']);
        Route::get('/{id}',             [VenueController::class, 'show']);
        Route::put('/{id}',             [VenueController::class, 'update']);
        Route::patch('/{id}/status',    [VenueController::class, 'updateStatus']);
    });

    // Tokens
    Route::prefix('tokens')->group(function () {
        Route::get('/',                     [TokenController::class, 'index']);
        Route::patch('/{id}/approve',       [TokenController::class, 'approve']);
        Route::patch('/{id}/reject',        [TokenController::class, 'reject']);
        Route::post('/{id}/print',          [TokenController::class, 'updatePrintCount']);
        Route::post('/search',              [TokenController::class, 'search']);
        Route::get('/venues',               [TokenController::class, 'getVenues']);
        Route::post('/check-phone',         [TokenController::class, 'checkPhone']);
        Route::post('/generate',            [TokenController::class, 'generateToken']);
        Route::post('/venue-availability',  [TokenController::class, 'venueAvailability']);
        Route::post('/validate-working-lady', [TokenController::class, 'validateWorkingLady']);
    });

    // Working Ladies
    Route::prefix('working-ladies')->group(function () {
        Route::get('/',                 [WorkingLadyController::class, 'index']);
        Route::post('/',                [WorkingLadyController::class, 'store']);
        Route::get('/{id}',             [WorkingLadyController::class, 'show']);
        Route::put('/{id}',             [WorkingLadyController::class, 'update']);
        Route::delete('/{id}',          [WorkingLadyController::class, 'destroy']);
        Route::patch('/{id}/status',    [WorkingLadyController::class, 'updateStatus']);
    });

    // Users
    Route::prefix('users')->group(function () {
        Route::get('/',                     [UserController::class, 'index']);
        Route::post('/',                    [UserController::class, 'store']);
        Route::get('/roles',                [UserController::class, 'getAllRoles']);
        Route::get('/{id}',                 [UserController::class, 'show']);
        Route::put('/{id}',                 [UserController::class, 'update']);
        Route::delete('/{id}',              [UserController::class, 'destroy']);
        Route::patch('/{id}/toggle-status', [UserController::class, 'toggleStatus']);
        Route::patch('/{id}/reset-password', [UserController::class, 'resetPassword']);
    });

    // Locations
    Route::prefix('locations')->group(function () {
        Route::get('/',                     [LocationController::class, 'index']);
        Route::post('/',                    [LocationController::class, 'store']);
        Route::get('/countries',            [LocationController::class, 'getCountries']);
        Route::post('/cities',              [LocationController::class, 'getCitiesByCountry']);
        Route::get('/{id}',                 [LocationController::class, 'show']);
        Route::put('/{id}',                 [LocationController::class, 'update']);
        Route::delete('/{id}',              [LocationController::class, 'destroy']);
    });

    // Saved Filters
    Route::prefix('saved-filters')->group(function () {
        Route::get('/',                         [SavedFilterController::class, 'index']);
        Route::post('/',                        [SavedFilterController::class, 'store']);
        Route::post('/{savedFilter}/default',   [SavedFilterController::class, 'setDefault']);
        Route::delete('/{savedFilter}',         [SavedFilterController::class, 'destroy']);
    });
});
