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
use Paytabscom\Laravel_paytabs\Facades\paypage;

class PayTabController extends Controller
{
    public function planPayWithPaytab(Request $request)
    {
        config([
            'paytabs.profile_id' => UtilityFacades::keysettings('paytab_profile_id', 1),
            'paytabs.server_key' => UtilityFacades::keysettings('paytab_server_key', 1),
            'paytabs.region'     =>  UtilityFacades::keysettings('paytab_region', 1),
            'paytabs.currency'   => UtilityFacades::keysettings('paytab_currency', 1),
        ]);
        $planID                 = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $authuser               = \Auth::user();
        $plan                   = Plan::find($planID);
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
        $resData['user_id']     = $authuser->id;
        $resData['email']       = $authuser->email;
        $resData['total_price'] = $price;
        $resData['coupon']      = $couponId;
        $resData['order_id']    = $order->id;
        $pay = paypage::sendPaymentCode('all')
            ->sendTransaction('sale')
            ->sendCart(1, $price, 'plan payment')
            ->sendCustomerDetails(isset($authuser->name) ? $authuser->name : "", isset($authuser->email) ? $authuser->email : '', '', '', '', '', '', '', '')
            ->sendURLs(
                route('admin.paytab.success', ['success' => 1, 'data' => $request->all(), 'plan_id' => $plan->id, 'amount' => $price, 'coupon' => $resData['coupon'], 'order_id' => $resData['order_id']]),
                route('admin.paytab.success', ['success' => 0, 'data' => $request->all(), 'plan_id' => $plan->id, 'amount' => $price, 'coupon' => $resData['coupon'], 'order_id' => $resData['order_id']])
            )
            ->sendLanguage('en')
            ->sendFramed(false)
            ->create_pay_page();
        return $pay;
    }

    public function paytabGetPayment(Request $request)
    {
        $order              = Order::find($request->order_id);
        $order->status      = 1;
        $order->paymet_type = 'paytab';
        $order->update();
        $coupons            = Coupon::find($request->coupon);
        $user               = User::find(Auth::user()->id);
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
        $plan = Plan::find($request->plan_id);
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
