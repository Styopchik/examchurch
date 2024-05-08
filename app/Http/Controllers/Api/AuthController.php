<?php

namespace App\Http\Controllers\Auth;

use App\Facades\UtilityFacades;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;
use App\Models\SmsTemplate;
use App\Models\UserCode;

class LoginController extends Controller
{
    use AuthenticatesUsers;
    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except(['logout']);
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user->phone_verified_at == '') {
            $code = rand(100000, 999999);
            UserCode::updateOrCreate(
                ['user_id' => $user->id],
                ['code' => $code]
            );
            $datas =  UserCode::where('user_id', '=', $user->id)->first();
            $data = [];
            $data['code'] = $datas->code;
            $data['name'] = $user->name;
            if ($send_sms = SmsTemplate::where('event', 'verification code sms')->first()) {
                // $result = $send_sms->send("+" . $user->country_code . $user->phone,  $data);
            }
            if (!empty($user)) {
                if ($user->type == 'Super Admin') {
                    $users = User::where('email', $request->email)->orWhere('password', $request->password)->first();
                    $email = $users->email;
                    $password = $users->password;
                    $phone = $users->phone;
                    if ($this->attemptLogin($request)) {
                        if (UtilityFacades::getsettings('TWILIO_SETTING') == 'on') {
                            return redirect()->route('smsindex.noticeverification');
                        } else {
                            return redirect()->back()->with('errors', __('Invalid twilio setting'));
                        }
                    } else {
                        return redirect()->back()->with('errors', __('Invalid username or password'));
                    }
                } elseif (!empty($user->id)) {
                    $users = User::where('email', $request->email)->first();
                    if ($users->active_status == 1) {
                        $users = User::where('email', $request->email)->orWhere('password', $request->password)->first();
                        $email = $users->email;
                        $password = $users->password;
                        $phone = $users->phone;
                        if ($this->attemptLogin($request)) {
                            if (UtilityFacades::getsettings('TWILIO_SETTING') == 'on') {
                                return redirect()->route('smsindex.noticeverification');
                            } else {
                                return redirect()->back()->with('errors', __('Invalid twilio setting'));
                            }
                        } else {
                            return redirect()->back()->with('errors', __('Invalid username or password'));
                        }
                    } else {
                        return redirect()->back()->with('errors', __('Please Contact to administrator'));
                    }
                } else {
                    return redirect()->back()->with('errors', __('permission denied'));
                }
            } else {
                return redirect()->back()->with('errors', __('user not found'));
            }
        } else {
            $user = User::where('email', $request->email)->first();
            if (!empty($user)) {
                if ($user->type == 'Super Admin') {
                    if ($this->attemptLogin($request)) {
                        return $this->sendLoginResponse($request);
                    } else {
                        return redirect()->back()->with('errors', __('Invalid username or password'));
                    }
                } elseif (!empty($user->id)) {
                    $users = User::where('email', $request->email)->first();
                    if ($users->active_status == 1) {
                        if ($this->attemptLogin($request)) {
                            $users->social_type = null;
                            $users->save();
                            return $this->sendLoginResponse($request);
                        } else {
                            return redirect()->back()->with('errors', __('Invalid username or password'));
                        }
                    } else {
                        return redirect()->back()->with('errors', __('Please Contact to administrator'));
                    }
                } else {
                    return redirect()->back()->with('errors', __('permission denied'));
                }
            } else {
                return redirect()->back()->with('errors', __('user not found'));
            }
        }
    }

}
