<?php

namespace App\Http\Controllers\Auth;

use App\Facades\UtilityFacades;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $lang = UtilityFacades::getActiveLanguage();
        \App::setLocale($lang);
        $roles = Role::whereNotIn('name', ['Super Admin', 'Admin'])->pluck('name', 'name')->all();
        return view('auth.register', compact('roles', 'lang'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(),  [
            'name'                  => ['required', 'string', 'max:191'],
            'email'                 => ['required', 'string', 'email', 'max:191', 'unique:users'],
            'password'              => ['required', 'confirmed', Rules\Password::defaults()],
            'country_code'          => ['required', 'string', 'max:191'],
        ]);
        if ($validator->fails()) {
            $messages = $validator->errors();
            return back()->withInput($request->only('name', 'email'))->with('failed', $messages->first());
        }
        $countries                  = \App\Core\Data::getCountriesList();
        $countryCode                = $countries[$request->country_code]['phone_code'];
        $user = User::create([
            'name'                  => $request->name,
            'email'                 => $request->email,
            'password'              => Hash::make($request->password),
            'country_code'          => $countryCode,
            'phone'                 => $request->phone,
            'phone_verified_at'     => Carbon::now(),
            'email_verified_at'     => Carbon::now(),
            'type'                  => UtilityFacades::getsettings('roles'),
            'lang'                  => 'en',
        ]);
        $user->assignRole(UtilityFacades::getsettings('roles'));

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
