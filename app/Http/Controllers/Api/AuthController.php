<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Token;
use App\Models\Venue;
use App\Models\WorkingLady;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email'    => 'required|email',
                'password' => 'required',
            ]);

            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            $user  = Auth::user();
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'user'  => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'role'  => $user->roles->first()?->name ?? 'admin',
                ],
                'token' => $token,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Login failed', 'error' => $th->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load('roles');
        return response()->json([
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->roles->first()?->name ?? 'admin',
        ]);
    }

    public function dashboardStats()
    {
        return response()->json([
            'total_tokens'    => Token::count(),
            'pending_approval' => Token::where('status', 'Pending')->count(),
            'active_venues'   => Venue::where('status', 'Active')->count(),
            'working_ladies'  => WorkingLady::count(),
        ]);
    }
}
