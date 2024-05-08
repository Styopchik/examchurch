<?php

namespace App\Http\Controllers\Payment;

use App\Facades\UtilityFacades;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use CoinGate\CoinGate;
use App\Models\Form;
use App\Models\FormValue;
use Hashids\Hashids;
use Paytm;

class PaymentController extends Controller
{
    public function coingateFillPaymentPrepare(Request $request)
    {
        CoinGate::config(
            array(
                'environment' => UtilityFacades::keysettings('coingate_environment', $request->cg_created_by), // sandbox OR live
                'auth_token' => UtilityFacades::keysettings('coingate_auth_token', $request->cg_created_by),
                'curlopt_ssl_verifypeer' => FALSE // default is false
            )
        );
        $params = array(
            'order_id' => rand(),
            'price_amount' => $request->cg_amount,
            'price_currency' => $request->cg_currency,
            'receive_currency' => $request->cg_currency,
            'callback_url' => route('coingatefillcallback', Crypt::encrypt(['form_id' => $request->cg_form_id, 'submit_type' => $request->cg_submit_type])),
            'cancel_url' => route('coingatefillcallback', Crypt::encrypt(['form_id' => $request->cg_form_id, 'status' => 'failed', 'submit_type' => $request->cg_submit_type])),
            'success_url' => route('coingatefillcallback', Crypt::encrypt(['form_id' => $request->cg_form_id, 'submit_type' => $request->cg_submit_type, 'status' => 'successfull'])),
        );
        $order = \CoinGate\Merchant\Order::create($params);
        $formvalue = FormValue::where('form_id', $request->cg_form_id)->latest('id')->first();
        $formvalue->transaction_id = $order->id;
        $formvalue->save();
        if ($order) {
            return redirect($order->payment_url);
        } else {
            return redirect()->back()->with('errors', __('Opps something wents wrong.'));
        }
    }

    public function coingateFillPlanGetPayment(Request $request, $data)
    {
        $data = Crypt::decrypt($data);
        $form       = Form::find($data['form_id']);
        if ($data['status'] == 'successfull') {
            $formvalue                      = FormValue::where('form_id', $form->id)->latest('id')->first();
            $formvalue->currency_symbol     = $form->currency_symbol;
            $formvalue->currency_name       = $form->currency_name;
            $formvalue->amount              = $form->amount;
            $formvalue->status              = 'successfull';
            $formvalue->payment_type        = 'coingate';
        } else {
            $formvalue = FormValue::where('form_id', $form->id)->latest('id')->first();
            $formvalue->currency_symbol     = $form->currency_symbol;
            $formvalue->currency_name       = $form->currency_name;
            $formvalue->amount              = $form->amount;
            $formvalue->status              = 'failed';
            $formvalue->payment_type        = 'coingate';
        }
        $formvalue->save();
        $hashids    = new Hashids('', 20);
        $id         = $hashids->encodeHex($form->id);
        $successMsg = strip_tags($form->success_msg);
        if ($data['submit_type'] == 'public_fill') {
            return redirect()->route('forms.survey', $id)->with('success', $successMsg);
        } else {
            return redirect()->back()->with('success', $successMsg);
        }
    }

    // paytm form
    public function paymentPaytmPayment(Request $request)
    {
        $payment = Paytm::with('receive');
        $payment->prepare([
            'order' => ($request->order) ? $request->order : rand(),
            'user' => $request->name,
            'mobile_number' => $request->mobile,
            'email' => $request->email,
            'amount' => $request->amount,
            'callback_url' => ($request->order) ?
                route('paymentpaytm.callback', [
                    'plan_id' => $request->plan_id,
                    'amount' => $request->amount,
                    'user_id' => $request->user_id,
                    'order_id' => $request->order,
                    'type' => 'paytm'
                ]) :
                route('paymentfillcallback', ['form_id' => $request->form_id, 'success_msg' => $request->succes_msg]),
        ]);
        $response = $payment->receive();
        return $response;
    }

