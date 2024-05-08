<?php

namespace App\Http\Controllers\Payment;

use App\Facades\UtilityFacades;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserCoupon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class PayUMoneyController extends Controller
{
    public function payumoneyPayment(Request $request)
    {
        $planID         = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $authuser       = \Auth::user();
        $plan           = Plan::find($planID);
        $couponId       = '0';
        $couponCode     = null;
        $discountValue  = null;
        $price          = $plan->price;
        $coupons        = Coupon::where('code', $request->coupon)->where('is_active', '1')->first();
        if ($coupons) {
            $couponCode = $coupons->code;
            $usedCoupun     = $coupons->usedCoupon();
            if ($coupons->limit == $usedCoupun) {
                $res_data['errors'] = __('This coupon code has expired.');
            } else {
                $discount = $coupons->discount;
                $discountType       = $coupons->discount_type;
                $discountValue      = UtilityFacades::calculateDiscount($price, $discount, $discountType);
                $price              = $price - $discountValue;
                if ($price < 0) {
                    $price = $plan->price;
                }
                $couponId           = $coupons->id;
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
        $resData['email']       = $authuser->email;
        $resData['total_price'] = $price;
        $resData['coupon']      = $couponId;
        $resData['plan_id']     = $plan->id;
        $resData['order_id']    = $order->id;
        $key                    = UtilityFacades::keysettings('payumoney_merchant_key', 1);
        $txnid                  = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
        $salt                   = UtilityFacades::keysettings('payumoney_salt_key', 1);
        $amount                 = $price;
        $hashString             = $key . '|' . $txnid . '|' . $amount . '|' . $plan->name . '|' . $authuser->name . '|' . $authuser->email . '|' . '||||||||||' . $salt;
        $hash                   = strtolower(hash('sha512', $hashString));
        $payuUrl                = 'https://test.payu.in/_payment';  // For production environment, change it to 'https://secure.payumoney.com/_payment'
        $paymentData = [
            'key'               => $key,
            'txnid'             => $txnid,
            'amount'            => $resData['total_price'],
            'productinfo'       => $plan->name,
            'firstname'         => $authuser->name,
            'email'             => $authuser->email,
            'hash'              => $hash,
            'surl'              => route('payu.success', Crypt::encrypt(['key' => $key, 'productinfo' => $plan->name, 'firstname' => $authuser->name, 'email' => $authuser->email,  'txnid' => $txnid,  'order_id' => $resData['order_id'], 'user_id' => $authuser->id, 'coupon' => $couponId, 'plan_id' => $plan->id, 'discount_amount' => $discountValue, 'currency' => env('CURRENCY_SYMBOL'), 'payment_type' => 'payumoney', 'status' => 'successfull'])),
            'furl'              => route('payu.failure', Crypt::encrypt(['key' => $key, 'productinfo' => $plan->name, 'firstname' => $authuser->name, 'email' => $authuser->email,  'txnid' => $txnid, 'order_id' => $resData['order_id'], 'user_id' => $authuser->id, 'coupon' => $couponId, 'plan_id' => $plan->id, 'discount_amount' => $discountValue, 'currency' => env('CURRENCY_SYMBOL'), 'payment_type' => 'payumoney', 'status' => 'failed'])),
        ];
        return view('plans.payumoneyRedirect', compact('payuUrl', 'paymentData'));
    }

    public function payuSuccess(Request $request, $data)
    {
        $data                       = Crypt::decrypt($data);
        if (\Auth::user()->type == 'Admin') {
            $order                  = Order::find($data['order_id']);
            $order->status          = 1;
            $order->payment_id      = $data['txnid'];
            $order->paymet_type     = 'payumoney';
            $order->update();
            $coupons                = Coupon::find($data['coupon']);
            $user                   = User::find(\Auth::user()->id);
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
            $plan                   = Plan::find($data['plan_id']);
            $user->plan_id          = $plan->id;
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

    public function payuFailure($data)
    {
        $data                   = Crypt::decrypt($data);
        $order                  = Order::find($data['order_id']);
        $order->status          = 2;
        $order->paymet_type     = 'payumoney';
        $order->update();
        return redirect()->route('plans.index')->with('errors', __('Payment payuFailure'));
    }
}
