<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\DTOs\Auth\LoginDTO;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;

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

    public function login(Request $request)
    {

        $dto = LoginDTO::fromLoginRequest($request);

        $result = $this->authService->login($dto);

        if ($result) {
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout(Request $request)
    {
        $this->authService->logout();
        return redirect('/');
    }
}