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

class SSPayController extends Controller
{
    public function initPayment(Request $request)
    {
        $planID                     = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $authuser                   = \Auth::user();
        $sspayCategoryCode          = UtilityFacades::keysettings('sspay_category_code', 1);
        $sspaySecretKey             = UtilityFacades::keysettings('sspay_secret_key', 1);
        $sspayDescription           = UtilityFacades::keysettings('sspay_description', 1);
        $plan                       = Plan::find($planID);
        $couponId                   = '0';
        $price                      = $plan->price;
        $couponCode                 = null;
        $discountValue              = null;
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
                $price              = $price - $discountValue;
                if ($price < 0) {
                    $price          = $plan->price;
                }
                $couponId           = $coupons->id;
            }
        }
        $order = Order::create([
            'plan_id'               => $plan->id,
            'user_id'               => $authuser->id,
            'amount'                => $price,
            'discount_amount'       => $discountValue,
            'coupon_code'           => $couponCode,
            'status'                => 0,
        ]);
        $resData['total_price']     = $price;
        $resData['coupon']          = $couponId;
        $resData['order_id']        = $order->id;
        try {
            $someData = array(
                'userSecretKey'                 => $sspaySecretKey,
                'categoryCode'                  => $sspayCategoryCode,
                'billName'                      => $plan->name,
                'billDescription'               => $plan->description,
                'billPriceSetting'              => 1,
                'billPayorInfo'                 => 1,
                'billAmount'                    => round($resData['total_price'] * 100, 2),
                'billReturnUrl'                 => route('sspay.payment.callback') . '?&order=' . $resData['order_id'] . '',
                'billCallbackUrl'               => route('sspay.payment.callback') . '?&order=' . $resData['order_id'] . '',
                'billExternalReferenceNo'       => 'AFR341DFI',
                'billTo'                        => $authuser->name,
                'billEmail'                     => $authuser->email,
                'billPhone'                     => $authuser->phone,
                'billSplitPayment'              => 0,
                'billSplitPaymentArgs'          => '',
                'billPaymentChannel'            => '0',
                'billDisplayMerchant'           => 1,
                'billContentEmail'              => $sspayDescription,
                'billChargeToCustomer'          => 1
            );
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_URL, 'https://sspay.my' . '/index.php/api/createBill');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $someData);
            $result = curl_exec($curl);
            $info = curl_getinfo($curl);
            curl_close($curl);
            $obj = json_decode($result);
            $url = 'https://sspay.my' . '/index.php/api/runBill';
            $billcode = $obj[0]->BillCode;
            $someData = array(
                'userSecretKey'             => $sspaySecretKey,
                'billCode'                  => $billcode,
                'billpaymentAmount'         => round($resData['total_price'] * 100, 2),
                'billpaymentPayorName'      => $authuser->name,
                'billpaymentPayorPhone'     => $authuser->phone,
                'billpaymentPayorEmail'     => $authuser->email,
                'billBankID'                => 'TEST0021'
            );
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $someData);
            $result = curl_exec($curl);
            curl_getinfo($curl);
            curl_close($curl);
            $obj = json_decode($result);
            return redirect()->to('https://sspay.my' . '/' . $billcode);
        } catch (\Exception $e) {
            return redirect()->back()->with('errors', $e->getMessage());
        }
    }

    public function sspayCallback(Request $request)
    {
        $authuser                   = \Auth::user();
        // status_id : Payment status. 1= success, 2=pending, 3=fail
        if ($request->status_id == 1) {
            $order                  = Order::find($request->order);
            $order->status          = 1;
            $order->payment_id      = $request->transaction_id;
            $order->paymet_type     = 'SSPay';
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
            $user->plan_id          = $plan->id;
            if ($plan->durationtype == 'Month' && $plan->id != '1') {
                $user->plan_expired_date = Carbon::now()->addMonths($plan->duration)->isoFormat('YYYY-MM-DD');
            } elseif ($plan->durationtype == 'Year' && $plan->id != '1') {
                $user->plan_expired_date = Carbon::now()->addYears($plan->duration)->isoFormat('YYYY-MM-DD');
            } else {
                $user->plan_expired_date = null;
            }
            $user->save();
            return redirect()->route('plans.index')->with('success', 'Payment successfully.');
        } elseif ($request->status_id == 3) {
            $order                  = Order::find($request->order);
            $order->status          = 2;
            $order->payment_id      = $request->transaction_id;
            $order->paymet_type     = 'SSPay';
            $order->save();
            return redirect()->route('plans.index')->with('failed', __('Opps something wents wrong.'));
        } else {
            $order                  = Order::find($request->order);
            $order->payment_id      = $request->transaction_id;
            $order->paymet_type     = 'SSPay';
            $order->save();
            return redirect()->route('plans.index')->with('warning', __('Waiting for proccesing..'));
        }
    }
}
