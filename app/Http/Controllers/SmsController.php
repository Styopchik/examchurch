<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facades\UtilityFacades;
use App\Http\Controllers\Controller;
use App\Models\SmsTemplate;
use App\Models\User;
use App\Models\UserCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class SmsController extends Controller
{
    public function smsNoticeIndex()
    {
        $user = \Auth::user();
        if ($user->type == 'Super Admin') {
            $createdBy = $user->id;
        } else {
            $createdBy = $user->created_by;
        }
        if (UtilityFacades::keysettings('sms_verification', $createdBy) == '1') {
            return view('auth.smsnotice');
        } else {
            return redirect()->route('home');
        }
    }

    public function smsNoticeVerify(Request $request)
    {
        $smsType = 'sms';
        $user = \Auth::user();
        if ($user->type == 'Super Admin') {
            $createdBy = $user->id;
        } else {
            $createdBy = $user->created_by;
        }
        $user = User::where('email', $request->email)->where('phone', $request->phone)->first();
        $code = rand(100000, 999999);
        if (UtilityFacades::keysettings('smssetting', $createdBy) == 'nexmo') {
            $response = Http::asForm()->post('https://rest.nexmo.com/sms/json/', [
                'api_key'       => UtilityFacades::keysettings('nexmo_key', $createdBy),
                'api_secret'    => UtilityFacades::keysettings('nexmo_secret', $createdBy),
                'from'          => env('APP_NAME'),
                'text'          => $code,
                'to'            => $user->country_code . $user->phone,
            ]);
        }
        if (UtilityFacades::keysettings('smssetting', $createdBy) == 'fast2sms') {
            $fast2smsApiKey = UtilityFacades::keysettings('fast2sms_api_key', $createdBy);
            $url            = 'https://www.fast2sms.com/dev/bulkV2';
            if ($request->smstype == 'call') {
                $smsType    = $request->smstype;
                $url        = 'https://www.fast2sms.com/dev/voice';
            }
            $fields = array(
                "variables_values"  => $code,
                "route"             => "otp",
                "numbers"           => $user->phone,
            );
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($fields),
                CURLOPT_HTTPHEADER => array(
                    "authorization: " . $fast2smsApiKey,
                    "accept: */*",
                    "cache-control: no-cache",
                    "content-type: application/json"
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                return redirect()->back()->with('errors', $err);
            }
        }
        if (UtilityFacades::keysettings('smssetting', $createdBy) == 'twilio' || UtilityFacades::keysettings('smssetting', $createdBy) == 'fast2sms' || UtilityFacades::keysettings('smssetting', $createdBy) == 'nexmo' && $response->status() == 200) {
            UserCode::updateOrCreate(
                ['user_id'  => $user->id],
                ['code'     => $code]
            );
            $userCode           =  UserCode::where('user_id', $user->id)->first();
            $codeData           = [];
            $codeData['code']   = $userCode->code;
            $codeData['name']   = $user->name;
        } else {
            return redirect()->back()->with('errors', __('Please check nexmo sms setting.'));
        }
        if (UtilityFacades::keysettings('sms_verification', $createdBy) == '1') {
            if ($sendSms = SmsTemplate::where('event', 'verification code sms')->first()) {
                 $sendSms->send("+" . $user->country_code . $user->phone, $codeData);
            } else {
                return redirect()->back()->with('errors', __('Sms template not found.'));
            }
        } else {
            return redirect()->back()->with('errors', __('Please check sms setting.'));
        }
        return redirect()->route('smsindex.verification', ['smstype' => $smsType])->with('success', __('Sms code send successfully.'));
    }

    public function smsIndex(Request $request)
    {
        $user = \Auth::user();
        if ($user->type == 'Super Admin') {
            $createdBy = $user->id;
        } else {
            $createdBy = $user->created_by;
        }
        $smstype = $request->input('smstype');
        if (UtilityFacades::keysettings('sms_verification', $createdBy) == '1') {
            return view('auth.sms', compact('smstype'));
        } else {
            return redirect()->route('home');
        }
    }

    public function smsVerify(Request $request)
    {
        $user = User::where('email', $request->email)->orWhere('password', $request->password)->first();
        if (!empty($user)) {
            if ($user->type == 'Super Admin') {
                $users = User::where('email', $user->email)->first();
                if (UserCode::where('code', $request->code)->where('user_id', $users->id)->first()) {
                    $users->phone_verified_at = Carbon::now()->toDateTimeString();
                    $users->save();
                    return redirect()->route('home');
                } else {
                    return redirect()->back()->with('errors', __('Sms Code invalid.'));
                }
            } elseif (!empty($user->id)) {
                $users = User::where('email', $user->email)->first();
                if ($users->active_status == 1) {
                    if (UserCode::where('code', $request->code)->where('user_id', $users->id)->first()) {
                        $users->phone_verified_at = Carbon::now()->toDateTimeString();
                        $users->save();
                        return redirect()->route('home');
                    } else {
                        return redirect()->back()->with('errors', __('Sms Code invalid.'));
                    }
                } else {
                    return redirect()->back()->with('errors', __('Please contact to administrator.'));
                }
            } else {
                return redirect()->back()->with('errors', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('errors', __('User not found.'));
        }
    }

    public function smsResend(Request $request)
    {
        $users = auth()->user();
        $user = User::where('email', $users->email)->where('phone', $users->phone)->first();
        if ($user->type == 'Super Admin') {
            $createdBy = $user->id;
        } else {
            $createdBy = $user->created_by;
        }
        $code = rand(100000, 999999);
        if (UtilityFacades::keysettings('smssetting', $createdBy) == 'nexmo') {
            $response = Http::asForm()->post('https://rest.nexmo.com/sms/json/', [
                'api_key'       => UtilityFacades::keysettings('nexmo_key', $createdBy),
                'api_secret'    => UtilityFacades::keysettings('nexmo_secret', $createdBy),
                'from'          => env('APP_NAME'),
                'text'          => $code,
                'to'            => $user->country_code . $user->phone,
            ]);
        }
        if (UtilityFacades::keysettings('smssetting', $createdBy) == 'fast2sms') {
            $fast2smsApiKey = UtilityFacades::keysettings('fast2sms_api_key', $createdBy);
            $url            = 'https://www.fast2sms.com/dev/bulkV2';
            if ($request->smstype == 'call') {
                $url        = 'https://www.fast2sms.com/dev/voice';
            }
            $fields = array(
                "variables_values"  => $code,
                "route"             => "otp",
                "numbers"           => $user->phone,
            );
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($fields),
                CURLOPT_HTTPHEADER => array(
                    "authorization: " . $fast2smsApiKey,
                    "accept: */*",
                    "cache-control: no-cache",
                    "content-type: application/json"
                ),
            ));
            $response   = curl_exec($curl);
            $err        = curl_error($curl);
            curl_close($curl);
            if ($err) {
                return redirect()->back()->with('errors', $err);
            }
        }
        if (UtilityFacades::keysettings('smssetting', $createdBy) == 'twilio' || UtilityFacades::keysettings('smssetting', $createdBy) == 'fast2sms' || UtilityFacades::keysettings('smssetting', $createdBy) == 'nexmo' && $response->status() == 200) {
            UserCode::updateOrCreate(
                ['user_id'  => $user->id],
                ['code'     => $code]
            );
            $userCode           =  UserCode::where('user_id', '=', $user->id)->first();
            $codeData           = [];
            $codeData['code']   = $userCode->code;
            $codeData['name']   = $user->name;
        } else {
            return redirect()->back()->with('errors', __('Please check nexmo sms setting.'));
        }
        if (UtilityFacades::keysettings('sms_verification', $createdBy) == '1') {
            if ($sendSms = SmsTemplate::where('event', 'verification code sms')->first()) {
                 $sendSms->send("+" . $user->country_code . $user->phone, $codeData);
            } else {
                return redirect()->back()->with('errors', __('Please check sms setting.'));
            }
        } else {
            return redirect()->back()->with('errors', __('Please check sms setting.'));
        }
        return redirect()->back()
            ->with('success', __('We have resent OTP on your mobile number.'));
    }
}
