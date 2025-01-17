<?php

namespace App\Http\Controllers\Auth;

use App\Facades\UtilityFacades;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function __invoke(Request $request)
    {
        $lang = UtilityFacades::getActiveLanguage();
        \App::setLocale($lang);
        return $request->user()->hasVerifiedEmail()
            ? redirect()->intended(RouteServiceProvider::HOME)
            : view('auth.verify-email', compact('lang'));
    }
}
