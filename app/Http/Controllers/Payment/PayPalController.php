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
use Illuminate\Support\Facades\Crypt;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalController extends Controller
{
    //paypal coupon
    public function processTransactionAdmin(Request $request)
    {
        $authuser               = \Auth::user();
        $planID                 = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan                   = Plan::find($planID);
        $couponId               = '0';
        $price                  = $plan->price;
        $couponCode             = null;
        $discountValue          = null;
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
        $order = Order::create([
            'plan_id'           => $plan->id,
            'user_id'           => $authuser->id,
            'amount'            => $price,
            'discount_amount'   => $discountValue,
            'coupon_code'       => $couponCode,
            'status'            => 0,
        ]);
        $resData['total_price']        = $price;
        $resData['coupon']             = $couponId;
        $resData['order_id']           = $order->id;
        $resData['email']              = \Auth::user()->email;
        $resData['plan_name']          = $plan->name;
        $resData['currency_symbol']    = env('CURRENCY_SYMBOL');
        $resData['currency']           = env('CURRENCY');
        $resData['plan_id']            = $plan->id;
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                'return_url'           => route('paysuccessTransaction', Crypt::encrypt(['coupon' => $resData['coupon'], 'product_name' => $plan->name, 'price' => $resData['total_price'], 'user_id' => $authuser->id, 'currency' => $resData['currency'], 'coupon' => $resData['coupon'], 'product_id' => $plan->id, 'order_id' => $resData['order_id']])),
                'cancel_url'           => route('paycancelTransaction', Crypt::encrypt(['coupon' => $resData['coupon'], 'product_name' => $plan->name, 'price' => $resData['total_price'], 'user_id' => $authuser->id, 'currency' => $resData['currency'], 'coupon' => $resData['coupon'], 'product_id' => $plan->id, 'order_id' => $resData['order_id']])),
            ],
            "purchase_units" => [
                0 => [
                    "amount" => [
                        "currency_code" => $resData['currency'],
                        "value"         => $resData['total_price'],
                    ]
                ]
            ]
        ]);
        if (isset($response['id']) && $response['id'] != null) {
            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    return redirect()->away($links['href']);
                }
            }
            return redirect()->back()->with('failed',  'Something wents wrong.');
        } else {
            return redirect()->back()->with('failed',  'Something wents wrong.');
        }
    }

    public function successTransaction($data, Request $request)
    {
        $data                   = Crypt::decrypt($data);
        $order                  = Order::find($data['order_id']);
        $order->status          = 1;
        $order->payment_id      = $request['PayerID'];
        $order->paymet_type     = 'paypal';
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
        $plan                    = Plan::find($order['plan_id']);
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

    public function cancelTransaction($data)
    {
        $data               = Crypt::decrypt($data);
        $order              = Order::find($data['order_id']);
        $order->status      = 2;
        $order->paymet_type = 'paypal';
        $order->update();
        return redirect()->route('plans.index')->with('failed', 'Payment canceled.');
    }
}
