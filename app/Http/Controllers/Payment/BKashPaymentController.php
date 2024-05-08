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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BKashPaymentController extends Controller
{
    public function token()
    {
        session_start();
        $request_token = $this->_bkash_Get_Token();
        $idtoken = $request_token['id_token'];
        $_SESSION['token'] = $idtoken;
        /*$strJsonFileContents = file_get_contents("config.json");
        $array = json_decode($strJsonFileContents, true);*/
        $array = $this->_get_config_file();
        $array['token'] = $idtoken;
        $newJsonString = json_encode($array);
        $file = Storage::path(UtilityFacades::keysettings('bkash_json_file', 1));
        File::put($file, $newJsonString);
        return $idtoken;
    }

    public function paymentInit(Request $request)
    {
        $planID                 = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $authuser               = \Auth::user();
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
                $resData['errors'] = 'This coupon code has expired.';
            } else {
                $discount = $coupons->discount;
                $discount_type  = $coupons->discount_type;
                $discountValue  = UtilityFacades::calculateDiscount($price, $discount, $discount_type);
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
        return $resData;
    }

    protected function _bkash_Get_Token()
    {
        /*$strJsonFileContents = file_get_contents("config.json");
        $array = json_decode($strJsonFileContents, true);*/
        $array          = $this->_get_config_file();
        $post_token = array(
            'app_key'    => $array["app_key"],
            'app_secret' => $array["app_secret"]
        );
        $url            = curl_init($array["tokenURL"]);
        $array["proxy"];
        $posttoken      = json_encode($post_token);
        $header = array(
            'Content-Type:application/json',
            'password:' . $array["password"],
            'username:' . $array["username"]
        );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $posttoken);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        //curl_setopt($url, CURLOPT_PROXY, $proxy);
        $resultdata = curl_exec($url);
        curl_close($url);
        return json_decode($resultdata, true);
    }

    protected function _get_config_file()
    {
        $path = Storage::path(UtilityFacades::keysettings('bkash_json_file', 1));
        return json_decode(file_get_contents($path), true);
    }

    public function createPayment(Request $request)
    {
        session_start();
        /*$strJsonFileContents = file_get_contents("config.json");
        $array = json_decode($strJsonFileContents, true);*/
        $array = $this->_get_config_file();
        $amount = $request->amount;
        $invoice = $request->invoice; // must be unique
        $intent = "sale";
        $array["proxy"];
        $currency = UtilityFacades::keysettings('bkash_currency', 1);
        $createpaybody = array('amount' => $amount, 'currency' => $currency, 'merchantInvoiceNumber' => $invoice, 'intent' => $intent);
        $url = curl_init($array["createURL"]);
        $createpaybodyx = json_encode($createpaybody);
        $header = array(
            'Content-Type:application/json',
            'authorization:' . $array["token"],
            'x-app-key:' . $array["app_key"]
        );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $createpaybodyx);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        //curl_setopt($url, CURLOPT_PROXY, $proxy);
        $resultdata = curl_exec($url);
        curl_close($url);
        return $resultdata;
    }

    public function executePayment(Request $request)
    {
        session_start();
        /*$strJsonFileContents = file_get_contents("config.json");
        $array = json_decode($strJsonFileContents, true);*/
        $array = $this->_get_config_file();
        $paymentID = $request->paymentID;
        $array["proxy"];
        $url = curl_init($array["executeURL"] . $paymentID);
        $header = array(
            'Content-Type:application/json',
            'authorization:' . $array["token"],
            'x-app-key:' . $array["app_key"]
        );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        // curl_setopt($url, CURLOPT_PROXY, $proxy);
        $resultdatax = curl_exec($url);
        curl_close($url);
        $this->_updateOrderStatus($resultdatax, $request->order_id);
        return $resultdatax;
    }

    protected function _updateOrderStatus($resultdatax, $orderId)
    {
        $resultdatax = json_decode($resultdatax);
        $authuser  = \Auth::user();
        if ($resultdatax && $resultdatax->paymentID != null && $resultdatax->transactionStatus == 'Completed') {
            $order = Order::find($orderId);
            $order->status = 1;
            $order->payment_id = $resultdatax->trxID;
            $order->paymet_type = 'Cashfree';
            $order->update();
            $coupons = Coupon::where('code', $order->coupon_code)->where('is_active', '1')->first();
            $user = User::find($authuser->id);
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
            $plan = Plan::find($order['plan_id']);
            $user->plan_id = $plan->id;
            if ($plan->durationtype == 'Month' && $plan->id != '1') {
                $user->plan_expired_date = Carbon::now()->addMonths($plan->duration)->isoFormat('YYYY-MM-DD');
            } elseif ($plan->durationtype == 'Year' && $plan->id != '1') {
                $user->plan_expired_date = Carbon::now()->addYears($plan->duration)->isoFormat('YYYY-MM-DD');
            } else {
                $user->plan_expired_date = null;
            }
            $user->save();
        }
    }
}