    public function paymentFillCallback(Request $request)
    {
        $transaction = Paytm::with('receive');
        $response = $transaction->response(); // To get raw response as array
        //Check out response parameters sent by paytm here -> http://paywithpaytm.com/developer/paytm_api_doc?target=interpreting-response-sent-by-paytm
        $transaction->getTransactionId(); // Get transaction id
        $formvalue = FormValue::where('form_id', $request->form_id)->where('payment_type', 'Paytm')->where('status', 'pending')->latest('id')->first();
        if ($transaction->isSuccessful()) {
            //Transaction Successful
            $formvalue->status = 'successfull';
            $formvalue->transaction_id = $transaction->getTransactionId();
            $formvalue->save();
            $success_msg = strip_tags(htmlspecialchars_decode($request->success_msg));
            return redirect()->back()->with('success', $success_msg);
        } else if ($transaction->isFailed()) {
            //Transaction Failed
            $formvalue->status = 'failed';
            $formvalue->transaction_id = $transaction->getTransactionId();
            $formvalue->save();
            return redirect()->back()->with('errors', __('Payment failed.'));
        } else if ($transaction->isOpen()) {
            //Transaction Open/Processing
            $formvalue->status = 'failed';
            $formvalue->transaction_id = $transaction->getTransactionId();
            $formvalue->save();
            return redirect()->back()->with('Failed', __('Payment failed.'));
        }
        $transaction->getResponseMessage(); //Get Response Message If Available
    }

    public function paymentPaytmCallback(Request $request)
    {
        $transaction = Paytm::with('receive');
        $response = $transaction->response(); // To get raw response as array
        //Check out response parameters sent by paytm here -> http://paywithpaytm.com/developer/paytm_api_doc?target=interpreting-response-sent-by-paytm
        $transaction->getTransactionId(); // Get transaction id
        if ($transaction->isSuccessful()) {
            //Transaction Successful
            if (isset(Auth::user()->type) == 'Admin') {
                $order = Order::find($request->order_id);
                $order->status = 1;
                $order->paymet_type = 'paytm';
                $order->payment_id = $transaction->getTransactionId();
                $order->update();
                $user = User::find($request->user_id);
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
                return redirect()->back()->with('success', __('Payment received successfully.'));
            } else {
                $order = Order::find($request->order_id);
                $order->status = 1;
                $order->paymet_type = 'paytm';
                $order->update();
                return redirect()->route('landingpage')->with('success', __('Payment received successfully.'));
            }
        } else if ($transaction->isFailed()) {
            //Transaction Failed
            return redirect()->back()->with('errors', __('Payment failed.'));
        } else if ($transaction->isOpen()) {
            //Transaction Open Processing
            return redirect()->back()->with('Failed', __('Payment failed.'));
        }
        return redirect()->back()->with('errors', $transaction->getResponseMessage());
    }

