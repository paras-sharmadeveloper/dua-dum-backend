<?php
namespace App\DTOs\Auth;

use Illuminate\Http\Request;

class LoginDTO
{
    public string $email;
    public string $password;

    private function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public static function fromLoginRequest(Request $request): self
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        return new self(
            $validated['email'],
            $validated['password'],
        );
    }
}