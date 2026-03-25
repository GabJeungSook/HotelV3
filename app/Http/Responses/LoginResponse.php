<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        // Flash = available for the next request only
        session()->flash('from_login', true);

        return redirect()->intended(config('fortify.home'));
    }

    public function goToCashOnHand()
    {
        session()->flash('from_dashboard', true);

        return redirect()->route('frontdesk.cash-on-hand');
    }
}
