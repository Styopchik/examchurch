<?php

namespace App\Facades;

use App\Models\Order;
use App\Models\RequestUser;
use App\Models\settings;
use Carbon\Carbon;
use App\Mail\ApproveMail;
use App\Models\FormValue;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserCoupon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Spatie\MailTemplates\Models\MailTemplate;

class Utility
{
    public function settings()
    {
        $settingDatas = DB::table('settings');
        $settingDatas = $settingDatas->get();
        $settings = [
            "date_format" => "M j, Y",
            "time_format" => "g:i A",
        ];
        foreach ($settingDatas as $settingData) {
            $settings[$settingData->key] = $settingData->value;
        }
        return $settings;
    }

    public function dateFormat($date)
    {
        return Carbon::parse($date)->format(Self::getsettings('date_format'));
    }

    public function timeFormat($time)
    {
        return Carbon::parse($time)->format(Self::getsettings('time_format'));
    }

    public function dateTimeFormat($date)
    {
        return Carbon::parse($date)->format(Self::getsettings('date_format') . ' ' . Self::getsettings('time_format'));
    }

    public function setEnvironmentValue(array $values)
    {
        $envFile = app()->environmentFilePath();
        $str     = file_get_contents($envFile);
        if (count($values) > 0) {
            foreach ($values as $envKey => $envValue) {
                $keyPosition       = strpos($str, "{$envKey}=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine           = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
                if (!$keyPosition || !$endOfLinePosition || !$oldLine) {
                    $str .= "{$envKey}='{$envValue}'\n";
                } else {
                    $str = str_replace($oldLine, "{$envKey}='{$envValue}'", $str);
                }
            }
        }
        $str = substr($str, 0, -1);
        $str .= "\n";
        if (!file_put_contents($envFile, $str)) {
            return false;
        }
        return true;
    }

    public function keysettings($key = '', $formUserId = '')
    {
        $settingValue          = '';
        $createdBy             = '';
        if ($formUserId) {
            $createdBy         = $formUserId;
        }
        $setting               = settings::select('value')->where('created_by', $createdBy)->where('key', $key)->first();
        $settingValue          = '';
        if (!empty($setting->value)) {
            $settingValue      = $setting->value;
        }
        return $settingValue;
    }

    public function getValByName($key)
    {
        $setting = Self::settings();
        if (!isset($setting[$key]) || empty($setting[$key])) {
            $setting[$key] = '';
        }
        return $setting[$key];
    }

    public function languages()
    {
        $dir     = base_path() . '/resources/lang/';
        $glob    = glob($dir . "*", GLOB_ONLYDIR);
        $arrLang = array_map(
            function ($value) use ($dir) {
                return str_replace($dir, '', $value);
            },
            $glob
        );
        $arrLang = array_map(
            function ($value) use ($dir) {
                return preg_replace('/[0-9]+/', '', $value);
            },
            $arrLang
        );
        $arrLang = array_filter($arrLang);
        return $arrLang;
    }

    public static function deleteDirectory($directory)
    {
        if (!file_exists($directory)) {
            return true;
        }
        if (!is_dir($directory)) {
            return unlink($directory);
        }
        foreach (scandir($directory) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (!Self::deleteDirectory($directory . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        return rmdir($directory);
    }

    public function logoSetting($key = '')
    {
        $settingValue       = '';
        $createdBy          = 1;
        $setting            = settings::select('value')->where('created_by', $createdBy)->where('key', $key)->first();
        if (!empty($setting->value)) {
            $settingValue   = $setting->value;
        }
        return $settingValue;
    }

    public function getsettings($key = '')
    {
        $settingValue       = '';
        if (Auth::user()) {
            $createdBy      = Auth::user()->admin_id;
        } else {
            $createdBy      = '';
        }
        if ($createdBy) {
            $setting        = settings::select('value')->where('created_by', $createdBy)->where('key', $key)->first();
        } else {
            $setting        = settings::select('value')->where('key', $key)->first();
        }
        if (!empty($setting->value)) {
            $settingValue = $setting->value;
        }
        return $settingValue;
    }

    public function fileSystemSettings($key = '')
    {
        $settingValue       = '';
        if (Auth::user()) {
            $createdBy      = Auth::user()->admin_id;
        } else {
            $createdBy      = '';
        }
        if ($createdBy) {
            $setting        = settings::select('value')->where('key', $key)->first();
        } else {
            $setting        = settings::select('value')->where('key', $key)->first();
        }
        if (!empty($setting->value)) {
            $settingValue = $setting->value;
        }
        return $settingValue;
    }

    public static function colorCodeData($type)
    {
        if ($type == 'event') {
            return 1;
        } elseif ($type == 'zoom_meeting') {
            return 2;
        } elseif ($type == 'task') {
            return 3;
        } elseif ($type == 'appointment') {
            return 11;
        } elseif ($type == 'rotas') {
            return 3;
        } elseif ($type == 'holiday') {
            return 4;
        } elseif ($type == 'call') {
            return 10;
        } elseif ($type == 'meeting') {
            return 5;
        } elseif ($type == 'leave') {
            return 6;
        } elseif ($type == 'work_order') {
            return 7;
        } elseif ($type == 'lead') {
            return 7;
        } elseif ($type == 'deal') {
            return 8;
        } elseif ($type == 'interview_schedule') {
            return 9;
        } else {
            return 11;
        }
    }

    public static $colorCode = [
        1 => 'event-warning',
        2 => 'event-secondary',
        3 => 'event-info',
        4 => 'event-warning',
        5 => 'event-danger',
        6 => 'event-dark',
        7 => 'event-black',
        8 => 'event-info',
        9 => 'event-dark',
        10 => 'event-success',
        11 => 'event-warning',
    ];

    public function widgetChartData($formId)
    {
        $chartArray = [];
        $formValues = FormValue::select('forms.json as form_json', 'form_values.*')->where('form_id', $formId)->join('forms', 'forms.id', '=', 'form_values.form_id');
        $formValues = $formValues->get();
        foreach ($formValues as $formValue) {
            $array1 = json_decode($formValue->form_json);
            foreach ($array1 as $rows1) {
                foreach ($rows1 as $row_key1 => $row1) {
                    if (property_exists($row1, 'name')) {
                        if (!isset($chartArray[$row1->name])) {
                            $options = [];
                            if ($row1->type == 'radio-group' || $row1->type == 'select' || $row1->type == 'checkbox-group') {
                                foreach ($row1->values as $value) {
                                    $options[$value->label] = 0;
                                }
                                if (isset($row1->value)) {
                                    $options['other'] = 0;
                                }
                                if (isset($row1->other)) {
                                    $options['other'] = 0;
                                }
                            } elseif ($row1->type == 'starRating') {
                                $options = [
                                    '0' => 0, '0.5' => 0, '1' => 0, '1.5' => 0, '2' => 0, '2.5' => 0, '3' => 0, '3.5' => 0, '4' => 0, '4.5' => 0, '5' => 0,
                                ];
                            } elseif ($row1->type == 'date') {
                                $options = [];
                            } else {
                                $row1->chart_type = '';
                                $row1->label = '';
                            }
                            if (isset($row1->is_enable_chart)) {
                                $tmp = [
                                    'name' => $row1->name,
                                    'label' => $row1->label,
                                    'options' => $options,
                                    'is_enable_chart' => $row1->is_enable_chart,
                                    'chart_type' => $row1->chart_type
                                ];
                                $chartArray[$row1->name] = $tmp;
                            } else {
                                $tmp = [
                                    'name' => $row1->name,
                                    'label' => $row1->label,
                                    'options' => $options,
                                    'chart_type' => $row1->chart_type
                                ];
                                $chartArray[$row1->name] = $tmp;
                            }
                        }
                    }
                }
            }
            $array = json_decode($formValue->json);
            foreach ($array as $rows) {
                foreach ($rows as $row_key => $row) {
                    if ($row->type == 'radio-group' || $row->type == 'select' || $row->type == 'checkbox-group'   || $row->type == 'starRating' || $row->type == 'date' || $row->type == 'number') {
                        if (!isset($chartArray[$row->name])) {
                            $options = [];
                            if ($row->type == 'radio-group' || $row->type == 'select' || $row->type == 'checkbox-group') {
                                foreach ($row->values as $value) {
                                    $options[$value->label] = 0;
                                }
                                if (isset($row->value)) {
                                    $options['other'] = 0;
                                }
                                if (isset($row->other)) {
                                    $options['other'] = 0;
                                }
                            } elseif ($row->type == 'starRating') {
                                $options = [
                                    '0' => 0, '0.5' => 0, '1' => 0, '1.5' => 0, '2' => 0, '2.5' => 0, '3' => 0, '3.5' => 0, '4' => 0, '4.5' => 0, '5' => 0,
                                ];
                            } elseif ($row->type == 'date') {
                                $options = [];
                            } else {
                                $row->chart_type = '';
                                $row->label = '';
                            }

                            if (isset($row->is_enable_chart)) {
                                $tmp = [
                                    'name' => $row->name,
                                    'label' => $row->label,
                                    'options' => $options,
                                    'is_enable_chart' => $row->is_enable_chart,
                                    'chart_type' => $chartArray
                                ];
                                $chartArray[$row->name] = $tmp;
                            } else {
                                $tmp = [
                                    'name' => $row->name,
                                    'label' => $row->label,
                                    'options' => $options,
                                    'chart_type' => $chartArray
                                ];
                                $chartArray[$row->name] = $tmp;
                            }
                        }
                        if ($row->type == 'radio-group' || $row->type == 'select' || $row->type == 'checkbox-group') {
                            foreach ($row->values as $value) {
                                if (isset($value->selected)) {
                                    $chartArray[$row->name]['options'][$value->label]++;
                                }
                            }
                            if (isset($row->value)) {
                                if (!isset($chartArray[$row->name]['options']['other'])) {
                                    $chartArray[$row->name]['options']['other'] = 0;
                                }
                                $chartArray[$row->name]['options']['other']++;
                            }
                        }
                        if ($row->type == 'starRating') {
                            $chartArray[$row->name]['options'][$row->value]++;
                        }
                        if ($row->type == 'date') {
                            if (!isset($chartArray[$row->name]['options'][$row->value])) {
                                $chartArray[$row->name]['options'][$row->value] = 0;
                            }
                            $chartArray[$row->name]['options'][$row->value]++;
                        }
                    }
                }
            }
        }
        return $chartArray;
    }

    public function chartData($formId)
    {
        $chartArray = [];
        $formValues = FormValue::select('forms.json as form_json', 'form_values.*')->where('form_id', $formId)->join('forms', 'forms.id', '=', 'form_values.form_id');
        $formValues = $formValues->get();
        foreach ($formValues as $formValue) {
            $array1 = json_decode($formValue->form_json);
            foreach ($array1 as $rows1) {
                foreach ($rows1 as $row_key1 => $row1) {
                    if (isset($row1->is_enable_chart) && $row1->is_enable_chart) {
                        if (!isset($chartArray[$row1->name])) {
                            $options = [];
                            if ($row1->type == 'radio-group' || $row1->type == 'select' || $row1->type == 'checkbox-group') {
                                foreach ($row1->values as $value) {
                                    $options[$value->label] = 0;
                                }
                                if (isset($row1->value)) {
                                    $options['other'] = 0;
                                }
                                if (isset($row1->other)) {

                                    $options['other'] = 0;
                                }
                            } elseif ($row1->type == 'starRating') {
                                $options = [
                                    '0' => 0, '0.5' => 0, '1' => 0, '1.5' => 0, '2' => 0, '2.5' => 0, '3' => 0, '3.5' => 0, '4' => 0, '4.5' => 0, '5' => 0,
                                ];
                            } elseif ($row1->type == 'date' || $row1->type == 'number') {
                                $options = [];
                            }
                            if (isset($row1->is_enable_chart)) {
                                $tmp = [
                                    'name' => $row1->name,
                                    'label' => $row1->label,
                                    'options' => $options,
                                    'is_enable_chart' => $row1->is_enable_chart,
                                    'chart_type' => $row1->chart_type
                                ];
                                $chartArray[$row1->name] = $tmp;
                            } else {
                                $tmp = [
                                    'name' => $row1->name,
                                    'label' => $row1->label,
                                    'options' => $options,
                                    'chart_type' => $chartArray
                                ];
                                $chartArray[$row1->name] = $tmp;
                            }
                        }
                    }
                }
            }
            $array = json_decode($formValue->json);
            foreach ($array as $rows) {
                foreach ($rows as $row_key => $row) {
                    if ($row->type == 'radio-group' || $row->type == 'select' || $row->type == 'checkbox-group'   || $row->type == 'starRating' || $row->type == 'date' || $row->type == 'number') {
                        if (!isset($chartArray[$row->name])) {
                            $options = [];
                            if ($row->type == 'radio-group' || $row->type == 'select' || $row->type == 'checkbox-group') {
                                foreach ($row->values as $value) {
                                    $options[$value->label] = 0;
                                }
                                if (isset($row->value)) {
                                    $options['other'] = 0;
                                }
                                if (isset($row->other)) {
                                    $options['other'] = 0;
                                }
                            } elseif ($row->type == 'starRating') {
                                $options = [
                                    '0' => 0, '0.5' => 0, '1' => 0, '1.5' => 0, '2' => 0, '2.5' => 0, '3' => 0, '3.5' => 0, '4' => 0, '4.5' => 0, '5' => 0,
                                ];
                            } elseif ($row->type == 'date' || $row->type == 'number') {
                                $options = [];
                            }
                            if (isset($row->is_enable_chart)) {
                                $tmp = [
                                    'name' => $row->name,
                                    'label' => $row->label,
                                    'options' => $options,
                                    'is_enable_chart' => $row->is_enable_chart,
                                    'chart_type' => $row->chart_type
                                ];
                                $chartArray[$row->name] = $tmp;
                            } else {
                                $tmp = [
                                    'name' => $row->name,
                                    'label' => $row->label,
                                    'options' => $options,
                                    'chart_type' => $chartArray
                                ];
                                $chartArray[$row->name] = $tmp;
                            }
                        }
                        if ($row->type == 'radio-group' || $row->type == 'select' || $row->type == 'checkbox-group') {
                            foreach ($row->values as $value) {
                                if (isset($value->selected)) {
                                    $chartArray[$row->name]['options'][$value->label]++;
                                }
                            }
                            if (isset($row->value)) {
                                if (!isset($chartArray[$row->name]['options']['other'])) {
                                    $chartArray[$row->name]['options']['other'] = 0;
                                }
                                $chartArray[$row->name]['options']['other']++;
                            }
                        }
                        if ($row->type == 'starRating') {
                            $chartArray[$row->name]['options'][$row->value]++;
                        }
                        if ($row->type == 'date' ||  $row->type == 'number') {
                            if (!isset($chartArray[$row->name]['options'][$row->value])) {
                                $chartArray[$row->name]['options'][$row->value] = 0;
                            }
                            $chartArray[$row->name]['options'][$row->value]++;
                        }
                    }
                }
            }
        }
        return $chartArray;
    }

    public function approvedRequest($requestUserId)
    {
        if (Self::getsettings('approve_type') == 'Auto') {
            if (is_array($requestUserId)) {
                $requestUserId = $requestUserId['request_user_id'];
            } else if (isset($requestUserId->id)) {
                $requestUserId = $requestUserId->id;
            } else {
                $requestUserId = $requestUserId;
            }
        }
        $requestUser                    = RequestUser::find($requestUserId);
        $order                          = Order::where('request_user_id', $requestUser->id)->first();
        $input['name']                  = $requestUser->name;
        $input['email']                 = $requestUser->email;
        $input['password']              = $requestUser->password;
        $input['country_code']          = $requestUser->country_code;
        $input['phone']                 = $requestUser->phone;
        $input['type']                  = 'Admin';
        $input['plan_id']               = 1;
        $input['created_by']            = 1;
        $input['email_verified_at']     = Carbon::now()->toDateTimeString();
        $input['phone_verified_at']     = (Self::getsettings('sms_verification') == '1') ? null : Carbon::now()->toDateTimeString();
        $input['lang']                  = 'en';
        $input['avatar']                = 'avatar/avatar.png';
        $user                           = User::create($input);
        $user->assignRole('Admin');
        $usercoupon                     =  UserCoupon::where('userrequest', $requestUser->id)->first();
        if ($usercoupon) {
            $usercoupon->user           = $user->id;
            $usercoupon->userrequest    = null;
            $usercoupon->save();
        }
        $plan                           = Plan::find($order['plan_id']);
        $user->plan_id                  = $plan->id;
        if ($plan->durationtype == 'Month' && $plan->id != '1') {
            $user->plan_expired_date    = Carbon::now()->addMonths($plan->duration)->isoFormat('YYYY-MM-DD');
        } elseif ($plan->durationtype == 'Year' && $plan->id != '1') {
            $user->plan_expired_date    = Carbon::now()->addYears($plan->duration)->isoFormat('YYYY-MM-DD');
        } else {
            $user->plan_expired_date    = null;
        }
        $user->save();
        $order->user_id                 = $user->id;
        $order->save();
        $requestUser->is_approved       = 1;
        $requestUser->save();
        if (Self::getsettings('email_setting_enable') == 'on') {
            if (MailTemplate::where('mailable', ApproveMail::class)->first()) {
                try {
                    Mail::to($requestUser->email)->send(new ApproveMail($requestUser));
                } catch (\Exception $e) {
                    return redirect()->back()->with('errors', $e->getMessage());
                }
            }
        }
    }

    public function getAdminPaymentSettings()
    {
        $paymentSetting = [];
        $paymentSetting['stripesetting']                   = Self::keysettings('stripesetting', 1);
        $paymentSetting['stripe_key']                      = Self::keysettings('stripe_key', 1);
        $paymentSetting['stripe_secret']                   = Self::keysettings('stripe_secret', 1);
        $paymentSetting['stripe_description']              = Self::keysettings('stripe_description', 1);

        $paymentSetting['paypalsetting']                   = Self::keysettings('paypalsetting', 1);
        $paymentSetting['paypal_client_id']                = Self::keysettings('paypal_sandbox_client_id', 1);
        $paymentSetting['paypal_client_secret']            = Self::keysettings('paypal_sandbox_client_secret', 1);
        $paymentSetting['paypal_mode']                     = Self::keysettings('paypal_mode', 1);
        $paymentSetting['paypal_description']              = Self::keysettings('paypal_description', 1);

        $paymentSetting['razorpaysetting']                 = Self::keysettings('razorpaysetting', 1);
        $paymentSetting['razorpay_key']                    = Self::keysettings('razorpay_key', 1);
        $paymentSetting['razorpay_secret']                 = Self::keysettings('razorpay_secret', 1);
        $paymentSetting['razorpay_description']            = Self::keysettings('razorpay_description', 1);

        $paymentSetting['paystacksetting']                 = Self::keysettings('paystacksetting', 1);
        $paymentSetting['paystack_key']                    = Self::keysettings('paystack_public_key', 1);
        $paymentSetting['paystack_secret']                 = Self::keysettings('paystack_secret_key', 1);
        $paymentSetting['paystack_currency']               = Self::keysettings('paystack_currency', 1);
        $paymentSetting['paystack_description']            = Self::keysettings('paystack_description', 1);

        $paymentSetting['cashfreesetting']                 = Self::keysettings('cashfreesetting', 1);
        $paymentSetting['cashfree_app_id']                 = Self::keysettings('cashfree_app_id', 1);
        $paymentSetting['cashfree_secret_key']             = Self::keysettings('cashfree_secret_key', 1);
        $paymentSetting['cashfree_url']                    = Self::keysettings('cashfree_url', 1);
        $paymentSetting['cashfree_description']            = Self::keysettings('cashfree_description', 1);

        $paymentSetting['payumoneysetting']                = Self::keysettings('payumoneysetting', 1);
        $paymentSetting['payumoney_mode']                  = Self::keysettings('payumoney_mode', 1);
        $paymentSetting['payumoney_merchant_key']          = Self::keysettings('payumoney_merchant_key', 1);
        $paymentSetting['payumoney_salt_key']              = Self::keysettings('payumoney_salt_key', 1);
        $paymentSetting['payumoney_description']           = Self::keysettings('payumoney_description', 1);

        $paymentSetting['paytabsetting']                   = Self::keysettings('paytabsetting', 1);
        $paymentSetting['paytab_profile_id']               = Self::keysettings('paytab_profile_id', 1);
        $paymentSetting['paytab_server_key']               = Self::keysettings('paytab_server_key', 1);
        $paymentSetting['paytab_region']                   = Self::keysettings('paytab_region', 1);
        $paymentSetting['paytab_description']              = Self::keysettings('paytab_description', 1);

        $paymentSetting['flutterwavesetting']              = Self::keysettings('flutterwavesetting', 1);
        $paymentSetting['flutterwave_key']                 = Self::keysettings('flw_public_key', 1);
        $paymentSetting['flutterwave_secret']              = Self::keysettings('flw_secret_key', 1);
        $paymentSetting['flutterwave_description']         = Self::keysettings('flutterwave_description', 1);

        $paymentSetting['paytmsetting']                    = Self::keysettings('paytmsetting', 1);
        $paymentSetting['paytm_merchant_id']               = Self::keysettings('paytm_merchant_id', 1);
        $paymentSetting['paytm_merchant_key']              = Self::keysettings('paytm_merchant_key', 1);
        $paymentSetting['paytm_description']               = Self::keysettings('paytm_description', 1);
        $paymentSetting['paytm_environment']               = Self::keysettings('paytm_environment', 1);

        $paymentSetting['bkashsetting']                    = Self::keysettings('bkashsetting', 1);
        $paymentSetting['bkash_description']               = Self::keysettings('bkash_description', 1);

        $paymentSetting['coingatesetting']                 = Self::keysettings('coingatesetting', 1);
        $paymentSetting['coingate_environment']            = Self::keysettings('coingate_environment', 1);
        $paymentSetting['coingate_auth_token']             = Self::keysettings('coingate_auth_token', 1);
        $paymentSetting['coingate_description']            = Self::keysettings('coingate_description', 1);

        $paymentSetting['sspaysetting']                    = Self::keysettings('sspaysetting', 1);
        $paymentSetting['sspay_category_code']             = Self::keysettings('sspay_category_code', 1);
        $paymentSetting['sspay_secret_key']                = Self::keysettings('sspay_secret_key', 1);
        $paymentSetting['sspay_description']               = Self::keysettings('sspay_description', 1);

        $paymentSetting['mercadosetting']                  = Self::keysettings('mercadosetting', 1);
        $paymentSetting['mercado_mode']                    = Self::keysettings('mercado_mode', 1);
        $paymentSetting['mercado_access_token']            = Self::keysettings('mercado_access_token', 1);
        $paymentSetting['mercado_description']             = Self::keysettings('mercado_description', 1);

        $paymentSetting['offlinesetting']                  = Self::keysettings('offlinesetting', 1);
        $paymentSetting['payment_details']                 = Self::keysettings('payment_details', 1);

        $paymentSetting['currency_symbol']                 = env('CURRENCY_SYMBOL');
        $paymentSetting['currency']                        = env('CURRENCY');
        return $paymentSetting;
    }

    public function getpath($name)
    {
        $src = $name ? Storage::url($name) : Storage::url('logo/app-logo.png');
        return $src;
    }

    public function getFormPaymentTypes()
    {
        $paymentType = [];
        $paymentType[''] = 'Select payment';
        if (Self::getsettings('stripesetting') == 'on') {
            $paymentType['stripe']          = 'Stripe';
        }
        if (Self::getsettings('paypalsetting') == 'on') {
            $paymentType['paypal']          = 'Paypal';
        }
        if (Self::getsettings('razorpaysetting') == 'on') {
            $paymentType['razorpay']        = 'Razorpay';
        }
        if (Self::getsettings('paytmsetting') == 'on') {
            $paymentType['paytm']           = 'Paytm';
        }
        if (Self::getsettings('flutterwavesetting') == 'on') {
            $paymentType['flutterwave']     = 'Flutterwave';
        }
        if (Self::getsettings('paystacksetting') == 'on') {
            $paymentType['paystack']        = 'Paystack';
        }
        if (Self::getsettings('coingatesetting') == 'on') {
            $paymentType['coingate']        = 'Coingate';
        }
        if (Self::getsettings('mercadosetting') == 'on') {
            $paymentType['mercado']         = 'Mercado';
        }
        if (Self::getsettings('bkashsetting') == 'on') {
            $paymentType['bkash']           = 'bKash';
        }
        return $paymentType;
    }

    public function getpaymenttypes()
    {
        $paymentType = [];
        if (Self::keysettings('stripesetting', 1) == 'on') {
            $paymentType['stripe']          = 'Stripe';
        }
        if (Self::keysettings('paypalsetting', 1) == 'on') {
            $paymentType['paypal']          = 'Paypal';
        }
        if (Self::keysettings('razorpaysetting', 1) == 'on') {
            $paymentType['razorpay']        = 'Razorpay';
        }
        if (Self::keysettings('paytmsetting', 1) == 'on') {
            $paymentType['paytm']           = 'Paytm';
        }
        if (Self::keysettings('paystacksetting', 1) == 'on') {
            $paymentType['paystack']        = 'Paystack';
        }
        if (Self::keysettings('cashfreesetting', 1) == 'on') {
            $paymentType['cashfree']        = 'Cashfree';
        }
        if (Self::keysettings('flutterwavesetting', 1) == 'on') {
            $paymentType['flutterwave']     = 'Flutterwave';
        }
        if (Self::keysettings('mercadosetting', 1) == 'on') {
            $paymentType['mercado']         = 'Mercado Pago';
        }
        if (Self::keysettings('coingatesetting', 1) == 'on') {
            $paymentType['coingate']        = 'Coingate';
        }
        if (Self::keysettings('sspaysetting', 1) == 'on') {
            $paymentType['sspay']           = 'SSPay';
        }
        if (Self::keysettings('payumoneysetting', 1) == 'on') {
            $paymentType['payumoney']       = 'PayU Money';
        }
        if (Self::keysettings('paytabsetting', 1) == 'on') {
            $paymentType['paytab']          = 'Paytab';
        }
        if (Self::keysettings('bkashsetting', 1) == 'on') {
            $paymentType['bkash']           = 'bKash';
        }
        if (Self::keysettings('offlinesetting', 1) == 'on') {
            $paymentType['offline']         = 'Offline';
        }
        return $paymentType;
    }

    public function amountFormat($amount)
    {
        return env('CURRENCY_SYMBOL') . number_format($amount, 2);
    }

    public function calculateDiscount($price = "", $discount = "", $discountType = "")
    {
        $discountedAmount           = 0;
        if ($discount != "" && $price != "" && $discountType != "") {
            if ($discountType == "percentage") {
                $discountedAmount   = ($price / 100) * $discount;
            }
            if ($discountType == "flat") {
                $discountedAmount   = $discount;
            }
        }
        return $discountedAmount;
    }

    public function getDeviceType($userAgent)
    {
        $mobileRegex = '/(?:phone|windows\s+phone|ipod|blackberry|(?:android|bb\d+|meego|silk|googlebot) .+? mobile|palm|windows\s+ce|opera mini|avantgo|mobilesafari|docomo)/i';
        $tabletRegex = '/(?:ipad|playbook|(?:android|bb\d+|meego|silk)(?! .+? mobile))/i';
        if (preg_match_all($mobileRegex, $userAgent)) {
            return 'mobile';
        } else {
            if (preg_match_all($tabletRegex, $userAgent)) {
                return 'tablet';
            } else {
                return 'desktop';
            }
        }
    }

    public function getActiveLanguage()
    {
        $lang = Cookie::get('lang');
        if ($lang) {
            return $lang;
        } else {
            return Self::getValByName('default_language');
        }
    }

    public function cacheSize()
    {
        $fileSize = 0;
        foreach (File::allFiles(base_path() . '/storage/framework') as $file) {
            $fileSize += $file->getSize();
        }
        $fileSize = number_format($fileSize / 1000000, 4);
        return $fileSize;
    }
}
