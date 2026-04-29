<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\DTOs\Auth\LoginDTO;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }
    // AuthController.php mein add karo

    public function dashboardStats()
    {
        return response()->json([
            'total_tokens'    => \App\Models\Token::count(),
            'pending_tokens'  => \App\Models\Token::where('status', 'pending')->count(),
            'active_venues'   => \App\Models\Venue::where('status', 'active')->count(),
            'working_ladies'  => \App\Models\WorkingLady::count(),
        ]);
    }
    // login
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user  = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ]);
    }

    // logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    // me
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
