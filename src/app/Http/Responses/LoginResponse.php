<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        \Log::info('🔥 LoginResponse called');
        \Log::info('🔥 session just_registered: '.json_encode(session('just_registered')));

        $redirectTo = session()->pull('just_registered') ? '/mypage/profile' : '/';

        return redirect()->intended($redirectTo);
    }
}
