<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class RoleBasedLoginResponse implements LoginResponseContract
{
    public function toResponse($request): mixed
    {
        $user = Auth::user();

        return match ($user->role) {
            'super_admin', 'admin' => redirect()->route('admin.dashboard'),
            'mekanik' => redirect()->route('mekanik.dashboard'),
            default => redirect()->intended(route('dashboard')),
        };
    }
}
