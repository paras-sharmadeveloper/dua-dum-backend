<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PermissionController;
use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\ProfileController;

//Auth Routes
Route::get('/', [AuthController::class, 'showLoginForm'])->name('auth.login.page');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//Role Routes
Route::post('/role/get-roles', [RoleController::class, 'getRolesData'])->name('role.get-roles');
Route::get('/role/get-permissions', [RoleController::class, 'getAllPermissions'])->name('role.get-permissions');
Route::get('/role/{id}/get-role', [RoleController::class, 'getRole'])->name('role.get-role');
Route::resource('role', controller: RoleController::class);


// Permissions Routes
Route::prefix('permission')->group(function () {
    Route::get('/', [PermissionController::class, 'index'])->name('permission.index');
    Route::post('/get-permissions', [PermissionController::class, 'getPermissionsData'])->name('permission.get-permissions');
    Route::post('/create-permission', [PermissionController::class, 'createPermission'])->name('permission.create-permission');
    Route::get('/{id}/get-permission', [PermissionController::class, 'getPermissionForEdit'])->name('permission.get-permission');
    Route::put('/{id}/update-permission', [PermissionController::class, 'updatePermission'])->name('permission.update-permission');
});
//Route::resource('permission', PermissionController::class);

// User Routes
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::post('/users/data', [UserController::class, 'getUsersData'])->name('users.data');
Route::get('/users/roles', [UserController::class, 'getAllRoles'])->name('users.roles');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::get('/users/{id}', [UserController::class, 'getUser'])->name('users.get');
Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});




// Route::middleware('guest')->group(function () {
//     Route::get('register', [RegisteredUserController::class, 'create'])
//         ->name('register');

//     Route::post('register', [RegisteredUserController::class, 'store']);

//     Route::get('loginn', [AuthenticatedSessionController::class, 'create'])
//         ->name('login');

//     Route::post('loginn', [AuthenticatedSessionController::class, 'store']);

//     Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
//         ->name('password.request');

//     Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
//         ->name('password.email');

//     Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
//         ->name('password.reset');

//     Route::post('reset-password', [NewPasswordController::class, 'store'])
//         ->name('password.store');
// });

// Route::middleware('auth')->group(function () {
//     Route::get('verify-email', EmailVerificationPromptController::class)
//         ->name('verification.notice');

//     Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
//         ->middleware(['signed', 'throttle:6,1'])
//         ->name('verification.verify');

//     Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
//         ->middleware('throttle:6,1')
//         ->name('verification.send');

//     Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
//         ->name('password.confirm');

//     Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

//     Route::put('password', [PasswordController::class, 'update'])->name('password.update');

//     Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
//         ->name('logout');
// });