<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        // Flash = available for the next request only
        session()->flash('from_login', true);

        // Always redirect to /dashboard for role-based routing.
        // Never use intended() — it would send users to the previous
        // session's last URL, which may belong to a different role.
        session()->forget('url.intended');

        return redirect('/dashboard');
    }

    public function goToCashOnHand()
    {
        session()->flash('from_dashboard', true);

        return redirect()->route('frontdesk.cash-on-hand');
    }
}
