<?php

namespace App\Http\Controllers;

use App\Facades\UtilityFacades;
use App\Models\LoginSecurity;
use Hash;
use Illuminate\Http\Request;

class LoginSecurityController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function show2faForm()
    {
        $user               = \Auth::user();
        $google2faUrl       = "";
        $secretKey          = "";
        if ($user->loginSecurity()->exists()) {
            $google2fa      = (new \PragmaRX\Google2FAQRCode\Google2FA());
            $google2faUrl   = $google2fa->getQRCodeInline(
                @UtilityFacades::getsettings('app_name'),
                $user->name,
                $user->loginSecurity->google2fa_secret
            );
            $secretKey      = $user->loginSecurity->google2fa_secret;
        }
        $user               = auth()->user();
        $role               = $user->roles->first();
        return view('profile.index', [
            'user' => $user,
            'role' => $role,
            'secret' => $secretKey,
            'google2fa_url' => $google2faUrl,
        ]);
    }

    public function generate2faSecret()
    {
        $user                               = \Auth::user();
        $google2fa                          = (new \PragmaRX\Google2FAQRCode\Google2FA());
        $loginSecurity                      = LoginSecurity::firstOrNew(array('user_id' => $user->id));
        $loginSecurity->user_id             = $user->id;
        $loginSecurity->google2fa_enable    = 0;
        $loginSecurity->google2fa_secret    = $google2fa->generateSecretKey();
        $loginSecurity->save();
        return redirect()->back()->with('success', __('Secret key is generated successfully.'));
    }

    public function enable2fa(Request $request)
    {
        $user                   = \Auth::user();
        $google2fa              = (new \PragmaRX\Google2FAQRCode\Google2FA());
        $secret                 = $request->input('secret');
        $valid                  = $google2fa->verifyKey($user->loginSecurity->google2fa_secret, $secret);
        if ($valid) {
            $user->loginSecurity->google2fa_enable = 1;
            $user->loginSecurity->save();
            return redirect()->back()->with('success', __('2fa is enabled successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Invalid verification code, please try again.'));
        }
    }

    public function disable2fa(Request $request)
    {
        if (!(Hash::check($request->get('current-password'), \Auth::user()->password))) {
            return redirect()->back()->with("failed", __('Your password does not matches with your account password. please try again.'));
        }
        $request->validate([
            'current-password' => 'required',
        ]);
        $user                                   = \Auth::user();
        $user->loginSecurity->google2fa_enable  = 0;
        $user->loginSecurity->save();
        return redirect()->back()->with('success', __('2fa is now disabled.'));
    }
}
