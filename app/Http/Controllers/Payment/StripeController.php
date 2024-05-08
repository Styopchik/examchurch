<?php

namespace App\Http\Controllers\Payment;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use Illuminate\Http\Request;
use App\Facades\UtilityFacades;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Stripe\Charge;
use Stripe\Stripe as StripeStripe;
use App\Models\UserCoupon;

class StripeController extends Controller
{
    public function stripePostPending(Request $request)
    {
        $planID             = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $authuser           = \Auth::user();
        $plan               = Plan::find($planID);
        $couponId           = '0';
        $price              = $plan->price;
        $couponCode         = null;
        $discountValue      = null;
        $coupons = Coupon::where('code', $request->coupon)->where('is_active', '1')->first();
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
                    $price = $plan->price;
                }
                $couponId       = $coupons->id;
            }
        }
        $order = Order::create([
            'plan_id'           => $plan->id,
            'user_id'           => $authuser->id,
            'amount'            => $price,
            'discount_amount'   => $discountValue,
            'coupon_code'       => $couponCode,
            'status'            => 0,
        ]);
        $resData['total_price'] = $price;
        $resData['plan_id']     = $plan->id;
        $resData['coupon']      = $couponId;
        $resData['order_id']    = $order->id;
        return $resData;
    }

    public function stripeSession(Request $request)
    {
        $currency           = env('CURRENCY');
        StripeStripe::setApiKey(UtilityFacades::keysettings('stripe_secret', 1));
        if (!empty($request->createCheckoutSession)) {
            $plan           =  Plan::find($request->plan_id);
            try {
                $checkoutSession = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price_data' => [
                            'product_data' => [
                                'name' => $plan->name,
                                'metadata' => [
                                    'plan_id' => $request->plan_id,
                                    'user_id' => Auth::user()->id
                                ]
                            ],
                            'unit_amount' => $request->amount * 100,
                            'currency' => $currency,
                        ],
                        'quantity' => 1,
                    ]],
                    'mode'          => 'payment',
                    'success_url'   => route('stripe.success.pay', Crypt::encrypt(['coupon' => $request->coupon, 'plan_id' => $plan->id, 'price' => $request->amount, 'user_id' => Auth::user()->id, 'order_id' => $request->order_id, 'type' => 'stripe'])),
                    'cancel_url'    => route('stripe.cancel.pay', Crypt::encrypt(['coupon' => $request->coupon, 'plan_id' => $plan->id, 'price' => $request->amount, 'user_id' => Auth::user()->id, 'order_id' => $request->order_id, 'type' => 'stripe'])),
                ]);
            } catch (Exception $e) {
                $apiError           = $e->getMessage();
            }
            if (empty($apiError) && $checkoutSession) {
                $response = array(
                    'status'        => 1,
                    'message'       => 'Checkout session created successfully.',
                    'sessionId'     => $checkoutSession->id
                );
            } else {
                $response = array(
                    'status' => 0,
                    'errors' => array(
                        'message' => 'Checkout session creation failed.' . $apiError
                    )
                );
            }
        }
        echo json_encode($response);
        die;
    }

    function paymentPending(Request $request)
    {
        $user   = User::find(Auth::user()->id);
        $plan   = Plan::find($request->plan_id);
        $user   = User::where('email', $user->email)->first();
        $order  = Order::create([
            'plan_id'           => $request->plan_id,
            'user_id'           => $user->id,
            'amount'            => $plan->price,
            'status'            => 0,
        ]);
        $response = array(
            'status'            => 0,
            'order_id'          => $order->id,
            'amount'            => $order->amount,
            'currency'          => $request->currency,
            'currency_symbol'   => $request->currency_symbol,
            'plan_name'         => $plan->name,
        );
        echo json_encode($response);
        die;
    }

    function paymentCancel($data)
    {
        $data                   = Crypt::decrypt($data);
        if (Auth::user()->type == 'Admin') {
            $order              = Order::find($data['order_id']);
            $order->status      = 2;
            $order->paymet_type = 'stripe';
            $order->update();
        }
        return redirect()->route('plans.index')->with('failed', 'Payment canceled.');
    }

    function paymentSuccess(Request $request, $data)
    {
        $data = Crypt::decrypt($data);
        if (Auth::user()->type == 'Admin') {
            $order              = Order::find($data['order_id']);
            $order->status      = 1;
            $order->paymet_type = 'stripe';
            $order->update();
            $coupons = Coupon::find($data['coupon']);
            $user = User::find(Auth::user()->id);
            if (!empty($coupons)) {
                $userCoupon         = new UserCoupon();
                $userCoupon->user   = $user->id;
                $userCoupon->coupon = $coupons->id;
                $userCoupon->order  = $order->id;
                $userCoupon->save();
                $usedCoupun = $coupons->usedCoupon();
                if ($coupons->limit <= $usedCoupun) {
                    $coupons->is_active = 0;
                    $coupons->save();
                }
            }
            $plan                 = Plan::find($data['plan_id']);
            $user->plan_id        = $plan->id;
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
}
