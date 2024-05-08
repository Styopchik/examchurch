<?php

namespace App\Http\Controllers\Auth;

use App\Facades\UtilityFacades;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $user           = \Auth::user();
        if ($user->type == 'Super Admin') {
            $createdBy  = $user->id;
        } else {
            $createdBy  = $user->created_by;
        }
        config([
            'mail.default'                  => UtilityFacades::keysettings('mail_mailer', $createdBy),
            'mail.mailers.smtp.host'        => UtilityFacades::keysettings('mail_host', $createdBy),
            'mail.mailers.smtp.port'        => UtilityFacades::keysettings('mail_port', $createdBy),
            'mail.mailers.smtp.encryption'  => UtilityFacades::keysettings('mail_encryption', $createdBy),
            'mail.mailers.smtp.username'    => UtilityFacades::keysettings('mail_username', $createdBy),
            'mail.mailers.smtp.password'    => UtilityFacades::keysettings('mail_password', $createdBy),
            'mail.from.address'             => UtilityFacades::keysettings('mail_from_address', $createdBy),
            'mail.from.name'                => UtilityFacades::keysettings('mail_from_name', $createdBy),
        ]);
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }
        try {
            $request->user()->sendEmailVerificationNotification();
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }
        return back()->with('status', __('verification-link-sent'));
    }
}
