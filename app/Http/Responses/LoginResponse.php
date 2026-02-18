<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        if ($request->user()?->must_change_password) {
            return redirect()->route('password.change');
        }

        return redirect()->intended(config('fortify.home', '/dashboard'));
    }
}
