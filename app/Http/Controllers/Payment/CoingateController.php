<?php

namespace App\Http\Controllers\Payment;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use CoinGate\CoinGate;
use App\Facades\UtilityFacades;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use App\Models\UserCoupon;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

class CoingateController extends Controller
{
    // coingate coupon
    public function coingatePrepare(Request $request)
    {
        $planID                 = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $authuser               = \Auth::user();
        $coingateEnvironment    = UtilityFacades::keysettings('coingate_environment', 1);
        $coingateAuthToken      = UtilityFacades::keysettings('coingate_auth_token', 1);
        $currency               = env('CURRENCY');
        $plan                   = Plan::find($planID);
        $couponId               = '0';
        $price                  = $plan->price;
        $couponCode             = null;
        $discountValue          = null;
        $coupons                = Coupon::where('code', $request->coupon)->where('is_active', '1')->first();
        if ($coupons) {
            $couponCode = $coupons->code;
            $usedCoupun     = $coupons->usedCoupon();
            if ($coupons->limit == $usedCoupun) {
                $res_data['errors'] = 'This coupon code has expired.';
            } else {
                $discount = $coupons->discount;
                $discountType = $coupons->discount_type;
                $discountValue = UtilityFacades::calculateDiscount($price, $discount, $discountType);
                $price          = $price - $discountValue;
                if ($price < 0) {
                    $price = $plan->price;
                }
                $couponId = $coupons->id;
            }
        }
        $order = Order::create([
            'plan_id' => $plan->id,
            'user_id' => $authuser->id,
            'amount' => $price,
            'discount_amount' => $discountValue,
            'coupon_code' => $couponCode,
            'status' => 0,
        ]);
        $resData['total_price'] = $price;
        $resData['coupon']      = $couponId;
        $resData['order_id']    = $order->id;
        CoinGate::config(
            array(
                'environment' => $coingateEnvironment,  // sandbox OR live
                'auth_token' => $coingateAuthToken,
                'curlopt_ssl_verifypeer' => FALSE
            )
        );
        $params = array(
            'order_id' => rand(),
            'price_amount' => $resData['total_price'],
            'price_currency' => $currency,
            'receive_currency' => $currency,
            'callback_url' => route('coingate.payment.callback', Crypt::encrypt(['order_id' => $resData['order_id'], 'coupon_id' => $resData['coupon'], 'plan_id' => $planID])),
            'cancel_url' => route('coingate.payment.callback', Crypt::encrypt(['order_id' => $resData['order_id'], 'coupon_id' => $resData['coupon'], 'plan_id' => $planID, 'status' => 'failed'])),
            'success_url' => route('coingate.payment.callback', Crypt::encrypt(['order_id' => $resData['order_id'], 'coupon_id' => $resData['coupon'], 'plan_id' => $planID, 'status' => 'successfull'])),
        );
        $order = \CoinGate\Merchant\Order::create($params);
        if ($order) {
            $paymentId = Order::find($resData['order_id']);
            $paymentId->payment_id = $order->id;
            $paymentId->update();
            return redirect($order->payment_url);
        } else {
            return redirect()->back()->with('errors', __('Opps something wents wrong.'));
        }
    }

    public function coingateCallback($data)
    {
        $data = Crypt::decrypt($data);
        if ($data['status'] == 'successfull') {
            $order = Order::find($data['order_id']);
            $order->status = 1;
            $order->paymet_type = 'Coingate';
            $order->update();
            $user = User::find(Auth::user()->id);
            $plan = Plan::find($data['plan_id']);
            $user->plan_id = $plan->id;
            if ($plan->durationtype == 'Month' && $plan->id != '1') {
                $user->plan_expired_date = Carbon::now()->addMonths($plan->duration)->isoFormat('YYYY-MM-DD');
            } elseif ($plan->durationtype == 'Year' && $plan->id != '1') {
                $user->plan_expired_date = Carbon::now()->addYears($plan->duration)->isoFormat('YYYY-MM-DD');
            } else {
                $user->plan_expired_date = null;
            }
            $user->save();
            return redirect()->route('plans.index')->with('success', __('Payment successfully.'));
        } else {
            $order = Order::find($data['order_id']);
            $order->status = 2;
            $order->paymet_type = 'coingate';
            $order->update();
            return redirect()->route('plans.index')->with('failed', __('Opps something wents wrong.'));
        }
    }
}
