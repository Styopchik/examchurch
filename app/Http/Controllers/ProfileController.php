<?php

namespace App\Http\Controllers;

use App\Facades\UtilityFacades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Hash;

class ProfileController extends Controller
{
    public $user;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = \Auth::user();
            return $next($request);
        });
    }

    public function index()
    {
        if (!UtilityFacades::getsettings('2fa')) {
            $user       = Auth::user();
            $role       = $user->roles->first();
            return view('profile.index', [
                'user'      => $user,
                'role'      => $role,
            ]);
        }
        return $this->activeTwoFactor();
    }

    public function profileStatus()
    {
        $user = \Auth::user();
        if ($user->id != 1) {
            $user->active_status = 0;
            $user->save();
            auth()->logout();
            return redirect()->route('home');
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    private function activeTwoFactor()
    {
        $user               = \Auth::user();
        $google2faUrl       = "";
        $secretKey          = "";
        if ($user->loginSecurity()->exists()) {
            $google2fa = (new \PragmaRX\Google2FAQRCode\Google2FA());
            $google2faUrl = $google2fa->getQRCodeInline(
                @UtilityFacades::getsettings('app_name'),
                $user->name,
                $user->loginSecurity->google2fa_secret
            );
            $secretKey = $user->loginSecurity->google2fa_secret;
        }
        $user               = auth()->user();
        $role               = $user->roles->first();
        return view('profile.index', [
            'user'          => $user,
            'role'          => $role,
            'secret'        => $secretKey,
            'google2fa_url' => $google2faUrl,
        ]);
    }

    public function update(Request $request)
    {
        $user                   = \Auth::user();
        request()->validate([
            'name'              => 'required|regex:/^[A-Za-z0-9_.,() ]+$/|string|max:191',
            'address'           => 'nullable|regex:/^[A-Za-z0-9_.,() ]+$/|string|max:191',
            'country'           => 'nullable|string|max:191',
            'phone'             => 'nullable|string|max:191',
        ], [
            'name.regex'        =>  __('Invalid entry! the name only letter and numbers are allowed.'),
            'address.regex'     =>  __('Invalid entry! the address only letter and numbers are allowed.'),
        ]);
        $countries              = \App\Core\Data::getCountriesList();
        $country                = $countries[$request->country]['name'];
        $user->name             = $request->name;
        $user->address          = $request->address;
        $user->country          = $country;
        $user->phone            = $request->phone;
        $user->save();
        return redirect()->back()->with('success',  __('Account details updated successfully.'));
    }

    public function updateAvatar(Request $request)
    {
        $disk           = Storage::disk();
        $user           = \Auth::user();
        request()->validate([
            'avatar'    => 'required',
        ]);
        $image          = $request->avatar;
        $image          = str_replace('data:image/png;base64,', '', $image);
        $image          = str_replace(' ', '+', $image);
        $imagename      = time() . '.' . 'png';
        $imagepath      = "avatar/" . $imagename;
        $disk->put($imagepath, base64_decode($image));
        $user->avatar   = $imagepath;
        if ($user->save()) {
            return __("Avatar Updated Successfully.");
        }
        return __("Avatar updated failed.");
    }

    public function updateLogin(Request $request)
    {
        $user                       = \Auth::user();
        request()->validate([
            'email'                 => 'required|string|email|max:191|unique:users,email,' . $user->id,
            'password'              => 'nullable|string|min:5|same:password_confirmation',
        ]);
        $user->email                = $request->email;
        if (!empty($request->password)) {
            $user->password         = Hash::make($request->password);
        }
        $user->save();
        return redirect()->back()->with('success',  __('Login details updated successfully.'));
    }
}
