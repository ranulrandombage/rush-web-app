<?php

namespace App\Service;

use Illuminate\Support\Facades\Auth;

class AuthService
{
    /**
     * Check if user is logged in
     * @return bool
     */
    public function CheckIfLoggedIn(): bool
    {
        return Auth::check();
    }

    /**
     * Authenticate user attempt
     * @param array $credentials
     * @param mixed $remember
     * @return bool
     */
    public function Authenticate(array $credentials,mixed $remember): bool
    {
        return Auth::attempt($credentials, $remember);
    }

    /**
     * Logout and delete Auth object
     * @return void
     */
    public function logout(): void
    {
        Auth::logout();
    }
}
