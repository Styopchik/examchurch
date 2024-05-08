<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Facades\UtilityFacades;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserCoupon;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class MercadoController extends Controller
{
    public function mercadoPrepare(Request $request)
    {
        $planID                 = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $authuser               = \Auth::user();
        $mercadoMode            = UtilityFacades::keysettings('mercadosetting', 1);
        $mercadoAccessToken     = UtilityFacades::keysettings('mercado_access_token', 1);
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
        $resData['total_price'] = $price;
        $resData['coupon']      = $couponId;
        $resData['order_id']    = $order->id;
        \MercadoPago\SDK::setAccessToken($mercadoAccessToken);
        try {
            $preference = new \MercadoPago\Preference();
            // Create an item in the preference
            $item              = new \MercadoPago\Item();
            $item->title       = "Plan : " . $plan->name;
            $item->quantity    = 1;
            $item->unit_price  = $resData['total_price'];
            $preference->items = array($item);
            $successUrl        = route('mercado.payment.callback', [Crypt::encrypt(['order_id' => $resData['order_id'], 'coupon' => $resData['coupon'], 'flag' => 'success'])]);
            $failureUrl        = route('mercado.payment.callback', [Crypt::encrypt(['order_id' => $resData['order_id'], 'coupon' => $resData['coupon'], 'flag' => 'failure'])]);
            $pendingUrl        = route('mercado.payment.callback', [Crypt::encrypt(['order_id' => $resData['order_id'], 'coupon' => $resData['coupon'], 'flag' => 'pending'])]);
            $preference->back_urls = array(
                "success" => $successUrl,
                "failure" => $failureUrl,
                "pending" => $pendingUrl,
            );
            $preference->auto_return = "approved";
            $preference->save();
            if ($mercadoMode == 'live') {
                $redirectUrl = $preference->init_point;
                return redirect($redirectUrl);
            } else {
                $redirectUrl = $preference->sandbox_init_point;
                return redirect($redirectUrl);
            }
        } catch (Exception $e) {
            return redirect()->back()->with('failed', __('Something wents wrong.'));
        }
    }

    public function mercadoCallback(Request $request, $data)
    {
        $data = Crypt::decrypt($data);
        if ($data['flag'] == 'success') {
            $order                  = Order::find($data['order_id']);
            $order->status          = 1;
            $order->payment_id      = $request->payment_id;
            $order->paymet_type     = 'mercadopago';
            $order->save();
            $user                   = User::find(Auth::user()->id);
            $coupons                = Coupon::find($data['coupon']);
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
            $plan                   = Plan::find($order->plan_id);
            $user->plan_id          = $plan->id;
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
            $order->paymet_type = 'mercadopago';
            $order->save();
            return redirect()->route('plans.index')->with('errors', __('Payment failed.'));
        }
        return redirect()->route('plans.index');
    }
}
