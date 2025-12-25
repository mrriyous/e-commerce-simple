<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        // Get the intended URL from session
        $intended = $request->session()->get('intended');
        
        // Clear the intended URL from session
        if ($intended) {
            $request->session()->forget('intended');
            return redirect($intended);
        }
        
        // Fallback to home page
        return redirect()->intended(config('fortify.home', '/'));
    }
}

