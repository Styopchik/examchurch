<?php

namespace App\Http\Controllers;

use App\DataTables\RequestuserDataTable;
use App\Facades\UtilityFacades;
use App\Mail\ConatctMail;
use App\Mail\DisapprovedMail;
use App\Models\Coupon;
use App\Models\Faq;
use App\Models\NotificationsSetting;
use App\Models\Order;
use App\Models\Plan;
use App\Models\RequestUser;
use App\Models\User;
use App\Models\UserCoupon;
use App\Notifications\NewEnquiryDetails;
use Carbon\Carbon;
use CoinGate\CoinGate;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Paytabscom\Laravel_paytabs\Facades\paypage;
use Paytm\JsCheckout\Facades\Paytm;
use Stripe\Stripe;
use Spatie\MailTemplates\Models\MailTemplate;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class RequestuserController extends Controller
{
    public function index(RequestuserDataTable $dataTable)
    {
        if (\Auth::user()->hasrole('Super Admin')) {
            return $dataTable->render('requestuser.index');
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function create($data)
    {
        try {
            $lang   = UtilityFacades::getActiveLanguage();
            \App::setLocale($lang);
            $datas  = Crypt::decrypt($data);
            $planId = $datas['plan_id'];
        } catch (DecryptException $e) {
            return redirect()->back()->with('failed', $e->getMessage());
        }
        return view('requestuser.create', compact('planId', 'data', 'lang'));
    }

    public function store(Request $request)
    {
        if (UtilityFacades::getsettings('login_recaptcha_status') == 1) {
            request()->validate([
                'g-recaptcha-response' => 'required',
            ]);
        }
        request()->validate([
            'name'          => 'required|string|max:191',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|same:password_confirmation',
            'phone'         => 'required|string|unique:users,phone',
            'country_code'  => 'required|string',
            'agree'         => 'required|accepted',
        ]);
        $countries                      = \App\Core\Data::getCountriesList();
        $countryCode                    = $countries[$request->country_code]['phone_code'];
        $requestUser                    = new RequestUser();
        $requestUser->name              = $request->name;
        $requestUser->email             = $request->email;
        $requestUser->password          = Hash::make($request->password);
        $requestUser->country_code      = $countryCode;
        $requestUser->phone             = $request->phone;
        $requestUser->plan_id           = $request->plan_id;
        $requestUser->type              = 'Admin';
        $requestUser->save();
        $plan                           = Plan::find($request->plan_id);
        $paymentType                    = '';
        $paymentStatus                  = 0;
        $order = Order::create([
            'plan_id'           => $request->plan_id,
            'amount'            => $plan->price,
            'request_user_id'   => $requestUser->id,
            'paymet_type'       => $paymentType,
            'status'            => $paymentStatus,
        ]);
        $input                  = $request->all();
        if ($request->plan_id != 1) {
            return redirect()->route('requestuser.payment', $order->id);
        } else {
            if (UtilityFacades::getsettings('approve_type') == 'Auto') {
                UtilityFacades::approvedRequest($requestUser, $input);
            }
            return redirect()->route('landingpage')->with('status', __('Thanks for registration, your account is in review and you get email when your account active.'));
        }
    }

    public function payment(Request $request, $id)
    {
        $lang                       = UtilityFacades::getActiveLanguage();
        \App::setLocale($lang);
        $order                      = Order::find($id);
        $requestUser                = RequestUser::find($order->request_user_id);
        $plan                       = Plan::find($requestUser->plan_id);
        $paymentTypes               = UtilityFacades::getpaymenttypes();
        $adminPaymentSetting        = UtilityFacades::getAdminPaymentSettings();
        return view('requestuser.front-payment', compact('requestUser', 'adminPaymentSetting', 'paymentTypes', 'order', 'plan', 'lang'));
    }

    public function disapprove(Request $request, $id)
    {
        request()->validate([
            'disapprove_reason'         => 'required|string',
        ]);
        $requestUser                    = RequestUser::find($id);
        $requestUser->disapprove_reason = $request->disapprove_reason;
        $requestUser->is_approved       = 2;
        $requestUser->update();
        if (UtilityFacades::getsettings('email_setting_enable') == 'on') {
            if (MailTemplate::where('mailable', DisapprovedMail::class)->first()) {
                try {
                    Mail::to($requestUser->email)->send(new DisapprovedMail($requestUser));
                } catch (\Exception $e) {
                    return redirect()->back()->with('errors', $e->getMessage());
                }
                return redirect()->back()->with('success', __('Domain request disapprove successfully.'));
            }
        }
        return redirect()->back()->with('success', __('Domain request disapprove successfully.'));
    }

    public function disapproveStatus($id)
    {
        $requestUser    = RequestUser::find($id);
        if ($requestUser->is_approved == 0) {
            $view       = view('requestuser.reason', compact('requestUser'));
            return ['html' => $view->render()];
        } else {
            return redirect()->back();
        }
    }

    public function approveStatus($id)
    {
        $requestUser    = RequestUser::find($id);
        if ($requestUser->is_approved == 0) {
            return view('requestuser.edit', compact('requestUser'));
        } else {
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        $requestUser    = RequestUser::find($id);
        return view('requestuser.user-edit', compact('requestUser'));
    }

    public function dataUpdate(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'name'      => 'required|string|max:191',
                'email'     => 'required|email|unique:users,email,' . $id,
                'password'  => 'required|string|confirmed',
                'phone'     => 'required|string|unique:users,phone,' . $id,
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('errors', $messages->first());
        }
        $requestUser                    = RequestUser::find($id);
        $requestUser['name']            = $request->name;
        $requestUser['email']           = $request->email;
        if (!empty($request->password)) {
            $requestUser->password      = Hash::make($request->password);
        }
        $requestUser['country_code']    = $request->country_code;
        $requestUser['phone']           = $request->phone;
        $requestUser->update();
        return redirect()->route('requestuser.index')->with('success', __('Admin request updated successfully.'));
    }

    public function update(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'name'      => 'required|string|max:191',
                'email'     => 'required|email|unique:users,email,',
                'phone'     => 'required|string|unique:users,phone,',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('errors', $messages->first());
        }
        $requestUser = RequestUser::where('email', $request->email)->first();
        UtilityFacades::approvedRequest($requestUser->id);
        return redirect()->route('requestuser.index')->with('success', __('User register successfully'));
    }

    public function destroy($id)
    {
        $requestUser = RequestUser::find($id);
        $requestUser->delete();
        return redirect()->route('requestuser.index')
            ->with('success', 'Requestuser deleted successfully.');
    }

    public function contactus()
    {
        $lang = UtilityFacades::getActiveLanguage();
        \App::setLocale($lang);
        return view('contactus', compact('lang'));
    }

    public function faqs()
    {
        $lang = UtilityFacades::getActiveLanguage();
        \App::setLocale($lang);
        $faqs = Faq::orderBy('order')->get();
        return view('faqs', compact('lang', 'faqs'));
    }

    public function contactMail(Request $request)
    {
        $user           = User::where('type', 'Super Admin')->first();
        $notify         = NotificationsSetting::where('title', 'new enquiry details')->first();
        if (UtilityFacades::getsettings('email_setting_enable') == 'on') {
            if (isset($notify)) {
                if ($notify->notify = '1') {
                    $user->notify(new NewEnquiryDetails($request));
                }
            }
        }
        if (UtilityFacades::getsettings('contact_us_recaptcha_status') == '1') {
            request()->validate([
                'g-recaptcha-response' => 'required',
            ]);
        }
        if (UtilityFacades::getsettings('email_setting_enable') == 'on' && UtilityFacades::getsettings('contact_email') != '') {
            if (MailTemplate::where('mailable', ConatctMail::class)->first()) {
                try {
                    if ($request) {
                        Mail::to(UtilityFacades::getsettings('contact_email'))->send(new ConatctMail($request->all()));
                    } else {
                        return redirect()->back()->with('failed', __('Please check recaptch.'));
                    }
                } catch (\Exception $e) {
                    return redirect()->back()->with('errors', $e->getMessage());
                }
                return redirect()->back()->with('success', __('Email sent successfully.'));
            }
        }
        return redirect()->back()->with('success', __('enquiry details send successfully'));
    }

    //stripe user request
    public function stripePostPending(Request $request)
    {
        $planID             = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan               = Plan::find($planID);
        $order              = Order::find($request->order_id);
        $requestUser        = RequestUser::find($order->request_user_id);
        $coupon_id          = '0';
        $price              = $plan->price;
        $couponCode         = null;
        $discountValue      = null;
        $coupons            = Coupon::where('code', $request->coupon)->where('is_active', '1')->first();
        if ($coupons) {
            $couponCode     = $coupons->code;
            $usedCoupun     = $coupons->usedCoupon();
            if ($coupons->limit == $usedCoupun) {
                $resData['errors'] = 'This coupon code has expired.';
            } else {
                $discount       = $coupons->discount;
                $discountType   = $coupons->discount_type;
                $discountValue  = UtilityFacades::calculateDiscount($price, $discount, $discountType);
                $price          = $price - $discountValue;
                if ($price < 0) {
                    $price      = $plan->price;
                }
                $coupon_id      = $coupons->id;
            }
        }
        $order->plan_id               = $plan->id;
        $order->request_user_id       = $requestUser->id;
        $order->amount                = $price;
        $order->discount_amount       = $discountValue;
        $order->coupon_code           = $couponCode;
        $order->status                = 0;
        $order->save();
        $resData['total_price']      = $price;
        $resData['request_user_id']  = $requestUser->id;
        $resData['plan_id']          = $plan->id;
        $resData['coupon']           = $coupon_id;
        $resData['order_id']         = $order->id;
        return $resData;
    }

    public function preStripeSession(Request $request)
    {
        Stripe::setApiKey(UtilityFacades::keysettings('stripe_secret', 1));
        $currency = env('CURRENCY');
        if (!empty($request->createCheckoutSession)) {
            $plan = Plan::find($request->plan_id);
            // Create new Checkout Session for the order
            try {
                $checkoutSession = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price_data' => [
                            'product_data' => [
                                'name' => $plan->name,
                                'metadata' => [
                                    'plan_id'           => $request->plan_id,
                                    'request_user_id'   => $request->request_user_id
                                ]
                            ],
                            'unit_amount'               => $request->amount * 100,
                            'currency'                  => $currency,
                        ],
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'success_url' => route('pre.stripe.success.pay', Crypt::encrypt(['coupon' => $request->coupon, 'plan_id' => $plan->id, 'price' => $request->amount, 'request_user_id' => $request->request_user_id, 'order_id' => $request->order_id, 'type' => 'stripe'])),
                    'cancel_url' => route('pre.stripe.cancel.pay', Crypt::encrypt(['coupon' => $request->coupon, 'plan_id' => $plan->id, 'price' => $request->amount, 'request_user_id' => $request->request_user_id, 'order_id' => $request->order_id, 'type' => 'stripe'])),
                ]);
            } catch (Exception $e) {
                $apiError = $e->getMessage();
            }
            if (empty($apiError) && $checkoutSession) {
                $response = array(
                    'status'    => 1,
                    'message'   => __('Checkout session created successfully.'),
                    'sessionId' => $checkoutSession->id
                );
            } else {
                $response = array(
                    'status' => 0,
                    'error'  => array(
                        'message' => 'Checkout session creation failed. ' . $apiError
                    )
                );
            }
        }
        echo json_encode($response);
        die;
    }

    function prePaymentCancel($data)
    {
        $data               = Crypt::decrypt($data);
        $order              = Order::find($data['order_id']);
        $order->status      = 2;
        $order->paymet_type = 'stripe';
        $order->save();
        return redirect()->route('landingpage')->with('errors', __('Payment canceled.'));
    }

    function prePaymentSuccess($data)
    {
        $data               = Crypt::decrypt($data);
        $database           = $data;
        $order              = Order::find($data['order_id']);
        $order->status      = 1;
        $order->paymet_type = 'stripe';
        $order->update();
        $coupons            = Coupon::find($data['coupon']);
        if (!empty($coupons)) {
            $userCoupon                 = new UserCoupon();
            $userCoupon->userrequest    = $data['request_user_id'];
            $userCoupon->coupon         = $coupons->id;
            $userCoupon->order          = $order->id;
            $userCoupon->save();
            $usedCoupun = $coupons->usedCoupon();
            if ($coupons->limit <= $usedCoupun) {
                $coupons->is_active     = 0;
                $coupons->save();
            }
        }
        if (UtilityFacades::getsettings('approve_type') == 'Auto') {
            UtilityFacades::approvedRequest($data['request_user_id'], $database);
        }
        return redirect()->route('landingpage')->with('status', __('Thanks for registration, your account is in review and you get email when your account active.'));
    }

    // razorpay user request
    public function paysRazorPayPayment(Request $request)
    {
        $planID             = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan               = Plan::find($planID);
        $order              = Order::find($request->order_id);
        $requestUser        = RequestUser::find($order->request_user_id);
        $couponId           = 0;
        $price              = $plan->price;
        $coupons            = Coupon::where('code', $request->coupon)->where('is_active', '1')->first();
        $couponCode         = null;
        $discountValue      = null;
        if ($coupons) {
            $couponCode     = $coupons->code;
            $usedCoupun     = $coupons->usedCoupon();
            if ($coupons->limit == $usedCoupun) {
                $resData['errors'] = 'This coupon code has expired.';
            } else {
                $discount       = $coupons->discount;
                $discountYype   = $coupons->discount_type;
                $discountValue  = UtilityFacades::calculateDiscount($price, $discount, $discountYype);
                $price          = $price - $discountValue;
                if ($price < 0) {
                    $price      = $plan->price;
                }
                $couponId       = $coupons->id;
            }
        }
        $order->plan_id                = $plan->id;
        $order->request_user_id        = $requestUser->id;
        $order->amount                 = $price;
        $order->discount_amount        = $discountValue;
        $order->coupon_code            = $couponCode;
        $order->status                 = 0;
        $order->save();
        $resData['email']              = $requestUser->email;
        $resData['currency']           = env('CURRENCY');
        $resData['request_user_id']    = $requestUser->id;
        $resData['order_id']           = $order->id;
        $resData['total_price']        = $price;
        $resData['coupon']             = $couponId;
        $resData['plan_name']          = $plan->name;
        $resData['plan_id']            = $plan->id;
        return $resData;
    }

    public function paysRazorPayCallback($order_id, $transaction_id, $requestuser_id, $coupon_id)
    {
        $order                          = Order::find($order_id);
        $order->status                  = 1;
        $order->payment_id              = $transaction_id;
        $order->paymet_type             = 'razorpay';
        $order->update();
        $coupons                        = Coupon::find($coupon_id);
        $requestuser                    =  RequestUser::find($requestuser_id);
        if (!empty($coupons)) {
            $userCoupon                 = new UserCoupon();
            $userCoupon->userrequest    = $requestuser->id;
            $userCoupon->coupon         = $coupons->id;
            $userCoupon->order          = $order->id;
            $userCoupon->save();
            $usedCoupun                 = $coupons->usedCoupon();
            if ($coupons->limit <= $usedCoupun) {
                $coupons->is_active     = 0;
                $coupons->save();
            }
        }
        if (UtilityFacades::getsettings('approve_type') == 'Auto') {
            UtilityFacades::approvedRequest($order);
        }
        return redirect()->route('landingpage')->with('status', 'Thanks for registration, your account is in review and you get email when your account active.');
    }

    // paypal request user
    public function processTransaction(Request $request)
    {
        $currency       = env('CURRENCY');
        $planID         = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan           = Plan::find($planID);
        $order          = Order::find($request->order_id);
        $requestUser    = RequestUser::find($order->request_user_id);
        $couponId       = '0';
        $price          = $plan->price;
        $couponCode     = null;
        $discountValue  = null;
        $coupons        = Coupon::where('code', $request->coupon)->where('is_active', '1')->first();
        if ($coupons) {
            $couponCode = $coupons->code;
            $usedCoupun = $coupons->usedCoupon();
            if ($coupons->limit == $usedCoupun) {
                $resData['errors'] = 'This coupon code has expired.';
            } else {
                $discount       = $coupons->discount;
                $discountType   = $coupons->discount_type;
                $discountValue  = UtilityFacades::calculateDiscount($price, $discount, $discountType);
                $price          = $price - $discountValue;
                if ($price < 0) {
                    $price      = $plan->price;
                }
                $couponId       = $coupons->id;
            }
        }
        $order->plan_id         = $plan->id;
        $order->request_user_id = $requestUser->id;
        $order->amount          = $price;
        $order->discount_amount = $discountValue;
        $order->coupon_code     = $couponCode;
        $order->status          = 0;
        $order->save();
        $resData['total_price']     = $price;
        $resData['request_user_id'] = $requestUser->id;
        $resData['coupon']          = $couponId;
        $resData['order_id']        = $order->id;
        $provider                   = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                'return_url' => route('successTransaction', Crypt::encrypt(['coupon' => $resData['coupon'], 'product_name' => $plan->name, 'price' => $resData['total_price'], 'user_id' => $resData['request_user_id'], 'currency' => $plan->currency, 'product_id' => $plan->id, 'order_id' => $resData['order_id']])),
                'cancel_url' => route('cancelTransaction', Crypt::encrypt(['coupon' => $resData['coupon'], 'product_name' => $plan->name, 'price' => $resData['total_price'], 'user_id' => $resData['request_user_id'], 'currency' => $plan->currency, 'product_id' => $plan->id, 'order_id' => $resData['order_id']])),
            ],
            "purchase_units" => [
                0 => [
                    "amount" => [
                        "currency_code" => $currency,
                        "value"         => $resData['total_price'],
                    ]
                ]
            ]
        ]);
        if (isset($response['id']) && $response['id'] != null) {    // redirect to approve href
            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    return redirect()->away($links['href']);
                }
            }
            return redirect()->back()->with('failed',  __('Something wents wrong.'));
        } else {
            return redirect()->back()->with('failed',  __('Something wents wrong.'));
        }
    }

    public function cancelTransaction($data)
    {
        $data                   = Crypt::decrypt($data);
        $order                  = Order::find($data['order_id']);
        $order->status          = 2;
        $order->paymet_type     = 'paypal';
        $order->update();
        return redirect()->route('landingpage')->with('failed', __('Payment canceled.'));
    }

    public function successTransaction($data, Request $request)
    {
        $data                   = Crypt::decrypt($data);
        $database               = $data;
        $order                  = Order::find($data['order_id']);
        $order->payment_id      = $request['PayerID'];
        $order->status          = 1;
        $order->paymet_type     = 'paypal';
        $order->update();
        $coupons                = Coupon::find($data['coupon']);
        if (!empty($coupons)) {
            $userCoupon                 = new UserCoupon();
            $userCoupon->userrequest    = $order->request_user_id;
            $userCoupon->coupon         = $coupons->id;
            $userCoupon->order          = $order->id;
            $userCoupon->save();
            $usedCoupun                 = $coupons->usedCoupon();
            if ($coupons->limit <= $usedCoupun) {
                $coupons->is_active     = 0;
                $coupons->save();
            }
        }
        if (UtilityFacades::getsettings('database_permission') == '1') {
            UtilityFacades::approvedRequest($data['request_user_id'], $database);
        }
        return redirect()->route('landingpage')->with('status', 'Thanks for registration, your account is in review and you get email when your account active.');
    }

    // flutterwave user request
    public function paysFlutterwavePayment(Request $request)
    {
        $planID             = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $order              = Order::find($request->order_id);
        $requestUser        = RequestUser::find($order->request_user_id);
        $plan               =  Plan::find($planID);
        $couponId           = 0;
        $couponCode         = null;
        $discountValue      = null;
        $price              = $plan->price;
        $coupons            = Coupon::where('code', $request->coupon)->where('is_active', '1')->first();
        if ($coupons) {
            $couponCode     = $coupons->code;
            $usedCoupun     = $coupons->usedCoupon();
            if ($coupons->limit == $usedCoupun) {
                $res_data['errors'] = 'This coupon code has expired.';
            } else {
                $discount       = $coupons->discount;
                $discountType   = $coupons->discount_type;
                $discountValue  = UtilityFacades::calculateDiscount($price, $discount, $discountType);
                $price          = $price - $discountValue;
                if ($price < 0) {
                    $price      = $plan->price;
                }
                $couponId       = $coupons->id;
            }
        }
        $order->plan_id                 = $plan->id;
        $order->request_user_id         = $requestUser->id;
        $order->amount                  = $price;
        $order->discount_amount         = $discountValue;
        $order->coupon_code             = $couponCode;
        $order->status                  = 0;
        $order->save();
        $resData['email']               = $requestUser->email;
        $resData['currency']            = env('CURRENCY');
        $resData['request_user_id']     = $requestUser->id;
        $resData['order_id']            = $order->id;
        $resData['total_price']         = $price;
        $resData['coupon']              = $couponId;
        $resData['plan_name']           = $plan->name;
        $resData['plan_id']             = $plan->id;
        return $resData;
    }

    public function paysFlutterwaveCallback($order_id, $transaction_id, $requestuser_id, $coupon_id)
    {
        $order                          = Order::find($order_id);
        $order->status                  = 1;
        $order->payment_id              = $transaction_id;
        $order->paymet_type             = 'flutterwave';
        $order->update();
        $coupons                        = Coupon::find($coupon_id);
        $requestUser                    = RequestUser::find($requestuser_id);
        if (!empty($coupons)) {
            $userCoupon                 = new UserCoupon();
            $userCoupon->userrequest    = $requestUser->id;
            $userCoupon->coupon         = $coupons->id;
            $userCoupon->order          = $order->id;
            $userCoupon->save();
            $usedCoupun                 = $coupons->usedCoupon();
            if ($coupons->limit <= $usedCoupun) {
                $coupons->is_active     = 0;
                $coupons->save();
            }
        }
        if (UtilityFacades::getsettings('approve_type') == 'Auto') {
            UtilityFacades::approvedRequest($order);
        }
        return redirect()->route('landingpage')->with('status', __('Thanks for registration, your account is in review and you get email when your account active.'));
    }

    // paystack user request
    public function paymentPaystackPayment(Request $request)
    {
        $planID                     = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $order                      = Order::find($request->order_id);
        $requestUser                = RequestUser::find($order->request_user_id);
        $plan                       = Plan::find($planID);
        $couponId                   = 0;
        $couponCode                 = null;
        $discountValue              = null;
        $price                      = $plan->price;
        $coupons                    = Coupon::where('code', $request->coupon)->where('is_active', '1')->first();
        if ($coupons) {
            $couponCode             = $coupons->code;
            $usedCoupun             = $coupons->usedCoupon();
            if ($coupons->limit == $usedCoupun) {
                $resData['errors']  = 'This coupon code has expired.';
            } else {
                $discount           = $coupons->discount;
                $discountType       = $coupons->discount_type;
                $discountValue      = UtilityFacades::calculateDiscount($price, $discount, $discountType);
                $price          = $price - $discountValue;
                if ($price < 0) {
                    $price          = $plan->price;
                }
                $couponId           = $coupons->id;
            }
        }
        $order->plan_id                = $plan->id;
        $order->request_user_id        = $requestUser->id;
        $order->amount                 = $price;
        $order->discount_amount        = $discountValue;
        $order->coupon_code            = $couponCode;
        $order->status                 = 0;
        $order->save();
        $resData['email']              = $requestUser->email;
        $resData['request_user_id']    = $requestUser->id;
        $resData['order_id']           = $order->id;
        $resData['total_price']        = $price;
        $resData['currency']           = UtilityFacades::keysettings('paystack_currency', 1);
        $resData['coupon']             = $couponId;
        $resData['plan_id']            = $plan->id;
        return $resData;
    }

    public function paymentPaystackCallback(Request $request, $order_id, $transaction_id, $requestuser_id, $coupon_id)
    {
        $order                          = Order::find($order_id);
        $order->status                  = 1;
        $order->payment_id              = $transaction_id;
        $order->paymet_type             = 'paystack';
        $order->update();
        $coupons                        = Coupon::find($coupon_id);
        $requestUser                    = RequestUser::find($requestuser_id);
        if (!empty($coupons)) {
            $userCoupon                 = new UserCoupon();
            $userCoupon->userrequest    = $requestUser->id;
            $userCoupon->coupon         = $coupons->id;
            $userCoupon->order          = $order->id;
            $userCoupon->save();
            $usedCoupun                 = $coupons->usedCoupon();
            if ($coupons->limit <= $usedCoupun) {
                $coupons->is_active     = 0;
                $coupons->save();
            }
        }
        if (UtilityFacades::getsettings('approve_type') == 'Auto') {
            UtilityFacades::approvedRequest($order);
        }
        return redirect()->route('landingpage')->with('status', 'Thanks for registration, your account is in review and you get email when your account active.');
    }

    //paytm user request
    public function pay(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'mobile_number'     => 'required|numeric|digits:10',
            ]
        );
        if ($validator->fails()) {
            $messages               = $validator->getMessageBag();
            $errors['errors']       = $messages->first();
            return $errors;
        }
        $payment                    = Paytm::with('receive');
        $planID                     = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan                       = Plan::find($planID);
        $order                      = Order::find($request->order_id);
        $requestUser                = RequestUser::find($order->request_user_id);
        $couponId                   = '0';
        $couponCode                 = null;
        $discountValue              = null;
        $price                      = $plan->price;
        $coupons                    = Coupon::where('code', $request->coupon)->where('is_active', '1')->first();
        if ($coupons) {
            $couponCode             = $coupons->code;
            $usedCoupun             = $coupons->usedCoupon();
            if ($coupons->limit == $usedCoupun) {
                $resData['errors']  = 'This coupon code has expired.';
            } else {
                $discount = $coupons->discount;
                $discount_type      = $coupons->discount_type;
                $discountValue      = UtilityFacades::calculateDiscount($price, $discount, $discount_type);
                $price              = $price - $discountValue;
                if ($price < 0) {
                    $price          = $plan->price;
                }
                $couponId           = $coupons->id;
            }
        }
        $order->plan_id             = $plan->id;
        $order->request_user_id     = $requestUser->id;
        $order->amount              = $price;
        $order->discount_amount     = $discountValue;
        $order->coupon_code         = $couponCode;
        $order->status              = 0;
        $order->save();
        $resData['user']            = $requestUser->id;
        $resData['email']           = $requestUser->email;
        $resData['total_price']     = $price;
        $resData['coupon']          = $couponId;
        $resData['order_id']        = $order->id;
        $payment->prepare([
            'order' => rand(),
            'user' => $resData['user'],
            'mobile_number' => $request->mobile_number,
            'email' => $resData['email'],
            'amount' =>  $resData['total_price'], // amount will be paid in INR.
            'callback_url' => route('paytm.callback', ['coupon' => $resData['coupon'], 'order_id' => $resData['order_id'], 'request_user_id' => $resData['user']]) // callback URL
        ]);
        $response =  $payment->receive();  // initiate a new payment
        return $response;
    }

    public function paymentCallback(Request $request)
    {
        $transaction        = Paytm::with('receive');
        $orderId            = $request->order_id; // return a order id
        $order              = Order::find($orderId);
        $order->status      = 1;
        $order->payment_id  = $transaction->getTransactionId();
        $order->paymet_type = 'paytm';
        $order->update();
        $coupons = Coupon::find($request->coupon);
        if (!empty($coupons)) {
            $userCoupon                 = new UserCoupon();
            $userCoupon->userrequest    = $order->request_user_id;
            $userCoupon->coupon         = $coupons->id;
            $userCoupon->order          = $orderId;
            $userCoupon->save();
            $usedCoupun = $coupons->usedCoupon();
            if ($coupons->limit <= $usedCoupun) {
                $coupons->is_active     = 0;
                $coupons->save();
            }
        }
        if (UtilityFacades::getsettings('approve_type') == 'Auto') {
            UtilityFacades::approvedRequest($order);
        }
        return redirect()->route('landingpage')->with('status', 'Thanks for registration, your account is in review and you get email when your account active.');
    }

    // coingate user request
    public function coingatePayment(Request $request)
    {
        $planID         = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan           = Plan::find($planID);
        $order          = Order::find($request->order_id);
        $requestUser    = RequestUser::find($order->request_user_id);
        $couponId       = '0';
        $couponCode     = null;
        $discountValue  = null;
        $price          = $plan->price;
        $coupons        = Coupon::where('code', $request->coupon)->where('is_active', '1')->first();
        if ($coupons) {
            $couponCode = $coupons->code;
            $usedCoupun = $coupons->usedCoupon();
            if ($coupons->limit == $usedCoupun) {
                $resData['errors'] = 'This coupon code has expired.';
            } else {
                $discount       = $coupons->discount;
                $discountType   = $coupons->discount_type;
                $discountValue  = UtilityFacades::calculateDiscount($price, $discount, $discountType);
                $price          = $price - $discountValue;
                if ($price < 0) {
                    $price      = $plan->price;
                }
                $couponId       = $coupons->id;
            }
        }
        $order->plan_id                 = $plan->id;
        $order->request_user_id         = $requestUser->id;
        $order->amount                  = $price;
        $order->discount_amount         = $discountValue;
        $order->coupon_code             = $couponCode;
        $order->status                  = 0;
        $order->save();
        $resData['total_price']         = $price;
        $resData['request_user_id']     = $requestUser->id;
        $resData['coupon']              = $couponId;
        $resData['order_id']            = $order->id;
        CoinGate::config(
            array(
                'environment'               => UtilityFacades::keysettings('coingate_environment', 1),   // sandbox OR live
                'auth_token'                => UtilityFacades::keysettings('coingate_auth_token', 1),
                'curlopt_ssl_verifypeer'    => FALSE    // default is false
            )
        );
        $currency                   = env('CURRENCY');
        $params = array(
            'order_id'              => rand(),
            'price_amount'          => $resData['total_price'],
            'price_currency'        => $currency,
            'receive_currency'      => $currency,
            'callback_url'          => route('coingatecallback', Crypt::encrypt(['order_id' => $resData['order_id'], 'coupon_id' => $resData['coupon'], 'request_user_id' => $resData['request_user_id']])),
            'cancel_url'            => route('coingatecallback', Crypt::encrypt(['order_id' => $resData['order_id'], 'coupon_id' => $resData['coupon'], 'request_user_id' => $resData['request_user_id'], 'status' => 'failed'])),
            'success_url'           => route('coingatecallback', Crypt::encrypt(['order_id' => $resData['order_id'], 'coupon_id' => $resData['coupon'], 'request_user_id' => $resData['request_user_id'], 'status' => 'successfull'])),
        );
        $order = \CoinGate\Merchant\Order::create($params);
        if ($order) {
            $paymentID              = Order::find($resData['order_id']);
            $paymentID->payment_id  = $order->id;
            $paymentID->update();
            return redirect($order->payment_url);
        } else {
            return redirect()->back()->with('errors', __('Opps something wents wrong.'));
        }
    }

    public function coingatePlanGetPayment(Request $request, $data)
    {
        $data = Crypt::decrypt($data);
        if ($data['status'] == 'successfull') {
            $order                          = Order::find($data['order_id']);
            $order->status                  = 1;
            $order->paymet_type             = 'coingate';
            $order->update();
            $coupons                        = Coupon::find($data['coupon_id']);
            if (!empty($coupons)) {
                $userCoupon                 = new UserCoupon();
                $userCoupon->userrequest    = $data['request_user_id'];
                $userCoupon->coupon         = $coupons->id;
                $userCoupon->order          = $order->id;
                $userCoupon->save();
                $usedCoupun = $coupons->usedCoupon();
                if ($coupons->limit <= $usedCoupun) {
                    $coupons->is_active     = 0;
                    $coupons->save();
                }
            }
            return redirect()->route('landingpage')->with('status', 'Thanks for registration, your account is in review and you get email when your account active.');
        } else {
            $order                          = Order::find($data['order_id']);
            $order->status                  = 2;
            $order->paymet_type             = 'coingate';
            $order->update();
            $coupons                        = Coupon::find($data['coupon_id']);
            if (!empty($coupons)) {
                $userCoupon                 = new UserCoupon();
                $userCoupon->user           = $data['request_user_id'];
                $userCoupon->coupon         = $coupons->id;
                $userCoupon->order          = $order->id;
                $userCoupon->save();
                $usedCoupun = $coupons->usedCoupon();
                if ($coupons->limit <= $usedCoupun) {
                    $coupons->is_active     = 0;
                    $coupons->save();
                }
            }
            return redirect()->route('landingpage')->with('failed', __('Payment canceled.'));
        }
    }

    //offline user request
    public function offlinePaymentEntry(Request $request)
    {
        $planID                         = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $order                          = Order::find($request->order_id);
        $requestUser                    = RequestUser::find($order->request_user_id);
        $plan                           = Plan::find($planID);
        $couponCode                     = null;
        $discountValue                  = null;
        $price                          = $plan->price;
        $coupons                        = Coupon::where('code', $request->coupon)->where('is_active', '1')->first();
        if ($coupons) {
            $couponCode                 = $coupons->code;
            $usedCoupun                 = $coupons->usedCoupon();
            if ($coupons->limit == $usedCoupun) {
                $res_data['errors']     = 'This coupon code has expired.';
            } else {
                $discount               = $coupons->discount;
                $discountType           = $coupons->discount_type;
                $discountValue          = UtilityFacades::calculateDiscount($price, $discount, $discountType);
                $price                  = $price - $discountValue;
                if ($price < 0) {
                    $price              = $plan->price;
                }
            }
        }
        $order->plan_id                 = $plan->id;
        $order->request_user_id         = $requestUser->id;
        $order->amount                  = $price;
        $order->paymet_type             = 'offline';
        $order->discount_amount         = $discountValue;
        $order->coupon_code             = $couponCode;
        $order->status                  = 3;
        $order->save();
        return redirect()->route('landingpage')->with('status', 'Thanks for registration, your account is in review and you get email when your account active.');
    }

    //mercado front
    public function mercadoPagoPayment(Request $request)
    {
        $planID                         = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan                           = Plan::find($planID);
        $order                          = Order::find($request->order_id);
        $requestuser                    = RequestUser::find($order->request_user_id);
        $couponId                       = '0';
        $couponCode                     = null;
        $discountValue                  = null;
        $price                          = $plan->price;
        $coupons                        = Coupon::where('code', $request->coupon)->where('is_active', '1')->first();
        if ($coupons) {
            $couponCode                 = $coupons->code;
            $usedCoupun                 = $coupons->usedCoupon();
            if ($coupons->limit == $usedCoupun) {
                $resData['errors']      = 'This coupon code has expired.';
            } else {
                $discount = $coupons->discount;
                $discountType           = $coupons->discount_type;
                $discountValue          = UtilityFacades::calculateDiscount($price, $discount, $discountType);
                $price                  = $price - $discountValue;
                if ($price < 0) {
                    $price              = $plan->price;
                }
                $couponId               = $coupons->id;
            }
        }
        $order->plan_id                 = $plan->id;
        $order->request_user_id         = $requestuser->id;
        $order->amount                  = $price;
        $order->discount_amount         = $discountValue;
        $order->coupon_code             = $couponCode;
        $order->status                  = 0;
        $order->save();
        $resData['request_user_id']     = $requestuser->id;
        $resData['total_price']         = $price;
        $resData['coupon']              = $couponId;
        $resData['order_id']            = $order->id;
        $mercadoAccessToken             = UtilityFacades::getsettings('mercado_access_token');
        \MercadoPago\SDK::setAccessToken($mercadoAccessToken);
        try {
            // Create a preference object
            $preference                 = new \MercadoPago\Preference();
            // Create an item in the preference
            $item                       = new \MercadoPago\Item();
            $item->title                = "Plan : " . $plan->name;
            $item->quantity             = 1;
            $item->unit_price           = $resData['total_price'];
            $preference->items          = array($item);
            $successUrl                 = route('mercado.callback', [Crypt::encrypt(['order_id' => $resData['order_id'], 'request_user_id' => $resData['request_user_id'], 'coupon' => $resData['coupon'], 'flag' => 'success'])]);
            $failureUrl                 = route('mercado.callback', [Crypt::encrypt(['order_id' => $resData['order_id'], 'request_user_id' => $resData['request_user_id'], 'coupon' => $resData['coupon'], 'flag' => 'failure'])]);
            $pendingUrl                 = route('mercado.callback', [Crypt::encrypt(['order_id' => $resData['order_id'], 'request_user_id' => $resData['request_user_id'], 'coupon' => $resData['coupon'], 'flag' => 'pending'])]);
            $preference->back_urls = array(
                "success"               => $successUrl,
                "failure"               => $failureUrl,
                "pending"               => $pendingUrl,
            );
            $preference->auto_return    = "approved";
            $preference->save();
            if (UtilityFacades::getsettings('mercado_mode') == 'live') {
                $redirectUrl            = $preference->init_point;
                return redirect($redirectUrl);
            } else {
                $redirectUrl            = $preference->sandbox_init_point;
                return redirect($redirectUrl);
            }
        } catch (Exception $e) {
            return redirect()->back()->with('failed', __('Something wents wrong.'));
        }
    }

    public function mercadoPagoPaymentCallback(Request $request, $data)
    {
        $data                       = Crypt::decrypt($data);
        if ($data['flag'] == 'success') {
            $order                  = Order::find($data['order_id']);
            $order->status          = 1;
            $order->payment_id      = $request->payment_id;
            $order->paymet_type     = 'mercadopago';
            $order->update();
            $coupons = Coupon::find($data['coupon']);
            if (!empty($coupons)) {
                $userCoupon                 = new UserCoupon();
                $userCoupon->userrequest    = $data['request_user_id'];
                $userCoupon->coupon         = $coupons->id;
                $userCoupon->order          = $order->id;
                $userCoupon->save();
                $usedCoupun = $coupons->usedCoupon();
                if ($coupons->limit <= $usedCoupun) {
                    $coupons->is_active     = 0;
                    $coupons->save();
                }
            }
            if (UtilityFacades::getsettings('approve_type') == 'Auto') {
                UtilityFacades::approvedRequest($order);
            }
            return redirect()->route('landingpage')->with('status', __('Thanks for registration, your account is in review and you get email when your account active.'));
        } else {
            $order                  = Order::find($data['order_id']);
            $order->status          = 2;
            $order->payment_id      = $request->transaction_id;
            $order->paymet_type     = 'mercadopago';
            $order->update();
            return redirect()->back()->with('failed', __('Payment failed.'));
        }
    }

    public function cashfreePayment(Request $request)
    {
        $cashfreeAppId          = UtilityFacades::keysettings('cashfree_app_id', 1);
        $cashfreeSecretKey      = UtilityFacades::keysettings('cashfree_secret_key', 1);
        $cashfreeUrl            = UtilityFacades::keysettings('cashfree_url', 1);
        $planID                 = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan                   = Plan::find($planID);
        $order                  = Order::find($request->order_id);
        $requestUser            = RequestUser::find($order->request_user_id);
        $couponId               = '0';
        $couponCode             = null;
        $discountValue          = null;
        $price                  = $plan->price;
        $coupons                = Coupon::where('code', $request->coupon)->where('is_active', '1')->first();
        if ($coupons) {
            $couponCode         = $coupons->code;
            $usedCoupun         = $coupons->usedCoupon();
            if ($coupons->limit == $usedCoupun) {
                $resData['errors'] = 'This coupon code has expired.';
            } else {
                $discount       = $coupons->discount;
                $discountType   = $coupons->discount_type;
                $discountValue  = UtilityFacades::calculateDiscount($price, $discount, $discountType);
                $price          = $price - $discountValue;
                if ($price < 0) {
                    $price      = $plan->price;
                }
                $couponId       = $coupons->id;
            }
        }
        $order->plan_id             = $plan->id;
        $order->request_user_id     = $requestUser->id;
        $order->amount              = $price;
        $order->discount_amount     = $discountValue;
        $order->coupon_code         = $couponCode;
        $order->status              = 0;
        $order->save();
        $resData['request_user_id'] = $requestUser->id;
        $resData['total_price']     = $price;
        $resData['coupon']          = $couponId;
        $resData['order_id']        = $order->id;
        try {
            $url = $cashfreeUrl;
            $headers = array(
                "Content-Type: application/json",
                "x-api-version: 2022-01-01",
                "x-client-id: " . $cashfreeAppId,
                "x-client-secret: " . $cashfreeSecretKey
            );
            $data = json_encode([
                'order_id'              => 'order_' . rand(1111111111, 9999999999),
                'order_amount'          => $resData['total_price'],
                "order_currency"        => 'INR',
                "order_name"            => $plan->name,
                "customer_details" => [
                    "customer_id"       => 'customer_' . $requestUser->id,
                    "customer_name"     => $requestUser->name,
                    "customer_email"    => $requestUser->email,
                    "customer_phone"    => $requestUser->phone,
                ],
                "order_meta" => [
                    "return_url" => route('cashfree.callback') . '?order_id={order_id}&order_token={order_token}&order=' . Crypt::encrypt($resData['order_id']) . '',
                ]
            ]);
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $resp = curl_exec($curl);
            curl_close($curl);
            return redirect()->to(json_decode($resp)->payment_link);
        } catch (\Exception $e) {
            return redirect()->back()->with('errors', $e->getMessage());
        }
    }

    public function cashfreeCallback(Request $request)
    {
        $orderId            = Crypt::decrypt($request->order);
        $cashfreeAppId      = UtilityFacades::keysettings('cashfree_app_id', 1);
        $cashfreeSecretKey  = UtilityFacades::keysettings('cashfree_secret_key', 1);
        $cashfreeUrl        = UtilityFacades::keysettings('cashfree_url', 1);
        $client             = new \GuzzleHttp\Client();
        $response           = $client->request('GET', $cashfreeUrl . '/' . $request->get('order_id') . '/settlements', [
            'headers' => [
                'accept'            => 'application/json',
                'x-api-version'     => '2022-09-01',
                "x-client-id"       => $cashfreeAppId,
                "x-client-secret"   => $cashfreeSecretKey
            ],
        ]);
        $respons = json_decode($response->getBody());
        if ($respons->order_id && $respons->cf_payment_id != NULL) {
            $response = $client->request('GET', $cashfreeUrl . '/' . $respons->order_id . '/payments/' . $respons->cf_payment_id . '', [
                'headers' => [
                    'accept'            => 'application/json',
                    'x-api-version'     => '2022-09-01',
                    'x-client-id'       => $cashfreeAppId,
                    'x-client-secret'   => $cashfreeSecretKey,
                ],
            ]);
            $info = json_decode($response->getBody());
            if ($info->payment_status == "SUCCESS") {
                $order                          = Order::find($orderId);
                $order->status                  = 1;
                $order->payment_id              = $info->cf_payment_id;
                $order->paymet_type             = 'cashfree';
                $order->update();
                $coupons                        = Coupon::find($order['coupon']);
                if (!empty($coupons)) {
                    $userCoupon                 = new UserCoupon();
                    $userCoupon->userrequest    = $order['request_user_id'];
                    $userCoupon->coupon         = $coupons->id;
                    $userCoupon->order          = $order->id;
                    $userCoupon->save();
                    $usedCoupun                 = $coupons->usedCoupon();
                    if ($coupons->limit <= $usedCoupun) {
                        $coupons->is_active     = 0;
                        $coupons->save();
                    }
                }
                if (UtilityFacades::getsettings('approve_type') == 'Auto') {
                    UtilityFacades::approvedRequest($order);
                }
                return redirect()->route('landingpage')->with('status', __('Thanks for registration, your account is in review and you get email when your account active.'));
            } else {
                $order = Order::find($orderId);
                $order->status = 2;
                $order->payment_id = $info->cf_payment_id;
                $order->paymet_type = 'cashfree';
                $order->update();
                return redirect()->back()->with('failed', __('Payment failed.'));
            }
        }
    }

    public function sspayInitPayment(Request $request)
    {
        $sspayCategoryCode      = UtilityFacades::keysettings('sspay_category_code', 1);
        $sspaySecretKey         = UtilityFacades::keysettings('sspay_secret_key', 1);
        $sspayDescription       = UtilityFacades::keysettings('sspay_description', 1);
        $planID                 = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan                   = Plan::find($planID);
        $order                  = Order::find($request->order_id);
        $requestuser            = RequestUser::find($order->request_user_id);
        $couponId               = '0';
        $couponCode             = null;
        $discountValue          = null;
        $price                  = $plan->price;
        $coupons                = Coupon::where('code', $request->coupon)->where('is_active', '1')->first();
        if ($coupons) {
            $couponCode         = $coupons->code;
            $usedCoupun         = $coupons->usedCoupon();
            if ($coupons->limit == $usedCoupun) {
                $res_data['errors'] = 'This coupon code has expired.';
            } else {
                $discount       = $coupons->discount;
                $discountType   = $coupons->discount_type;
                $discountValue  = UtilityFacades::calculateDiscount($price, $discount, $discountType);
                $price          = $price - $discountValue;
                if ($price < 0) {
                    $price      = $plan->price;
                }
                $couponId       = $coupons->id;
            }
        }
        $order->plan_id         = $plan->id;
        $order->request_user_id = $requestuser->id;
        $order->amount          = $price;
        $order->discount_amount = $discountValue;
        $order->coupon_code     = $couponCode;
        $order->status          = 0;
        $order->save();
        $resData['request_user_id']     = $requestuser->id;
        $resData['total_price']         = $price;
        $resData['coupon']              = $couponId;
        $resData['order_id']            = $order->id;

        try {
            $someData = array(
                'userSecretKey'             => $sspaySecretKey,
                'categoryCode'              => $sspayCategoryCode,
                'billName'                  => $plan->name,
                'billDescription'           => $plan->description,
                'billPriceSetting'          => 1,
                'billPayorInfo'             => 1,
                'billAmount'                => round($resData['total_price'] * 100, 2),
                'billReturnUrl'             => route('sspay.callback') . '?&order=' . $resData['order_id'] . '',
                'billCallbackUrl'           => route('sspay.callback') . '?&order=' . $resData['order_id'] . '',
                'billExternalReferenceNo'   => 'AFR341DFI',
                'billTo'                    => $requestuser->name,
                'billEmail'                 => $requestuser->email,
                'billPhone'                 => $requestuser->phone,
                'billSplitPayment'          => 0,
                'billSplitPaymentArgs'      => '',
                'billPaymentChannel'        => '0',
                'billDisplayMerchant'       => 1,
                'billContentEmail'          => $sspayDescription,
                'billChargeToCustomer'      => 1
            );
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_URL, 'https://sspay.my' . '/index.php/api/createBill');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $someData);
            $result     = curl_exec($curl);
            $info       = curl_getinfo($curl);
            curl_close($curl);
            $obj        = json_decode($result);
            $url        = 'https://sspay.my' . '/index.php/api/runBill';
            $billcode   = $obj[0]->BillCode;
            $someData = array(
                'userSecretKey'             => $sspaySecretKey,
                'billCode'                  => $billcode,
                'billpaymentAmount'         => round($res_data['total_price'] * 100, 2),
                'billpaymentPayorName'      => $requestuser->name,
                'billpaymentPayorPhone'     => $requestuser->phone,
                'billpaymentPayorEmail'     => $requestuser->email,
                'billBankID'                => 'TEST0021'

            );
            $curl       = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $someData);
            $result     = curl_exec($curl);
            curl_getinfo($curl);
            curl_close($curl);
            $obj        = json_decode($result);
            return redirect()->to('https://sspay.my' . '/' . $billcode);
        } catch (\Exception $e) {
            return redirect()->back()->with('errors', $e->getMessage());
        }
    }

    public function sspayCallback(Request $request)
    {
        if ($request->status_id == 1) {
            $order = Order::find($request->order);
            $order->status = 1;
            $order->payment_id = $request->transaction_id;
            $order->paymet_type = 'sspay';
            $order->update();
            $coupons = Coupon::where('code', $request->coupon)->where('is_active', '1')->first();
            if (!empty($coupons)) {
                $userCoupon                     = new UserCoupon();
                $userCoupon->userrequest        = $order->request_user_id;
                $userCoupon->coupon             = $coupons->id;
                $userCoupon->order              = $order->id;
                $userCoupon->save();
                $usedCoupun = $coupons->usedCoupon();
                if ($coupons->limit <= $usedCoupun) {
                    $coupons->is_active         = 0;
                    $coupons->save();
                }
            }
            if (UtilityFacades::getsettings('approve_type') == 'Auto') {
                UtilityFacades::approvedRequest($order);
            }
            return redirect()->route('landingpage')->with('status', 'Thanks for registration, your account is in review and you get email when your account active.');
        } elseif ($request->status_id == 3) {
            $order                              = Order::find($request->order);
            $order->status                      = 2;
            $order->payment_id                  = $request->transaction_id;
            $order->paymet_type                 = 'sspay';
            $order->update();
            return redirect()->route('landingpage')->with('failed', __('Payment failed.'));
        } else {
            $order                              = Order::find($request->order);
            $order->payment_id                  = $request->transaction_id;
            $order->paymet_type                 = 'sspay';
            $order->save();
            return redirect()->route('landingpage')->with('warning', __('Waiting for proccesing..'));
        }
    }

    public function frontPayUmoneyPayment(Request $request)
    {
        $planID             = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $order              = Order::find($request->order_id);
        $requestUser        = RequestUser::find($order->request_user_id);
        $plan               = Plan::find($planID);
        $couponId           = '0';
        $couponCode         = null;
        $discountValue      = null;
        $price              = $plan->price;
        $coupons            = Coupon::where('code', $request->coupon)->where('is_active', '1')->first();
        if ($coupons) {
            $couponCode     = $coupons->code;
            $usedCoupun     = $coupons->usedCoupon();
            if ($coupons->limit == $usedCoupun) {
                $resData['errors'] = 'This coupon code has expired.';
            } else {
                $discount = $coupons->discount;
                $discount_type = $coupons->discount_type;
                $discountValue = UtilityFacades::calculateDiscount($price, $discount, $discount_type);
                $price          = $price - $discountValue;
                if ($price < 0) {
                    $price = $plan->price;
                }
                $couponId = $coupons->id;
            }
        }
        $order = Order::create([
            'plan_id'           => $plan->id,
            'request_user_id'   => $requestUser->id,
            'amount'            => $price,
            'discount_amount'   => $discountValue,
            'coupon_code'       => $couponCode,
            'status'            => 0,
        ]);
        $resData['email']           = $requestUser->email;
        $resData['total_price']     = $price;
        $resData['coupon']          = $couponId;
        $resData['request_user_id'] = $requestUser->id;
        $resData['plan_id']         = $plan->id;
        $resData['order_id']        = $order->id;
        $key                        = UtilityFacades::getsettings('payumoney_merchant_key');
        $txnid                      = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
        $salt                       = UtilityFacades::getsettings('payumoney_salt_key');
        $amount                     = $price;
        $hashString                 = $key . '|' . $txnid . '|' . $amount . '|' . $plan->name . '|' . $requestUser->name . '|' . $requestUser->email . '|' . '||||||||||' . $salt;
        $hash                       = strtolower(hash('sha512', $hashString));
        $payuUrl                    = 'https://test.payu.in/_payment';  // For production environment, change it to 'https://secure.payumoney.com/_payment'
        $paymentData = [
            'key'               => $key,
            'txnid'             => $txnid,
            'amount'            => $resData['total_price'],
            'productinfo'       => $plan->name,
            'firstname'         => $requestUser->name,
            'email'             => $requestUser->email,
            'hash'              => $hash,
            'surl'              => route('front.payu.success', Crypt::encrypt(['key' => $key, 'productinfo' => $plan->name, 'firstname' => $requestUser->name, 'email' => $requestUser->email,  'txnid' => $txnid,  'order_id' => $resData['order_id'], 'request_user_id' => $requestUser->id, 'coupon' => $couponId, 'plan_id' => $plan->id, 'discount_amount' => $discountValue, 'currency' => env('CURRENCY_SYMBOL'), 'payment_type' => 'payumoney', 'status' => 'successfull'])),
            'furl'              => route('front.payu.failure', Crypt::encrypt(['key' => $key, 'productinfo' => $plan->name, 'firstname' => $requestUser->name, 'email' => $requestUser->email,  'txnid' => $txnid, 'order_id' => $resData['order_id'], 'request_user_id' => $requestUser->id, 'coupon' => $couponId, 'plan_id' => $plan->id, 'discount_amount' => $discountValue, 'currency' => env('CURRENCY_SYMBOL'), 'payment_type' => 'payumoney', 'status' => 'failed'])),
        ];
        return view('plans.payumoneyRedirect', compact('payuUrl', 'paymentData'));
    }

    public function frontPayuSuccess(Request $request, $data)
    {
        $data                           = Crypt::decrypt($data);
        if (\Auth::user()->type == 'Admin') {
            $order                      = Order::find($data['order_id']);
            $order->status              = 1;
            $order->request_user_id     = $data['request_user_id'];
            $order->payment_id =        $data['txnid'];
            $order->paymet_type         = 'payumoney';
            $order->update();
            $coupons                    = Coupon::find($data['coupon']);
            $user                       = User::find(\Auth::user()->id);
            if (!empty($coupons)) {
                $userCoupon             = new UserCoupon();
                $userCoupon->user       = $user->id;
                $userCoupon->coupon     = $coupons->id;
                $userCoupon->order      = $order->id;
                $userCoupon->save();
                $usedCoupun             = $coupons->usedCoupon();
                if ($coupons->limit <= $usedCoupun) {
                    $coupons->is_active = 0;
                    $coupons->save();
                }
            }
            $plan                       = Plan::find($data['plan_id']);
            $user->plan_id              = $plan->id;
            if ($plan->durationtype == 'Month' && $plan->id != '1') {
                $user->plan_expired_date = Carbon::now()->addMonths($plan->duration)->isoFormat('YYYY-MM-DD');
            } elseif ($plan->durationtype == 'Year' && $plan->id != '1') {
                $user->plan_expired_date = Carbon::now()->addYears($plan->duration)->isoFormat('YYYY-MM-DD');
            } else {
                $user->plan_expired_date = null;
            }
            $user->save();
        }
        return redirect()->route('plans.index')->with('success', __('Payment successfully.'));
    }


    public function frontPayuFailure($data)
    {
        $data                       = Crypt::decrypt($data);
        $order                      = Order::find($data['order_id']);
        $order->status              = 2;
        $order->paymet_type         = 'payumoney';
        $order->update();
        return redirect()->route('plans.index')->with('success', 'Payment payuFailure');
    }

    public function planPayWithPaytab(Request $request)
    {
        config([
            'paytabs.profile_id'    => UtilityFacades::keysettings('paytab_profile_id', 1),
            'paytabs.server_key'    => UtilityFacades::keysettings('paytab_server_key', 1),
            'paytabs.region'        =>  UtilityFacades::keysettings('paytab_region', 1),
            'paytabs.currency'      => env('CURRENCY'),
        ]);
        $planID             = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan               = Plan::find($planID);
        $order              = Order::find($request->order_id);
        $requestUser        = RequestUser::find($order->request_user_id);
        $couponId           = '0';
        $couponCode         = null;
        $discountValue      = null;
        $price              = $plan->price;
        $coupons            = Coupon::where('code', $request->coupon)->where('is_active', '1')->first();
        if ($coupons) {
            $couponCode     = $coupons->code;
            $usedCoupun     = $coupons->usedCoupon();
            if ($coupons->limit == $usedCoupun) {
                $resData['errors'] = 'This coupon code has expired.';
            } else {
                $discount       = $coupons->discount;
                $discountType   = $coupons->discount_type;
                $discountValue  = UtilityFacades::calculateDiscount($price, $discount, $discountType);
                $price          = $price - $discountValue;
                if ($price < 0) {
                    $price      = $plan->price;
                }
                $couponId       = $coupons->id;
            }
        }
        $order->plan_id         = $plan->id;
        $order->request_user_id = $requestUser->id;
        $order->amount          = $price;
        $order->discount_amount = $discountValue;
        $order->coupon_code     = $couponCode;
        $order->status          = 0;
        $order->save();
        $resData['request_user_id'] = $requestUser->id;
        $resData['total_price']     = $price;
        $resData['coupon']          = $couponId;
        $resData['order_id']        = $order->id;
        $pay = paypage::sendPaymentCode('all')
            ->sendTransaction('sale')
            ->sendCart(1, $price, 'plan payment')
            ->sendCustomerDetails('', '', '', '', '', '', '', '', '')
            ->sendURLs(
                route('plan.paytab.success', ['success' => 1, 'data' => $request->all(), 'plan_id' => $plan->id, 'amount' => $price, 'coupon' => $resData['coupon']]),
                route('plan.paytab.success', ['success' => 0, 'data' => $request->all(), 'plan_id' => $plan->id, 'amount' => $price, 'coupon' => $resData['coupon']])
            )
            ->sendLanguage('en')
            ->sendFramed(false)
            ->create_pay_page();
        return $pay;
    }

    public function paytabGetPayment(Request $request)
    {
        if ($request->respMessage == "Authorised") {
            $order                          = Order::find($request->data['order_id']);
            $order->status                  = 1;
            $order->payment_id              = $request->payment_id;
            $order->paymet_type             = 'paytab';
            $order->update();
            $coupons                        = Coupon::find($request->data['coupon']);
            if (!empty($coupons)) {
                $userCoupon                 = new UserCoupon();
                $userCoupon->userrequest    = $request->data['request_user_id'];
                $userCoupon->coupon         = $coupons->id;
                $userCoupon->order          = $order->id;
                $userCoupon->save();
                $usedCoupun = $coupons->usedCoupon();
                if ($coupons->limit <= $usedCoupun) {
                    $coupons->is_active     = 0;
                    $coupons->save();
                }
            }
            if (UtilityFacades::getsettings('approve_type') == 'Auto') {
                UtilityFacades::approvedRequest($order);
            }
            return redirect()->route('landingpage')->with('status', 'Thanks for registration, your account is in review and you get email when your account active.');
        } else {
            $order                          = Order::find($request->data['order_id']);
            $order->status                  = 2;
            $order->payment_id              = $request->transaction_id;
            $order->paymet_type             = 'paytab';
            $order->update();
            return redirect()->back()->with('failed', __('Payment failed.'));
        }
    }

    public function token()
    {
        session_start();
        $request_token      = $this->_bkash_Get_Token();
        $idtoken            = $request_token['id_token'];
        $_SESSION['token']  = $idtoken;
        /*$strJsonFileContents = file_get_contents("config.json");
        $array = json_decode($strJsonFileContents, true);*/
        $array              = $this->_get_config_file();
        $array['token']     = $idtoken;
        $newJsonString      = json_encode($array);
        $file               = Storage::path(UtilityFacades::keysettings('bkash_json_file', 1));
        File::put($file, $newJsonString);
        return $idtoken;
    }

    public function paymentInit(Request $request)
    {
        $planID         = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan           = Plan::find($planID);
        $order          = Order::find($request->order_id);
        $requestuser    = RequestUser::find($order->request_user_id);
        $couponId       = '0';
        $couponCode     = null;
        $discountValue  = null;
        $price          = $plan->price;
        $coupons        = Coupon::where('code', $request->coupon)->where('is_active', '1')->first();
        if ($coupons) {
            $couponCode = $coupons->code;
            $usedCoupun = $coupons->usedCoupon();
            if ($coupons->limit == $usedCoupun) {
                $res_data['errors'] = 'This coupon code has expired.';
            } else {
                $discount       = $coupons->discount;
                $discount_type  = $coupons->discount_type;
                $discountValue  = UtilityFacades::calculateDiscount($price, $discount, $discount_type);
                $price          = $price - $discountValue;
                if ($price < 0) {
                    $price      = $plan->price;
                }
                $couponId       = $coupons->id;
            }
        }
        $order->plan_id                 = $plan->id;
        $order->request_user_id         = $requestuser->id;
        $order->amount                  = $price;
        $order->discount_amount         = $discountValue;
        $order->coupon_code             = $couponCode;
        $order->status                  = 0;
        $order->save();
        $res_data['request_user_id']    = $requestuser->id;
        $res_data['total_price']        = $price;
        $res_data['coupon']             = $couponId;
        $res_data['order_id']           = $order->id;
        return $res_data;
    }

    protected function _bkash_Get_Token()
    {
        /*$strJsonFileContents = file_get_contents("config.json");
        $array = json_decode($strJsonFileContents, true);*/
        $array      = $this->_get_config_file();
        $post_token = array(
            'app_key'       => $array["app_key"],
            'app_secret'    => $array["app_secret"]
        );
        $url        = curl_init($array["tokenURL"]);
        $array["proxy"];
        $posttoken  = json_encode($post_token);
        $header     = array(
            'Content-Type:application/json',
            'password:' . $array["password"],
            'username:' . $array["username"]
        );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $posttoken);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        //curl_setopt($url, CURLOPT_PROXY, $proxy);
        $resultdata = curl_exec($url);
        curl_close($url);
        return json_decode($resultdata, true);
    }

    protected function _get_config_file()
    {
        $path = Storage::path(UtilityFacades::keysettings('bkash_json_file', 1));
        return json_decode(file_get_contents($path), true);
    }

    public function createPayment(Request $request)
    {
        session_start();
        /*$strJsonFileContents = file_get_contents("config.json");
        $array = json_decode($strJsonFileContents, true);*/
        $array          = $this->_get_config_file();
        $amount         = $request->amount;
        $invoice        = $request->invoice; // must be unique
        $intent         = "sale";
        $array["proxy"];
        $currency       = UtilityFacades::keysettings('bkash_currency', 1);
        $createpaybody  = array('amount' => $amount, 'currency' => $currency, 'merchantInvoiceNumber' => $invoice, 'intent' => $intent);
        $url            = curl_init($array["createURL"]);
        $createpaybodyx = json_encode($createpaybody);
        $header = array(
            'Content-Type:application/json',
            'authorization:' . $array["token"],
            'x-app-key:' . $array["app_key"]
        );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $createpaybodyx);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        //curl_setopt($url, CURLOPT_PROXY, $proxy);
        $resultdata     = curl_exec($url);
        curl_close($url);
        return $resultdata;
    }

    public function executePayment(Request $request)
    {
        session_start();
        /*$strJsonFileContents = file_get_contents("config.json");
        $array = json_decode($strJsonFileContents, true);*/
        $array      = $this->_get_config_file();
        $paymentID  = $request->paymentID;
        $array["proxy"];
        $url        = curl_init($array["executeURL"] . $paymentID);
        $header     = array(
            'Content-Type:application/json',
            'authorization:' . $array["token"],
            'x-app-key:' . $array["app_key"]
        );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        // curl_setopt($url, CURLOPT_PROXY, $proxy);
        $resultdatax = curl_exec($url);
        curl_close($url);
        $this->_updateOrderStatus($resultdatax, $request->order_id, $request->request_user_id, $request->coupon);
        return $resultdatax;
    }

    protected function _updateOrderStatus($resultdatax, $orderId, $request_user_id, $coupon)
    {
        $resultdatax                        = json_decode($resultdatax);
        if ($resultdatax && $resultdatax->paymentID != null && $resultdatax->transactionStatus == 'Completed') {
            $order                          = Order::find($orderId);
            $order->status                  = 1;
            $order->payment_id              = $resultdatax->trxID;
            $order->paymet_type             = 'paytab';
            $order->update();
            $coupons = Coupon::find($coupon);
            if (!empty($coupons)) {
                $userCoupon                 = new UserCoupon();
                $userCoupon->userrequest    = $request_user_id;
                $userCoupon->coupon         = $coupons->id;
                $userCoupon->order          = $order->id;
                $userCoupon->save();
                $usedCoupun = $coupons->usedCoupon();
                if ($coupons->limit <= $usedCoupun) {
                    $coupons->is_active     = 0;
                    $coupons->save();
                }
            }
        }
    }
}
