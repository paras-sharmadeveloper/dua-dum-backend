<?php
namespace App\Services\Auth;

use App\DTOs\Auth\LoginDTO;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function login(LoginDTO $dto): bool
    {
        return Auth::attempt([
            'email' => $dto->email,
            'password' => $dto->password,
        ]);
    }

    public function logout(): void
    {
        Auth::logout();
    }
}