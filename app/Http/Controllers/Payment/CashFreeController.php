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

class CashFreeController extends Controller
{
    public function cashfreePayment(Request $request)
    {
        $planID                 = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $authuser               = \Auth::user();
        $cashfreeAppId          = UtilityFacades::keysettings('cashfree_app_id', 1);
        $cashfreeSecretKey      = UtilityFacades::keysettings('cashfree_secret_key', 1);
        $cashfreeUrl            = UtilityFacades::keysettings('cashfree_url', 1);
        $plan                   = Plan::find($planID);
        $couponId               = '0';
        $price                  = $plan->price;
        $couponCode             = null;
        $discountValue          = null;
        $coupons = Coupon::where('code', $request->coupon)->where('is_active', '1')->first();
        if ($coupons) {
            $couponCode         = $coupons->code;
            $usedCoupun         = $coupons->usedCoupon();
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
        try {
            $url = $cashfreeUrl;
            $headers = array(
                "Content-Type: application/json",
                "x-api-version: 2022-01-01",
                "x-client-id: " . $cashfreeAppId,
                "x-client-secret: " . $cashfreeSecretKey
            );
            $data = json_encode([
                'order_id' =>  'order_' . rand(1111111111, 9999999999),
                'order_amount' => $resData['total_price'],
                "order_currency" => 'INR',
                "order_name" => $plan->name,
                "customer_details" => [
                    "customer_id" => 'customer_' . $authuser->id,
                    "customer_name" => $authuser->name,
                    "customer_email" => $authuser->email,
                    "customer_phone" => $authuser->phone,
                ],
                "order_meta" => [
                    "return_url" => route('cashfree.payment.callback') . '?order_id={order_id}&order_token={order_token}&order=' . Crypt::encrypt($resData['order_id']) . '',
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
        $orderId                       = Crypt::decrypt($request->order);
        $authuser                      = \Auth::user();
        $cashfreeAppId                 = UtilityFacades::keysettings('cashfree_app_id', 1);
        $cashfreeSecretKey             = UtilityFacades::keysettings('cashfree_secret_key', 1);
        $cashfreeUrl                   = UtilityFacades::keysettings('cashfree_url', 1);
        $client                        = new \GuzzleHttp\Client();
        $response = $client->request('GET', $cashfreeUrl . '/' . $request->get('order_id') . '/settlements', [
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
                    'accept' => 'application/json',
                    'x-api-version' => '2022-09-01',
                    'x-client-id' => $cashfreeAppId,
                    'x-client-secret' => $cashfreeSecretKey,
                ],
            ]);
            $info = json_decode($response->getBody());
            if ($info->payment_status == "SUCCESS") {
                $order                  = Order::find($orderId);
                $order->status          = 1;
                $order->payment_id      = $info->cf_payment_id;
                $order->paymet_type     = 'Cashfree';
                $order->update();
                $coupons                = Coupon::where('code', $order->coupon_code)->where('is_active', '1')->first();
                $user                   = User::find($authuser->id);
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
                $plan                   = Plan::find($order['plan_id']);
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
                $order                  = Order::find($orderId);
                $order->status          = 2;
                $order->payment_id      = $info->cf_payment_id;
                $order->paymet_type     = 'cashfree';
                $order->save();
                return redirect()->route('plans.index')->with('failed', __('Opps something wents wrong.'));
            }
        } else {
            $order                      = Order::find($orderId);
            $order->status              = 2;
            $order->paymet_type         = 'cashfree';
            $order->save();
            return redirect()->route('plans.index')->with('errors', __('Payment Failed.'));
        }
        return redirect()->route('plans.index')->with('success', __('Payment successfully.'));
    }
}