    // mercado form
    public function mercadoFillPaymentPrepare(Request $request)
    {
        $mercadoMode            = UtilityFacades::keysettings('mercadosetting', $request->mercado_created_by);
        $mercadoAccessToken     = UtilityFacades::keysettings('mercado_access_token', $request->mercado_created_by);
        $form                   = Form::find($request->mercado_form_id);
        \MercadoPago\SDK::setAccessToken($mercadoAccessToken);
        try {
            $preference        = new \MercadoPago\Preference();
            // Create an item in the preference
            $item              = new \MercadoPago\Item();
            $item->title       = $form->title;
            $item->quantity    = 1;
            $item->unit_price  = $request->mercado_amount;
            $preference->items = array($item);
            $successUrl        = route('mercadofillcallback', [Crypt::encrypt(['form_id' => $form->id, 'flag' => 'success', 'submit_type' => $request->mercado_submit_type])]);
            $failureUrl        = route('mercadofillcallback', [Crypt::encrypt(['form_id' => $form->id, 'flag' => 'failure', 'submit_type' => $request->mercado_submit_type])]);
            $pendingUrl        = route('mercadofillcallback', [Crypt::encrypt(['form_id' => $form->id, 'flag' => 'pending', 'submit_type' => $request->mercado_submit_type])]);
            $preference->back_urls = array(
                "success"      => $successUrl,
                "failure"      => $failureUrl,
                "pending"      => $pendingUrl,
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

    public function mercadoFillPlanGetPayment(Request $request, $data)
    {
        $data                           = Crypt::decrypt($data);
        $form                           = Form::find($data['form_id']);
        if ($data['flag'] == 'success') {
            $formvalue                  = FormValue::where('form_id', $form->id)->latest('id')->first();
            $formvalue->currency_symbol = $form->currency_symbol;
            $formvalue->currency_name   = $form->currency_name;
            $formvalue->amount          = $form->amount;
            $formvalue->transaction_id  = $request->payment_id;
            $formvalue->status          = 'successfull';
            $formvalue->payment_type    = 'mercado pago';
        } else {
            $formvalue                  = FormValue::where('form_id', $form->id)->latest('id')->first();
            $formvalue->currency_symbol = $form->currency_symbol;
            $formvalue->currency_name   = $form->currency_name;
            $formvalue->amount          = $form->amount;
            $formvalue->status          = 'failed';
            $formvalue->payment_type    = 'mercado pago';
        }
        $formvalue->save();
        $hashids        = new Hashids('', 20);
        $id             = $hashids->encodeHex($form->id);
        $successMsg     = strip_tags($form->success_msg);
        if ($data['submit_type'] == 'public_fill') {
            return redirect()->route('forms.survey', $id)->with('success', $successMsg);
        } else {
            return redirect()->back()->with('success', $successMsg);
        }
    }

    public function payUmoneyFillPaymentPrepare(Request $request)
    {
        $authuser                   = User::find($request->payumoney_created_by);
        $form                       = Form::find($request->payumoney_form_id);
        $discountValue              = null;
        $price                      = $request->payumoney_amount;
        $currency                   = $request->payumoney_currency;
        $symbol                     = $form->currency_symbol;
        $res_data['form_id']        = $form->id;
        $res_data['email']          = $authuser->email;
        $res_data['total_price']    = $price;
        $key                        = UtilityFacades::getsettings('payumoney_merchant_key');
        $txnid                      = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
        $salt                       = UtilityFacades::getsettings('payumoney_salt_key');
        $amount                     = $price;
        $hashString                 = $key . '|' . $txnid . '|' . $amount . '|' . $form->title . '|' . $authuser->name . '|' . $authuser->email . '|' . '||||||||||' . $salt;
        $hash                       = strtolower(hash('sha512', $hashString));
        $payuUrl                    = 'https://test.payu.in/_payment';
        $paymentData = [
            'key'                   => $key,
            'txnid'                 => $txnid,
            'amount'                => $res_data['total_price'],
            'productinfo'           => $form->title,
            'firstname'             => $authuser->name,
            'email'                 => $authuser->email,
            'phone'                 => '1234567890',
            'hash'                  => $hash,
            'surl'                  => route('payumoneyfillcallback', Crypt::encrypt(['key' => $key, 'productinfo' => $form->name, 'firstname' => $authuser->name,  'phone' => '1234567890', 'email' => $authuser->email, 'amount' => $res_data['total_price'], 'txnid' => $txnid,  'user_id' => $authuser->id,   'currency' => $currency, 'payment_type' => 'payumoney', 'status' => 'pending'])),
            'furl'                  => route('payumoneyfillcallback', Crypt::encrypt(['key' => $key, 'productinfo' => $form->name, 'firstname' => $authuser->name, 'phone' => '1234567890', 'email' => $authuser->email,  'txnid' => $txnid, 'amount' => $res_data['total_price'], 'user_id' => $authuser->id,  'currency' => $currency, 'payment_type' => 'payumoney', 'status' => 'failed'])),
        ];
        return view('plans.payumoneyRedirect', compact('payuUrl', 'paymentData'));
    }

    public function payUmoneyFillPlanGetPayment($data)
    {
        $data                               = Crypt::decrypt($data);
        $form                               = Form::find($data['form_id']);
        if ($data['status'] == 'pending') {
            $formvalue                      = FormValue::where('form_id', $form->id)->latest('id')->first();
            $formvalue->currency_symbol     = $form->currency_symbol;
            $formvalue->currency_name       = $form->currency_name;
            $formvalue->amount              = $form->amount;
            $formvalue->status              = 'successfull';
            $formvalue->payment_type        = 'payumoney';
        } else {
            $formvalue                      = FormValue::where('form_id', $form->id)->latest('id')->first();
            $formvalue->currency_symbol     = $form->currency_symbol;
            $formvalue->currency_name       = $form->currency_name;
            $formvalue->amount              = $form->amount;
            $formvalue->status              = 'failed';
            $formvalue->payment_type        = 'payumoney';
        }
        $formvalue->save();
        $hashids                            = new Hashids('', 20);
        $id                                 = $hashids->encodeHex($form->id);
        $successMsg                         = strip_tags($form->success_msg);
        if ($data['submit_type'] == 'public_fill') {
            return redirect()->route('forms.survey', $id)->with('success', $successMsg);
        } else {
            return redirect()->back()->with('success', $successMsg);
        }
    }
}
