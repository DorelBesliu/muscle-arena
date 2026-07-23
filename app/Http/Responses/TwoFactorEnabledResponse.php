<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContract;
use Laravel\Fortify\Fortify;

class TwoFactorEnabledResponse implements TwoFactorLoginResponseContract
{
    public function toResponse($request)
    {
        $request->session()->flash('open_2fa_setup_modal', true);

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', Fortify::TWO_FACTOR_AUTHENTICATION_ENABLED);
    }
}
