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

class RazorpayPaymentController extends Controller
{
    //razorpay coupon
    public function razorPayPayment(Request $request)
    {
        $planID                 = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $authuser               = \Auth::user();
        $plan                   = Plan::find($planID);
        $coupon_id              = '0';
        $price                  = $plan->price;
        $couponCode             = null;
        $discountValue          = null;
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
                    $price = $plan->price;
                }
                $coupon_id      = $coupons->id;
            }
        }
        Order::create([
            'plan_id'           => $plan->id,
            'user_id'           => $authuser->id,
            'amount'            => $price,
            'discount_amount'   => $discountValue,
            'coupon_code'       => $couponCode,
            'status'            => 0,
        ]);
        $resData['email']               = \Auth::user()->email;
        $resData['plan_name']           = $plan->name;
        $resData['total_price']         = $price;
        $resData['currency_symbol']     = env('CURRENCY_SYMBOL');
        $resData['currency']            = env('CURRENCY');
        $resData['coupon']              = $coupon_id;
        $resData['plan_id']             = $plan->id;
        return $resData;
    }

    public function razorpayCallback(Request $request, $transaction_id, $coupon_id, $plan_id)
    {
        $planID                     = $plan_id;
        $order                      = Order::orderBy('id', 'desc')->first();
        $order->status              = 1;
        $order->payment_id          = $transaction_id;
        $order->paymet_type         = 'razorpay';
        $order->update();
        $coupons                    = Coupon::find($coupon_id);
        $user                       = User::find(Auth::user()->id);
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
        $plan                       = Plan::find($planID);
        $user->plan_id              = $plan->id;
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
