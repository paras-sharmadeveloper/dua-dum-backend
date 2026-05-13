<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VenueController;
use App\Http\Controllers\Api\TokenController;
use App\Http\Controllers\Api\WorkingLadyController;
use App\Http\Controllers\Api\SavedFilterController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\ReasonController;

// ─── PUBLIC ──────────────────────────────────────────────
Route::post('/login', [AuthController::class, 'login']);
Route::get('/settings/public', [SettingsController::class, 'publicSettings']);

// Public token view by UUID
Route::get('/tokens/{id}/public', [TokenController::class, 'showPublic']);

// Public reasons list (used on booking form)
Route::get('/reasons/public', [ReasonController::class, 'publicIndex']);

// Public token booking (no auth required)
Route::prefix('booking')->group(function () {
    Route::get('/venues',               [TokenController::class, 'getVenues']);
    Route::post('/venue-availability',  [TokenController::class, 'venueAvailability']);
    Route::post('/check-phone',         [TokenController::class, 'checkPhone']);
    Route::post('/validate-working-lady', [TokenController::class, 'validateWorkingLady']);
    Route::post('/generate',            [TokenController::class, 'generateToken']);
});

// ─── PROTECTED (Sanctum) ─────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth (no extra permission needed)
    Route::post('/logout',          [AuthController::class, 'logout']);
    Route::get('/me',               [AuthController::class, 'me']);
    Route::get('/dashboard/stats',  [AuthController::class, 'dashboardStats']);

    // Settings
    Route::patch('/settings', [SettingsController::class, 'update']);

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
    // Saved Filters (own data — no permission gate needed)
    Route::prefix('saved-filters')->group(function () {
        Route::get('/',                         [SavedFilterController::class, 'index']);
        Route::post('/',                        [SavedFilterController::class, 'store']);
        Route::post('/{savedFilter}/default',   [SavedFilterController::class, 'setDefault']);
        Route::delete('/{savedFilter}',         [SavedFilterController::class, 'destroy']);
    });

    // ── Venues ──────────────────────────────────────────
    Route::prefix('venues')->group(function () {
        Route::middleware('perm:venue-list')->group(function () {
            Route::get('/',          [VenueController::class, 'index']);
            Route::get('/form-data', [VenueController::class, 'formData']);
            Route::get('/{id}',      [VenueController::class, 'show']);
        });
        Route::post('/',                [VenueController::class, 'store'])->middleware('perm:venue-create');
        Route::put('/{id}',             [VenueController::class, 'update'])->middleware('perm:venue-edit');
        Route::patch('/{id}/status',    [VenueController::class, 'updateStatus'])->middleware('perm:venue-edit');
    });

    // ── Tokens ──────────────────────────────────────────
    Route::prefix('tokens')->group(function () {
        Route::middleware('perm:token-list')->group(function () {
            Route::get('/',     [TokenController::class, 'index']);
            Route::post('/search', [TokenController::class, 'search']);
        });
        Route::patch('/{id}/approve', [TokenController::class, 'approve'])->middleware('perm:token-edit');
        Route::patch('/{id}/reject',  [TokenController::class, 'reject'])->middleware('perm:token-edit');
        Route::post('/{id}/print',    [TokenController::class, 'updatePrintCount'])->middleware('perm:token-print');
        // These are also used by admin-side token creation forms
        Route::get('/venues',               [TokenController::class, 'getVenues'])->middleware('perm:token-create');
        Route::post('/check-phone',         [TokenController::class, 'checkPhone'])->middleware('perm:token-create');
        Route::post('/generate',            [TokenController::class, 'generateToken'])->middleware('perm:token-create');
        Route::post('/venue-availability',  [TokenController::class, 'venueAvailability'])->middleware('perm:token-create');
        Route::post('/validate-working-lady', [TokenController::class, 'validateWorkingLady'])->middleware('perm:token-create');
    });

    // ── Working Ladies ────────────────────────────────
    Route::prefix('working-ladies')->group(function () {
        Route::middleware('perm:working-lady-list')->group(function () {
            Route::get('/',       [WorkingLadyController::class, 'index']);
            Route::get('/{id}',   [WorkingLadyController::class, 'show']);
        });
        Route::post('/',                [WorkingLadyController::class, 'store'])->middleware('perm:working-lady-create');
        Route::post('/{id}/image',      [WorkingLadyController::class, 'uploadImage'])->middleware('perm:working-lady-edit');
        Route::put('/{id}',             [WorkingLadyController::class, 'update'])->middleware('perm:working-lady-edit');
        Route::delete('/{id}',          [WorkingLadyController::class, 'destroy'])->middleware('perm:working-lady-delete');
        Route::patch('/{id}/status',    [WorkingLadyController::class, 'updateStatus'])->middleware('perm:working-lady-edit');
        Route::get('/{id}/scan',        [WorkingLadyController::class, 'scan'])->middleware('perm:site-admin');
    });

    // ── Users ─────────────────────────────────────────
    Route::prefix('users')->group(function () {
        // Static routes must come before /{id} to avoid being swallowed by the wildcard
        Route::get('/roles', [UserController::class, 'getAllRoles']);
        Route::middleware('perm:user-list')->group(function () {
            Route::get('/',       [UserController::class, 'index']);
            Route::get('/{id}',   [UserController::class, 'show']);
        });
        Route::post('/',                        [UserController::class, 'store'])->middleware('perm:user-create');
        Route::put('/{id}',                     [UserController::class, 'update'])->middleware('perm:user-edit');
        Route::delete('/{id}',                  [UserController::class, 'destroy'])->middleware('perm:user-delete');
        Route::patch('/{id}/toggle-status',     [UserController::class, 'toggleStatus'])->middleware('perm:user-edit');
        Route::patch('/{id}/reset-password',    [UserController::class, 'resetPassword'])->middleware('perm:user-edit');
    });

    // ── Locations ─────────────────────────────────────
    Route::prefix('locations')->group(function () {
        // Static routes before /{id} to avoid wildcard collision
        Route::get('/countries',    [LocationController::class, 'getCountries']);
        Route::post('/cities',      [LocationController::class, 'getCitiesByCountry']);
        Route::middleware('perm:location-list')->group(function () {
            Route::get('/',       [LocationController::class, 'index']);
            Route::get('/{id}',   [LocationController::class, 'show']);
        });
        Route::post('/',            [LocationController::class, 'store'])->middleware('perm:location-create');
        Route::put('/{id}',         [LocationController::class, 'update'])->middleware('perm:location-edit');
        Route::delete('/{id}',      [LocationController::class, 'destroy'])->middleware('perm:location-delete');
    });

    // ── Not-Happen Reasons ────────────────────────────
    Route::prefix('reasons')->group(function () {
        Route::get('/',        [ReasonController::class, 'index'])->middleware('perm:reason-list');
        Route::post('/',       [ReasonController::class, 'store'])->middleware('perm:reason-create');
        Route::get('/{id}',    [ReasonController::class, 'show'])->middleware('perm:reason-list');
        Route::put('/{id}',    [ReasonController::class, 'update'])->middleware('perm:reason-edit');
        Route::delete('/{id}', [ReasonController::class, 'destroy'])->middleware('perm:reason-delete');
    });

    // ── Roles & Permissions ───────────────────────────
    Route::prefix('roles')->group(function () {
        // permissions list needed in role create/edit form
        Route::get('/permissions', [RoleController::class, 'getAllPermissions'])->middleware('perm:role-create');
        Route::middleware('perm:role-list')->group(function () {
            Route::get('/',       [RoleController::class, 'index']);
            Route::get('/{id}',   [RoleController::class, 'getRole']);
        });
        Route::post('/',      [RoleController::class, 'store'])->middleware('perm:role-create');
        Route::put('/{id}',   [RoleController::class, 'update'])->middleware('perm:role-edit');
        Route::delete('/{id}', [RoleController::class, 'destroy'])->middleware('perm:role-delete');
    });
});
