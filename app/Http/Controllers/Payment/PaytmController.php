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
use Illuminate\Support\Facades\Auth;
use Paytm;

class PaytmController extends Controller
{
    //paytm coupon
    public function pay(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'mobile_number' => 'required|numeric|digits:10',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            $errors['errors'] = $messages->first();
            return $errors;
        }
        $planID         = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $authuser       = \Auth::user();
        $payment        = Paytm::with('receive');
        $plan           = Plan::find($planID);
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
                $discountType   = $coupons->discount_type;
                $discountValue  = UtilityFacades::calculateDiscount($price, $discount, $discountType);
                $price          = $price - $discountValue;
                if ($price < 0) {
                    $price = $plan->price;
                }
                $couponId = $coupons->id;
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
        $resData['user_id']     = $authuser->id;
        $resData['email']       = $authuser->email;
        $resData['total_price'] = $price;
        $resData['coupon']      = $couponId;
        $resData['order_id']    = $order->id;
        $payment->prepare([
            'order' => rand(),
            'user' => $resData['user_id'],
            'mobile_number' => $request->mobile_number,
            'email' => $resData['email'],
            'amount' =>  $resData['total_price'], // amount will be paid in INR.
            'callback_url' => route('paypaytm.callback', ['coupon' => $resData['coupon'], 'order_id' => $resData['order_id']]) // callback URL
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
        $plan = Plan::find($order->plan_id);
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
    }
}
