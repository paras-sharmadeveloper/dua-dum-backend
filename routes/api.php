<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\Auth\PermissionController;
use App\Http\Controllers\Location\LocationController;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\WorkingLadyController;
use App\Http\Controllers\FacialRecognitionController;
use App\Http\Controllers\SavedFilterController;

// ─── PUBLIC ROUTES ───────────────────────────────────────
Route::post('/login', [AuthController::class, 'login']);
Route::get('/test', function () {
    return "running";
});
// ─── PROTECTED ROUTES (Sanctum) ──────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Dashboard
    Route::get('/dashboard/stats', [AuthController::class, 'dashboardStats']);

    // ── Users ──
    Route::prefix('users')->group(function () {
        Route::get('/',                     [UserController::class, 'index']);
        Route::post('/',                    [UserController::class, 'store']);
        Route::get('/roles',                [UserController::class, 'getAllRoles']);
        Route::get('/{id}',                 [UserController::class, 'getUser']);
        Route::put('/{id}',                 [UserController::class, 'update']);
        Route::delete('/{id}',              [UserController::class, 'destroy']);
        Route::post('/{id}/toggle-status',  [UserController::class, 'toggleStatus']);
        Route::post('/{id}/reset-password', [UserController::class, 'resetPassword']);
    });

    // ── Roles ──
    Route::prefix('roles')->group(function () {
        Route::get('/',                         [RoleController::class, 'index']);
        Route::post('/',                        [RoleController::class, 'store']);
        Route::get('/permissions',              [RoleController::class, 'getAllPermissions']);
        Route::get('/{id}',                     [RoleController::class, 'getRole']);
        Route::put('/{id}',                     [RoleController::class, 'update']);
        Route::delete('/{id}',                  [RoleController::class, 'destroy']);
    });

    // ── Permissions ──
    Route::prefix('permissions')->group(function () {
        Route::get('/',        [PermissionController::class, 'index']);
        Route::post('/',       [PermissionController::class, 'createPermission']);
        Route::get('/{id}',    [PermissionController::class, 'getPermissionForEdit']);
        Route::put('/{id}',    [PermissionController::class, 'updatePermission']);
    });

    // ── Locations ──
    Route::prefix('locations')->group(function () {
        Route::get('/',             [LocationController::class, 'index']);
        Route::post('/',            [LocationController::class, 'createLocationGroup']);
        Route::get('/{id}',         [LocationController::class, 'edit']);
        Route::put('/{id}',         [LocationController::class, 'update']);
        Route::delete('/{id}',      [LocationController::class, 'destroy']);
        Route::post('/cities',      [LocationController::class, 'getCitiesByCountry']);
    });

    // ── Venues ──
    Route::prefix('venues')->group(function () {
        Route::get('/',             [VenueController::class, 'index']);
        Route::post('/',            [VenueController::class, 'store']);
        Route::get('/{id}',         [VenueController::class, 'edit']);
        Route::put('/{id}',         [VenueController::class, 'update']);
        Route::delete('/{id}',      [VenueController::class, 'destroy']);
        Route::patch('/{id}/status', [VenueController::class, 'updateStatus']);
        Route::get('/{id}/details', [VenueController::class, 'getVenueDetails']);
    });

    // ── Tokens ──
    Route::prefix('tokens')->group(function () {
        Route::get('/',                     [TokenController::class, 'tokensIndex']);
        Route::get('/venues',               [TokenController::class, 'getVenues']);
        Route::patch('/{id}/approve',       [TokenController::class, 'approve']);
        Route::patch('/{id}/reject',        [TokenController::class, 'reject']);
        Route::post('/{id}/print',          [TokenController::class, 'updatePrintCount']);
        Route::get('/print',                [TokenController::class, 'printPage']);
        Route::post('/search',              [TokenController::class, 'searchToken']);
        Route::post('/check-availability',  [TokenController::class, 'checkAvailability']);
        Route::post('/check-phone',         [TokenController::class, 'checkPhone']);
        Route::post('/decode-qr',           [TokenController::class, 'decodeQR']);
    });

    // ── Token Registration (public facing) ──
    Route::prefix('token-registration')->group(function () {
        Route::post('/save',                    [TokenController::class, 'save']);
        Route::post('/generate',                [TokenController::class, 'generateToken']);
        Route::post('/venue-availability',      [TokenController::class, 'getVenueAvailability']);
        Route::post('/validate-working-lady',   [TokenController::class, 'validateWorkingLady']);
    });

    // ── Working Ladies ──
    Route::prefix('working-ladies')->group(function () {
        Route::get('/',             [WorkingLadyController::class, 'index']);
        Route::post('/',            [WorkingLadyController::class, 'store']);
        Route::get('/{id}',         [WorkingLadyController::class, 'edit']);
        Route::put('/{id}',         [WorkingLadyController::class, 'update']);
        Route::delete('/{id}',      [WorkingLadyController::class, 'destroy']);
        Route::patch('/{id}/status', [WorkingLadyController::class, 'updateStatus']);
    });

    // ── Facial Recognition ──
    Route::prefix('facial-recognition')->group(function () {
        Route::get('/users',            [FacialRecognitionController::class, 'users']);
        Route::get('/manual-mappings',  [FacialRecognitionController::class, 'manualMappings']);
    });

    // ── Saved Filters ──
    Route::prefix('saved-filters')->group(function () {
        Route::get('/',                     [SavedFilterController::class, 'index']);
        Route::post('/',                    [SavedFilterController::class, 'store']);
        Route::delete('/{savedFilter}',     [SavedFilterController::class, 'destroy']);
        Route::post('/{savedFilter}/default', [SavedFilterController::class, 'setDefault']);
    });
});
