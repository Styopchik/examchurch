<?php

namespace App\Http\Controllers;

use App\Facades\UtilityFacades;
use App\Mail\TestMail;
use App\Models\NotificationsSetting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Spatie\MailTemplates\Models\MailTemplate;

use function PHPUnit\Framework\fileExists;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'web',  'permission:manage-setting']);
    }

    public function index()
    {
        if (\Auth::user()->can('manage-setting')) {
            $notificationsSettings = NotificationsSetting::all();
            return view('settings.index', compact('notificationsSettings'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function appNameUpdate(Request $request)
    {
        request()->validate([
            'app_name'          => 'required|string|max:191|min:4',
            'app_logo'          => 'image|mimes:png|max:2048',
            'favicon_logo'      => 'image|mimes:png|max:2048',
            'app_dark_logo'     => 'image|mimes:png|max:2048',
        ], [
            'app_name.regex'    =>  __('Invalid entry! the app name only letters and numbers are allowed.'),
        ]);
        $appLogo                = UtilityFacades::getsettings('app_logo');
        $appDarkLogo            = UtilityFacades::getsettings('app_dark_logo');
        $faviconLogo            = UtilityFacades::getsettings('favicon_logo');
        $appSettingData = [
            'app_name'          => $request->app_name,
        ];
        if ($request->hasFile('app_logo')) {
            $appLogo    = 'app-logo' . '.' . 'png';
            $logoPath   = "app-logo";
            $image      = request()->file('app_logo')->storeAs(
                $logoPath,
                $appLogo,
            );
            $appSettingData['app_logo']         = $image;
        }
        if ($request->hasFile('app_dark_logo')) {
            $appDarkLogo = 'app-dark-logo' . '.' . 'png';
            $logoPath    = "app-logo";
            $image       = request()->file('app_dark_logo')->storeAs(
                $logoPath,
                $appDarkLogo,
            );
            $appSettingData['app_dark_logo']    = $image;
        }
        if ($request->hasFile('favicon_logo')) {
            $faviconLogo = 'app-favicon-logo' . '.' . 'png';
            $logoPath    = "app-logo";
            $image       = request()->file('favicon_logo')->storeAs(
                $logoPath,
                $faviconLogo,
            );
            $appSettingData['favicon_logo']     = $image;
        }
        $arrEnv = [
            'APP_NAME'          => $request->app_name,
        ];
        UtilityFacades::setEnvironmentValue($arrEnv);
        Self::updateSettings($appSettingData);
        return redirect()->back()->with('success',  __('App setting updated successfully.'));
    }

    public function pusherSettingUpdate(Request $request)
    {
        request()->validate([
            'pusher_id'             => 'required|string|max:191|regex:/^[0-9]+$/',
            'pusher_key'            => 'required|string|max:191|regex:/^[A-Za-z0-9_.,()]+$/',
            'pusher_secret'         => 'required|string|max:191|regex:/^[A-Za-z0-9_.,()]+$/',
            'pusher_cluster'        => 'required|string|max:191|regex:/^[A-Za-z0-9_.,()]+$/',
        ]);
        $pusherSettingData = [
            'pusher_id'             => $request->pusher_id,
            'pusher_key'            => $request->pusher_key,
            'pusher_secret'         => $request->pusher_secret,
            'pusher_cluster'        => $request->pusher_cluster,
            'pusher_status'         => ($request->pusher_status == 'on') ? 1 : 0,
        ];
        $arrEnv = [
            'PUSHER_APP_ID'         => $request->pusher_id,
            'PUSHER_APP_KEY'        => $request->pusher_key,
            'PUSHER_APP_SECRET'     => $request->pusher_secret,
            'PUSHER_APP_CLUSTER'    => $request->pusher_cluster,
        ];
        UtilityFacades::setEnvironmentValue($arrEnv);
        Self::updateSettings($pusherSettingData);
        return redirect()->back()->with('success',  __('Pusher API keys updated successfully.'));
    }

    public function wasabiSettingUpdate(Request $request)
    {
        request()->validate([
            'storage_type'  => 'required|string|max:191',
        ]);
        if ($request->storage_type == 's3') {
            request()->validate([
                's3_key'    => 'required|string|max:191',
                's3_secret' => 'required|string|max:191',
                's3_region' => 'required|string|max:191',
                's3_bucket' => 'required|string|max:191',
                's3_url'        => 'required|string|max:191',
                's3_endpoint'   => 'required|string|max:191',
            ]);

            $s3 = [
                's3_key'    => $request->s3_key,
                's3_secret' => $request->s3_secret,
                's3_region' => $request->s3_region,
                's3_bucket' => $request->s3_bucket,
                's3_url'        => $request->s3_url,
                's3_endpoint'   => $request->s3_endpoint,
            ];
            Self::updateSettings($s3);
            $general = [
                'storage_type'      => $request->storage_type
            ];
            Self::updateSettings($general);
            return redirect()->back()->with('success',  __('s3 API keys updated successfully.'));
        }

        if ($request->storage_type == 'wasabi') {
            request()->validate([
                'wasabi_key'    => 'required|string|max:191',
                'wasabi_secret' => 'required|string|max:191',
                'wasabi_region' => 'required|string|max:191',
                'wasabi_bucket' => 'required|string|max:191',
                'wasabi_url'    => 'required|string|max:191',
                'wasabi_root'   => 'required|string|max:191',
            ]);

            $wasabi = [
                'wasabi_key'        => $request->wasabi_key,
                'wasabi_secret'     => $request->wasabi_secret,
                'wasabi_region'     => $request->wasabi_region,
                'wasabi_bucket'     => $request->wasabi_bucket,
                'wasabi_url'        => $request->wasabi_url,
                'wasabi_root'       => $request->wasabi_root,
                'filesystem_driver' => $request->storage_type,
            ];
            Self::updateSettings($wasabi);
            $general = [
                'storage_type'      => $request->storage_type
            ];
            Self::updateSettings($general);
            return redirect()->back()->with('success',  __('Wasabi keys updated successfully.'));
        } else {
            $general = [
                'storage_type'      => $request->storage_type
            ];
            Self::updateSettings($general);
            return redirect()->back()->with('success', __('Storage setting updated successfully'));
        }
    }

    public function emailSettingUpdate(Request $request)
    {
        if ($request->email_setting_enable && $request->email_setting_enable == 'on') {
            request()->validate([
                'mail_mailer'           => 'required|string|max:191',
                'mail_host'             => 'required|string|max:191',
                'mail_port'             => 'required|string|max:191',
                'mail_username'         => 'required|email',
                'mail_password'         => 'required|string|max:191',
                'mail_encryption'       => 'required|string|max:191',
                'mail_from_address'     => 'required|string|max:191',
                'mail_from_name'        => 'required|string|max:191',
            ]);
            $emailSettingData = [
                'email_setting_enable'  => ($request->email_setting_enable) ? 'on' : 'off',
                'mail_mailer'           => $request->mail_mailer,
                'mail_host'             => $request->mail_host,
                'mail_port'             => $request->mail_port,
                'mail_username'         => $request->mail_username,
                'mail_password'         => $request->mail_password,
                'mail_encryption'       => $request->mail_encryption,
                'mail_from_address'     => $request->mail_from_address,
                'mail_from_name'        => $request->mail_from_name,
            ];
        } else {
            $emailSettingData = [
                'email_setting_enable'  => 'off',
            ];
        }
        Self::updateSettings($emailSettingData);
        return redirect()->back()->with('success',  __('Email setting updated successfully.'));
    }

    public function captchaSettingUpdate(Request $request)
    {
        request()->validate([
            'captcha' => 'required|min:1'
        ]);
        if ($request->captcha == 'hcaptcha') {
            request()->validate([
                'hcaptcha_key'      => 'required|string|max:191',
                'hcaptcha_secret'   => 'required|string|max:191',
            ]);
        }
        if ($request->captcha == 'recaptcha') {
            request()->validate([
                'recaptcha_key'     => 'required|string|max:191',
                'recaptcha_secret'  => 'required|string|max:191',
            ]);
        }
        $capchaSettingData = [
            'captcha_enable'        => ($request->captcha_enable && $request->captcha_enable == 'on') ? 'on' : 'off',
            'captcha'               => $request->captcha,
            'captcha_secret'        => $request->recaptcha_secret,
            'captcha_sitekey'       => $request->recaptcha_key,
            'hcaptcha_secret'       => $request->hcaptcha_secret,
            'hcaptcha_sitekey'      => $request->hcaptcha_key,
        ];
        Self::updateSettings($capchaSettingData);
        return redirect()->back()->with('success',  __('Captcha settings updated successfully.'));
    }

    public function authSettingsUpdate(Request $request)
    {
        $user = \Auth::user();
        if ($user->type == 'Super Admin') {
            if ($request->sms_verification == 'on') {
                if (UtilityFacades::getsettings('multisms_setting') == 'on') {
                    $val = [
                        'sms_verification' => ($request->sms_verification == 'on') ? '1' : '0',
                    ];
                    Self::updateSettings($val);
                } else {
                    return redirect("/settings#sms_setting")->with('warning', __('Please set sms setting.'));
                }
            }
            if ($request->email_verification == 'on') {
                if (UtilityFacades::getsettings('mail_host') != '') {
                    $val = [
                        'email_verification' => ($request->email_verification == 'on') ? '1' : '0',
                    ];
                    Self::updateSettings($val);
                } else {
                    return redirect("/settings#useradd-6")->with('warning', __('Please set email setting.'));
                }
            }
            $generalSettingData = [
                '2fa'                       => ($request->two_factor_auth == 'on') ? '1' : '0',
                'landing_page_status'       => ($request->landing_page_status && $request->landing_page_status == 'on') ? 1 : 0,
                'gtag'                      => $request->gtag,
                'default_language'          => $request->default_language,
                'date_format'               => $request->date_format,
                'time_format'               => $request->time_format,
                'approve_type'              => $request->approve_type,
                'sms_verification'          => ($request->sms_verification == 'on') ? 1 : 0,
                'email_verification'        => ($request->email_verification == 'on') ? 1 : 0,
                'rtl'                       => ($request->rtl_setting == 'on') ? '1' : '0',
                'color'                     => ($request->color) ? $request->color : UtilityFacades::getsettings('color'),
                'dark_mode'                 => ($request->dark_mode == 'on') ? 'on' : 'off',
                'transparent_layout'        => ($request->transparent_layout == 'on') ? 'on' : 'off',
                'roles'                     => $request->roles
            ];
        } else {
            if ($request->email_verification == 'on') {
                if (UtilityFacades::getsettings('mail_host') != '') {
                    $val = [
                        'email_verification' => ($request->email_verification == 'on') ? '1' : '0',
                    ];
                    Self::updateSettings($val);
                } else {
                    return redirect("/settings#useradd-6")->with('warning', __('Please set email setting.'));
                }
            }
            if ($request->sms_verification == 'on') {
                if (UtilityFacades::getsettings('multisms_setting') == 'on') {
                    $val = [
                        'sms_verification'  => ($request->sms_verification == 'on') ? '1' : '0',
                    ];
                    Self::updateSettings($val);
                } else {
                    return redirect("/settings#sms_setting")->with('warning', __('Please set sms setting.'));
                }
            }
            $generalSettingData = [
                '2fa'                   => ($request->two_factor_auth == 'on') ? '1' : '0',
                'gtag'                  => $request->gtag,
                'default_language'      => $request->default_language,
                'date_format'           => $request->date_format,
                'time_format'           => $request->time_format,
                'sms_verification'      => ($request->sms_verification == 'on') ? 1 : 0,
                'email_verification'    => ($request->email_verification == 'on') ? 1 : 0,
                'rtl'                   => ($request->rtl_setting == 'on') ? '1' : '0',
                'color'                 => ($request->color) ? $request->color : UtilityFacades::getsettings('color'),
                'dark_mode'             => ($request->dark_mode == 'on') ? 'on' : 'off',
                'transparent_layout'    => ($request->transparent_layout == 'on') ? 'on' : 'off',
                'landing_page_status'   => ($request->landing_page_status && $request->landing_page_status == 'on') ? 1 : 0,
            ];
        }
        Self::updateSettings($generalSettingData);
        $user->dark_layout          = ($request->dark_mode && $request->dark_mode == 'on') ? 1 : 0;
        $user->rtl_layout           = ($request->rtl_setting && $request->rtl_setting == 'on') ? 1 : 0;
        $user->transprent_layout    = ($request->transparent_layout && $request->transparent_layout == 'on') ? 1 : 0;
        $user->theme_color          = ($request->color) ? $request->color : UtilityFacades::getsettings('color');
        $user->save();
        return redirect()->back()->with('success',  __('General settings updated successfully.'));
    }

    public function paymentSettingUpdate(Request $request)
    {
        if ($request->get('paymentsetting')) {
            if (in_array('stripe', $request->get('paymentsetting'))) {
                request()->validate([
                    'stripe_key'    => 'required|string',
                    'stripe_secret' => 'required|string'
                ]);
            }
            if (in_array('razorpay', $request->paymentsetting)) {
                request()->validate([
                    'razorpay_key'      => 'required|string',
                    'razorpay_secret'   => 'required|string'
                ]);
            }
            if (in_array('paypal', $request->paymentsetting)) {
                request()->validate([
                    'paypal_mode'       => 'required|string',
                    'client_id'         => 'required|string',
                    'client_secret'     => 'required|string',
                ]);
            }
            if (in_array('paytm', $request->get('paymentsetting'))) {
                request()->validate([
                    'paytm_environment' => 'required|string',
                    'merchant_id'       => 'required|string',
                    'merchant_key'      => 'required|string',
                ]);
            }
            if (in_array('flutterwave', $request->get('paymentsetting'))) {
                request()->validate([
                    'flw_public_key'            => 'required|string',
                    'flw_secret_key'            => 'required|string',
                ]);
            }
            if (in_array('paystack', $request->get('paymentsetting'))) {
                request()->validate([
                    'paystack_public_key'       => 'required|string',
                    'paystack_secret_key'       => 'required|string',
                ]);
            }
            if (in_array('coingate', $request->get('paymentsetting'))) {
                request()->validate([
                    'coingate_mode'         => 'required|string',
                    'coingate_auth_token'   => 'required|string',
                ]);
            }
            if (in_array('mercado', $request->paymentsetting)) {
                request()->validate([
                    'mercado_access_token' => 'required|string',
                ]);
            }
            if (in_array('cashfree', $request->paymentsetting)) {
                request()->validate([
                    'cashfree_app_id'       => 'required|string',
                    'cashfree_secret_key'   => 'required|string',
                    'cashfree_url'          => 'required|string',
                ]);
            }
            if (in_array('sspay', $request->paymentsetting)) {
                request()->validate([
                    'sspay_category_code'       => 'required|string',
                    'sspay_secret_key'          => 'required|string',
                ]);
            }
            if (in_array('payumoney', $request->paymentsetting)) {
                request()->validate([
                    'payumoney_mode'            => 'required|string',
                    'payumoney_merchant_key'    => 'required|string',
                    'payumoney_salt_key'        => 'required|string',
                ]);
            }
            if (in_array('paytab', $request->paymentsetting)) {
                request()->validate([
                    'paytab_profile_id'     => 'required|string',
                    'paytab_server_key'     => 'required|string',
                    'paytab_region'         => 'required|string',
                ]);
            }
            $bkashJson = '';
            if (in_array('bkash', $request->paymentsetting)) {
                request()->validate([
                    'bkash_json_file'    => 'mimes:json',
                    'bkash_currency'     => 'required|string',
                ]);

                if ($request->hasFile('bkash_json_file')) {
                    $bkashJsonFile      = 'config' . '.' . 'json';
                    $logoPath           = "bKash-payment";
                    $bkashJson          = request()->file('bkash_json_file')->storeAs(
                        $logoPath,
                        $bkashJsonFile,
                    );
                }
            }
            if (in_array('offline', $request->paymentsetting)) {
                request()->validate([
                    'payment_details' => 'required|string',
                ]);
            }

            if (in_array('offlinepayment', $request->paymentsetting)) {
                request()->validate([
                    'offline_payment_details'   => 'required'
                ]);
            }
        }
        $paymentDescription = [
            'razorpay_description'              => $request->razorpay_description,
            'paypal_description'                => $request->paypal_description,
            'stripe_description'                => $request->stripe_description,
            'flutterwave_description'           => $request->flutterwave_description,
            'paytm_description'                 => $request->paytm_description,
            'paystack_description'              => $request->paystack_description,
            'cashfree_description'              => $request->cashfree_description,
            'coingate_description'              => $request->coingate_description,
            'sspay_description'                 => $request->sspay_description,
            'payumoney_description'             => $request->payumoney_description,
            'paytab_description'                => $request->paytab_description,
            'bkash_description'                 => $request->bkash_description,
            'offline_description'               => $request->offline_description,
        ];
        $paymentData = [
            'offline_payment_details'           => $request->offline_payment_details,
            'stripe_key'                        => $request->stripe_key,
            'stripe_secret'                     => $request->stripe_secret,
            'paypal_sandbox_client_id'          => $request->client_id,
            'paypal_sandbox_client_secret'      => $request->client_secret,
            'paypal_mode'                       => $request->paypal_mode,
            'razorpay_key'                      => $request->razorpay_key,
            'razorpay_secret'                   => $request->razorpay_secret,
            'paytm_merchant_id'                 => $request->merchant_id,
            'paytm_merchant_key'                => $request->merchant_key,
            'paytm_environment'                 => $request->paytm_environment,
            'paytm_merchant_website'            => 'local',
            'paytm_channel'                     => 'WEB',
            'paytm_indistry_type'               => 'local',
            'paytm_currency'                    => $request->paytm_currency,
            'flw_public_key'                    => $request->flw_public_key,
            'flw_secret_key'                    => $request->flw_secret_key,
            'paystack_public_key'               => $request->paystack_public_key,
            'paystack_secret_key'               => $request->paystack_secret_key,
            'paystack_currency'                 => $request->paystack_currency,
            'cashfree_app_id'                   => $request->cashfree_app_id,
            'cashfree_secret_key'               => $request->cashfree_secret_key,
            'cashfree_url'                      => $request->cashfree_url,
            'coingate_environment'              => $request->coingate_mode,
            'coingate_auth_token'               => $request->coingate_auth_token,
            'payment_details'                   => $request->payment_details,
            'mercado_mode'                      => $request->mercado_mode,
            'mercado_access_token'              => $request->mercado_access_token,
            'mercado_description'               => $request->mercado_description,
            'sspay_category_code'               => $request->sspay_category_code,
            'sspay_secret_key'                  => $request->sspay_secret_key,
            'payumoney_mode'                    => $request->payumoney_mode,
            'payumoney_merchant_key'            => $request->payumoney_merchant_key,
            'payumoney_salt_key'                => $request->payumoney_salt_key,
            'paytab_profile_id'                 => $request->paytab_profile_id,
            'paytab_server_key'                 => $request->paytab_server_key,
            'paytab_region'                     => $request->paytab_region,
            'paytab_currency'                   => $request->paytab_currency,
            'bkash_currency'                    => $request->bkash_currency,
            'bkashsetting'                      => (in_array('bkash', $request->get('paymentsetting'))) ? 'on' : 'off',
            'payumoneysetting'                  => (in_array('payumoney', $request->get('paymentsetting'))) ? 'on' : 'off',
            'paytabsetting'                     => (in_array('paytab', $request->get('paymentsetting'))) ? 'on' : 'off',
            'sspaysetting'                      => (in_array('sspay', $request->get('paymentsetting'))) ? 'on' : 'off',
            'mercadosetting'                    => (in_array('mercado', $request->get('paymentsetting'))) ? 'on' : 'off',
            'coingatesetting'                   => (in_array('coingate', $request->get('paymentsetting'))) ? 'on' : 'off',
            'stripesetting'                     => (in_array('stripe', $request->get('paymentsetting'))) ? 'on' : 'off',
            'paypalsetting'                     => (in_array('paypal', $request->get('paymentsetting'))) ? 'on' : 'off',
            'razorpaysetting'                   => (in_array('razorpay', $request->get('paymentsetting'))) ? 'on' : 'off',
            'offlinesetting'                    => (in_array('offline', $request->get('paymentsetting'))) ? 'on' : 'off',
            'paytmsetting'                      => (in_array('paytm', $request->get('paymentsetting'))) ? 'on' : 'off',
            'flutterwavesetting'                => (in_array('flutterwave', $request->get('paymentsetting'))) ? 'on' : 'off',
            'cashfreesetting'                   => (in_array('cashfree', $request->get('paymentsetting'))) ? 'on' : 'off',
            'paystacksetting'                   => (in_array('paystack', $request->get('paymentsetting'))) ? 'on' : 'off',
            'offlinepaymentsetting'             => (in_array('offlinepayment', $request->get('paymentsetting'))) ? 'on' : 'off',
        ];
        if ($bkashJson) {
            $paymentData['bkash_json_file']     = $bkashJson;
        }
        $arrEnv = [
            'CURRENCY'                          => $request->currency,
            'CURRENCY_SYMBOL'                   => $request->currency_symbol
        ];
        UtilityFacades::setEnvironmentValue($arrEnv);
        if (Auth::user()->type == "Super Admin") {
            Self::updateSettings($paymentDescription);
        }
        Self::updateSettings($paymentData);
        return redirect()->back()->with('success', __('Payment settings updated successfully.'));
    }


    public function smsSettingUpdate(Request $request)
    {
        if ($request->multisms_setting && $request->multisms_setting == 'on') {
            request()->validate([
                'smssetting'            => 'required|string|max:191',
            ]);
            if ($request->smssetting == 'twilio') {
                request()->validate([
                    'twilio_sid'        => 'required|string|max:191',
                    'twilio_auth_token' => 'required|string|max:191',
                    'twilio_verify_sid' => 'required|string|max:191',
                    'twilio_number'     => 'required|string|max:191',
                ]);
            } else if ($request->smssetting == 'nexmo') {
                request()->validate([
                    'nexmo_key'         => 'required|string|max:191',
                    'nexmo_secret'      => 'required|string|max:191',
                ]);
            } else if ($request->smssetting == 'fast2sms') {
                request()->validate([
                    'fast2sms_api_key'  => 'required|string|max:191',
                ]);
            }
            $smsSettingData = [
                'multisms_setting'      => ($request->multisms_setting && $request->multisms_setting == 'on') ? 'on' : 'off',
                'smssetting'            => ($request->smssetting),
                'nexmo_key'             => $request->nexmo_key,
                'nexmo_secret'          => $request->nexmo_secret,
                'twilio_sid'            => $request->twilio_sid,
                'twilio_auth_token'     => $request->twilio_auth_token,
                'twilio_verify_sid'     => $request->twilio_verify_sid,
                'twilio_number'         => $request->twilio_number,
                'fast2sms_api_key'      => $request->fast2sms_api_key,
            ];
        } else {
            $smsSettingData = [
                'multisms_setting'      => 'off',
            ];
        }
        Self::updateSettings($smsSettingData);
        return redirect()->back()->with('success',  __('Sms setting updated successfully.'));
    }

    public function cookieSettingUpdate(Request $request)
    {
        request()->validate([
            'cookie_title'                      => 'required|string|max:191',
            'strictly_cookie_title'             => 'required|string|max:191',
            'cookie_description'                => 'required|string',
            'strictly_cookie_description'       => 'required|string',
            'contact_us_description'            => 'required|string',
            'contact_us_url'                    => 'required|string|max:191',
        ]);
        $cookieSettingData = [
            'cookie_setting_enable'             => ($request->cookie_setting_enable  && $request->cookie_setting_enable == 'on') ? 'on' : 'off',
            'cookie_logging'                    => ($request->cookie_logging && $request->cookie_logging == 'on') ? 'on' : 'off',
            'necessary_cookies'                 => ($request->cookie_logging && $request->necessary_cookies == 'on') ? 'on' : 'off',
            'cookie_title'                      => $request->cookie_title,
            'strictly_cookie_title'             => $request->strictly_cookie_title,
            'cookie_description'                => $request->cookie_description,
            'strictly_cookie_description'       => $request->strictly_cookie_description,
            'contact_us_description'            => $request->contact_us_description,
            'contact_us_url'                    => $request->contact_us_url,
        ];

        Self::updateSettings($cookieSettingData);
        return redirect()->back()->with('success', __('Cookie Setting updated successfully'));
    }


    public function cookieConsent(Request $request)
    {
        if (UtilityFacades::getsettings('cookie_setting_enable') == "on" &&  UtilityFacades::getsettings('cookie_logging') == "on") {
            try {
                $whichbrowser           = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);
                // Generate new CSV line
                $browser_name           = $whichbrowser->browser->name ?? null;
                $os_name                = $whichbrowser->os->name ?? null;
                $browser_language       = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
                $device_type            = UtilityFacades::getDeviceType($_SERVER['HTTP_USER_AGENT']);
                $ip                     = $_SERVER['REMOTE_ADDR'];
                $query                  = @unserialize(file_get_contents('http://ip-api.com/php/' . $ip));
                if ($query['status'] == 'success') {
                    $date               = (new \DateTime())->format('Y-m-d');
                    $time               = (new \DateTime())->format('H:i:s') . ' UTC';
                    $newLine            = implode(',', [$ip, $date, $time, implode('-', $request['cookie']), $device_type, $browser_language, $browser_name, $os_name, isset($query) ? $query['country'] : '', isset($query) ? $query['region'] : '', isset($query) ? $query['regionName'] : '', isset($query) ? $query['city'] : '', isset($query) ? $query['zip'] : '', isset($query) ? $query['lat'] : '', isset($query) ? $query['lon'] : '']);
                    if (!fileExists(Storage::url('cookie-csv/cookie-data.csv'))) {
                        $firstLine      = 'IP,Date,Time,Accepted-cookies,Device type,Browser anguage,Browser name,OS Name,Country,Region,RegionName,City,Zipcode,Lat,Lon';
                        file_put_contents(base_path() . Storage::url('cookie-csv/cookie-data.csv'), $firstLine . PHP_EOL, FILE_APPEND | LOCK_EX);
                    }
                    file_put_contents(base_path() . Storage::url('cookie-csv/cookie-data.csv'), $newLine . PHP_EOL, FILE_APPEND | LOCK_EX);
                }
            } catch (\Throwable $th) {
                return response()->json('error');
            }
            return response()->json('success');
        }
        return response()->json('error');
    }

    public function seoSettingsUpdate(Request $request)
    {
        if ($request->seo_setting && $request->seo_setting == 'on') {
            request()->validate([
                'seo_setting'                => 'required|string|max:191',
            ]);
            request()->validate([
                'meta_title'                 => 'required|string|max:191',
                'meta_keywords'              => 'required|string',
                'meta_description'           => 'required|string',
                'meta_image'                 => 'image|mimes:png,jpg,jpeg',
            ]);

            $seoSettingData = [
                'seo_setting'                    => ($request->seo_setting) ? 'on' : 'off',
                'meta_title'                     => $request->meta_title,
                'meta_keywords'                  => $request->meta_keywords,
                'meta_description'               => $request->meta_description,
            ];
            if ($request->hasFile('meta_image')) {
                $metaImage                       = 'meta_image' . '.' . $request->meta_image->getClientOriginalExtension();
                $logoPath                        = "seo";
                $image                           = request()->file('meta_image')->storeAs(
                    $logoPath,
                    $metaImage,
                );
                $seoSettingData['meta_image']    = $image;
            }
        } else {
            $seoSettingData = [
                'seo_setting'                    => 'off',
            ];
        }
        Self::updateSettings($seoSettingData);
        return redirect()->back()->with('success',  __('SEO setting updated successfully.'));
    }

    public function socialSettingUpdate(Request $request)
    {
        if ($request->socialsetting) {
            if (in_array('google', $request->get('socialsetting'))) {
                request()->validate([
                    'google_client_id'              => 'required',
                    'google_client_secret'          => 'required',
                    'google_redirect'               => 'required',
                ], [
                    'google_client_id.regex'        => 'Invalid entry! the google key only letters, underscore and numbers are allowed.',
                    'google_client_secret.regex'    => 'Invalid entry! the google secret only letters, underscore and numbers are allowed.',
                    'google_redirect.regex'         => 'Invalid entry! the google redirect only letters, underscore and numbers are allowed.',
                ]);
            }
            if (in_array('facebook', $request->get('socialsetting'))) {
                request()->validate([
                    'facebook_client_id'            => 'required',
                    'facebook_client_secret'        => 'required',
                    'facebook_redirect'             => 'required',
                ], [
                    'facebook_client_id.regex'      => 'Invalid entry! the facebook key only letters, underscore and numbers are allowed.',
                    'facebook_client_secret.regex'  => 'Invalid entry! the facebook secret only letters, underscore and numbers are allowed.',
                    'facebook_redirect.regex'       => 'Invalid entry! the facebook redirect only letters, underscore and numbers are allowed.',
                ]);
            }
            if (in_array('github', $request->get('socialsetting'))) {
                request()->validate([
                    'github_client_id'              => 'required',
                    'github_client_secret'          => 'required',
                    'github_redirect'               => 'required',
                ], [
                    'github_client_id.regex'        => 'Invalid entry! the github key only letters, underscore and numbers are allowed.',
                    'github_client_secret.regex'    => 'Invalid entry! the github secret only letters, underscore and numbers are allowed.',
                    'github_redirect.regex'         => 'Invalid entry! the github redirect only letters, underscore and numbers are allowed.',
                ]);
            }
            $socialSettingData = [
                'google_client_id'                  => $request->google_client_id,
                'google_client_secret'              => $request->google_client_secret,
                'google_redirect'                   => $request->google_redirect,
                'facebook_client_id'                => $request->facebook_client_id,
                'facebook_client_secret'            => $request->facebook_client_secret,
                'facebook_redirect'                 => $request->facebook_redirect,
                'github_client_id'                  => $request->github_client_id,
                'github_client_secret'              => $request->github_client_secret,
                'github_redirect'                   => $request->github_redirect,
                'googlesetting'                     => (in_array('google', $request->get('socialsetting'))) ? 'on' : 'off',
                'facebooksetting'                   => (in_array('facebook', $request->get('socialsetting'))) ? 'on' : 'off',
                'githubsetting'                     => (in_array('github', $request->get('socialsetting'))) ? 'on' : 'off',
            ];
        } else {
            $socialSettingData = [
                'googlesetting'                     => 'off',
                'facebooksetting'                   => 'off',
                'githubsetting'                     => 'off',
            ];
        }
        Self::updateSettings($socialSettingData);
        return redirect()->back()->with('success', __('Social settings updated successfully.'));
    }

    public function googleSettingUpdate(Request  $request)
    {
        if ($request->google_calendar_enable && $request->google_calendar_enable == 'on') {
            request()->validate([
                'google_calendar_json_file' => 'required|file|mimes:json',
                'google_calendar_id'        => 'required|string|max:191',
            ]);
            if ($request->hasFile('google_calendar_json_file')) {
                $dir    = md5(time());
                $path   = $dir . '/' . md5(time()) . "." . $request->google_calendar_json_file->getClientOriginalExtension();
                $file   = $request->file('google_calendar_json_file');
                $file->storeAs('google-json-file', $path);
            }
            $googleSettingData = [
                'google_calendar_enable'    => ($request->google_calendar_enable)  ? 'on' : 'off',
                'google_calendar_id'        => $request->google_calendar_id,
                'google_calendar_json_file' => $path,
            ];
            Self::updateSettings($googleSettingData);
        } else {
            $googleSettingData = [
                'google_calendar_enable'     => 'off',
            ];

            Self::updateSettings($googleSettingData);
        }
        return redirect()->back()->with('success',  __('Google Calendar API key updated successfully.'));
    }

    public function googleMapUpdate(Request $request)
    {
        if ($request->google_map_enable && $request->google_map_enable == 'on') {
            request()->validate([
                'google_map_api'    => 'required|string|max:191',
            ]);
            $googleMapSettingData = [
                'google_map_enable'     => ($request->google_map_enable) ? 'on' : 'off',
                'google_map_api'        => $request->google_map_api,
            ];
            Self::updateSettings($googleMapSettingData);
        } else {
            $googleMapSettingData = [
                'google_map_enable'     => 'off',
            ];
            Self::updateSettings($googleMapSettingData);
        }
        return redirect()->back()->with('success',  __('Google map API key updated successfully.'));
    }

    private function updateSettings($input)
    {
        foreach ($input as $key => $value) {
            DB::table('settings')
                ->updateOrInsert(
                    ['key' => $key, 'created_by' => Auth::user()->admin_id],
                    ['value' => $value]
                );
        }
    }

    public function testMail()
    {
        return view('settings.test-mail');
    }

    public function testSendMail(Request $request)
    {
        request()->validate(
            ['email' => 'required|email']
        );
        if (UtilityFacades::getsettings('email_setting_enable') == 'on') {
            if (MailTemplate::where('mailable', TestMail::class)->first()) {
                try {
                    Mail::to($request->email)->send(new TestMail());
                } catch (\Exception $e) {
                    return redirect()->back()->with('errors', $e->getMessage());
                }
            }
        }
        return redirect()->back()->with('success', __('Email send successfully.'));
    }

    public function upload(Request $request)
    {
        if ($request->hasFile('upload')) {
            $originName         = $request->file('upload')->getClientOriginalName();
            $fileName           = pathinfo($originName, PATHINFO_FILENAME);
            $extension          = $request->file('upload')->getClientOriginalExtension();
            $fileName           = $fileName . '_' . time() . '.' . $extension;
            $request->file('upload')->move(public_path('images'), $fileName);
            $CKEditorFuncNum    = $request->input('CKEditorFuncNum');
            $url                = asset('public/images/' . $fileName);
            $msg                = 'Image uploaded successfully';
            $response           = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";
            @header('Content-type: text/html; charset=utf-8');
            echo $response;
        }
    }

    public function pwaSettingUpdate(Request $request)
    {
        request()->validate([
            'pwa_icon_128' => 'mimes:png,image',
            'pwa_icon_144' => 'mimes:png,image',
            'pwa_icon_152' => 'mimes:png,image',
            'pwa_icon_192' => 'mimes:png,image',
            'pwa_icon_256' => 'mimes:png,image',
            'pwa_icon_512' => 'mimes:png,image',
        ]);

        $pwaIcon128   = UtilityFacades::getsettings('pwa_icon_128');
        $pwaIcon144   = UtilityFacades::getsettings('pwa_icon_144');
        $pwaIcon152   = UtilityFacades::getsettings('pwa_icon_152');
        $pwaIcon192   = UtilityFacades::getsettings('pwa_icon_192');
        $pwaIcon256   = UtilityFacades::getsettings('pwa_icon_256');
        $pwaIcon512   = UtilityFacades::getsettings('pwa_icon_512');

        if ($request->hasFile('pwa_icon_128')) {
            $pwaIcon128    = 'pwa-icon-128' . '.' . 'png';
            $logoPath   = "pwa-image";
            $image      = request()->file('pwa_icon_128')->storeAs(
                $logoPath,
                $pwaIcon128,
            );
            $appSettingData['pwa_icon_128']         = $image;
        }

        if ($request->hasFile('pwa_icon_144')) {
            $pwaIcon144    = 'pwa-icon-144' . '.' . 'png';
            $logoPath   = "pwa-image";
            $image      = request()->file('pwa_icon_144')->storeAs(
                $logoPath,
                $pwaIcon144,
            );
            $appSettingData['pwa_icon_144']         = $image;
        }
        if ($request->hasFile('pwa_icon_152')) {
            $pwaIcon152    = 'pwa-icon-152' . '.' . 'png';
            $logoPath   = "pwa-image";
            $image      = request()->file('pwa_icon_152')->storeAs(
                $logoPath,
                $pwaIcon152,
            );
            $appSettingData['pwa_icon_152']         = $image;
        }
        if ($request->hasFile('pwa_icon_192')) {
            $pwaIcon192    = 'pwa-icon-192' . '.' . 'png';
            $logoPath   = "pwa-image";
            $image      = request()->file('pwa_icon_192')->storeAs(
                $logoPath,
                $pwaIcon192,
            );
            $appSettingData['pwa_icon_192']         = $image;
        }
        if ($request->hasFile('pwa_icon_256')) {
            $pwaIcon256    = 'pwa-icon-256' . '.' . 'png';
            $logoPath   = "pwa-image";
            $image      = request()->file('pwa_icon_256')->storeAs(
                $logoPath,
                $pwaIcon256,
            );
            $appSettingData['pwa_icon_256']         = $image;
        }
        if ($request->hasFile('pwa_icon_512')) {
            $pwaIcon512    = 'pwa-icon-512' . '.' . 'png';
            $logoPath   = "pwa-image";
            $image      = request()->file('pwa_icon_512')->storeAs(
                $logoPath,
                $pwaIcon512,
            );
            $appSettingData['pwa_icon_512']         = $image;
        }

        // if ($request->pwa_icon_128) {
        //     $pwaIcon128             = 'pwa-icon-128' . '.' . $request->pwa_icon_128->getClientOriginalExtension();
        //     $logoPath               = "pwa-image";
        //     $image                  = request()->file('pwa_icon_128')->storeAs($logoPath, $pwaIcon128);
        //     $data['pwa_icon_128']   = $image;
        // }
        // if ($request->pwa_icon_144) {
        //     $pwaIcon144             = 'pwa-icon-144' . '.' . $request->pwa_icon_144->getClientOriginalExtension();
        //     $logoPath               = "pwa-image";
        //     $image144               = request()->file('pwa_icon_144')->storeAs($logoPath, $pwaIcon144);
        //     $data['pwa_icon_144']   = $image144;
        // }
        // if ($request->pwa_icon_152) {
        //     $pwaIcon152             = 'pwa-icon-152' . '.' . $request->pwa_icon_152->getClientOriginalExtension();
        //     $logoPath               = "pwa-image";
        //     $image152               = request()->file('pwa_icon_152')->storeAs($logoPath, $pwaIcon152);
        //     $data['pwa_icon_152']   = $image152;
        // }
        // if ($request->pwa_icon_192) {
        //     $pwaIcon192             = 'pwa-icon-192' . '.' . $request->pwa_icon_192->getClientOriginalExtension();
        //     $logoPath               = "pwa-image";
        //     $image192               = request()->file('pwa_icon_192')->storeAs($logoPath, $pwaIcon192);
        //     $data['pwa_icon_192']   = $image192;
        // }
        // if ($request->pwa_icon_256) {
        //     $pwaIcon256             = 'pwa-icon-256' . '.' . $request->pwa_icon_256->getClientOriginalExtension();
        //     $logoPath               = "pwa-image";
        //     $image256               = request()->file('pwa_icon_256')->storeAs($logoPath, $pwaIcon256);
        //     $data['pwa_icon_256']   = $image256;
        // }
        // if ($request->hasFile('pwa_icon_512')) {
        //     $pwaIcon512             = 'pwa-icon-512' . '.' . $request->pwa_icon_512->getClientOriginalExtension();
        //     $logoPath               = "pwa-image";
        //     $image512               = request()->file('pwa_icon_512')->storeAs($logoPath, $pwaIcon512);
        //     $data['pwa_icon_512']   = $image512;
        // }

        $this->updateSettings($appSettingData);
        return redirect()->back()->with('success',  __('PWA setting updated successfully.'));
    }
}
