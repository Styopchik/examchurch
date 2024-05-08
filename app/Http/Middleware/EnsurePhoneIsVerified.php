<?php

namespace App\Http\Middleware;

use App\Facades\UtilityFacades;
use Closure;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class EnsurePhoneIsVerified
{
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        $usr = \Auth::user();
        if ($usr) {
            $created_by = $usr->id;
        } else {
            $created_by = $usr->created_by;
        }
        if (!$request->user() || (!$request->user()->hasVerifiedPhone()) && UtilityFacades::keysettings('sms_verification', $created_by) == 1) {
            return $request->expectsJson()
                ? abort(403, 'Your phone number is not verified.')
                : Redirect::guest(URL::route('smsindex.noticeverification'));
        }
        return $next($request);
    }
}
