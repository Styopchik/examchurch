@php
    use Carbon\Carbon;
    use App\Models\Setting;
    use App\Facades\UtilityFacades;
    $payment_type = [];
    $currency_symbol = env('CURRENCY_SYMBOL');
@endphp
@extends('layouts.main')
@section('title', __('Order Summary'))
@section('breadcrumb')
    <div class="col-md-12">
        <div class="page-header-title">
            <h4 class="m-b-10">{{ __('Order Summary') }}</h4>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">{!! Html::link(route('home'), __('Dashboard'), ['']) !!}</li>
            <li class="breadcrumb-item">{{ __('Plans') }}</li>
            <li class="breadcrumb-item">{{ __('Order Summary') }}</li>
        </ul>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                {{ Form::label('billing_address', __('Billing Address:'), ['class' => 'form-label']) }}
            </div>
            <div class="mt-2 card">
                <div class="card-body inner-padding">
                    <div class="row d-flex justify-content-between">
                        <div class="col-lg-4 col-md-6 col-sm-12 d-flex justify-content-start bill-width">
                            <div class="flex-equal">
                                <table class="table m-0 fs-6 gs-0 gy-2 gx-2">
                                    <tr>
                                        <td class="text-muted">{{ __('Bill to') }}:</td>
                                        <td>
                                            <span class="text-gray-800 text-hover-primary">{{ Auth::user()->email }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">{{ __('Name') }}:</td>
                                        <td class="">{{ Auth::user()->name }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-6 col-sm-12 d-flex justify-content-end plan-width">
                            <div class="flex-equal">
                                <table class="table m-0 fs-6 gs-0 gy-2 gx-2">
                                    <tr class="text-width">
                                        <td class="text-muted">{{ __('Subscription plan') }}:</td>
                                        <td class="">
                                            <span class="text-gray-800 text-hover-primary">{{ $plan->name }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">{{ __('Subscription Description') }}:</td>
                                        <td class="">
                                            <span
                                                class="text-gray-800 text-hover-primary">{{ $plan->description ? $plan->description : '--' }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">{{ __('Subscription Duration') }}:</td>
                                        <td class="">
                                            {{ $plan->duration . ' ' . $plan->durationtype }}
                                    </tr>
                                    <tr class="total_payable">
                                        <td class="text-muted">{{ __('Subscription Fees') }}:</td>
                                        <td class="">
                                            {{ $adminPaymentSetting['currency_symbol'] }}{{ number_format($plan->price, 2) }}/{{ $plan->durationtype }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">{{ __('Disscount Price') }}:</td>
                                        <td class="discount_price">
                                            {{ $adminPaymentSetting['currency_symbol'] }}0.00</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">{{ __('Total Price') }}:</td>
                                        <td class="final-price">
                                            {{ $adminPaymentSetting['currency_symbol'] }}{{ number_format($plan->price, 2) }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-2 card">
                <div class="card-header">
                    <h5>{{ __('Payment Methods') }}</h5>
                </div>
                <div class="card-body">
                    <ul class="mb-3 nav nav-tabs" id="myTab" role="tablist">
                        @if ($paymentTypes)
                            @foreach ($paymentTypes as $key => $paymenttype)
                                @php
                                    if (array_key_first($paymentTypes) == $key) {
                                        $active = 'active show';
                                    } else {
                                        $active = '';
                                    }
                                @endphp
                                <li class="nav-item">
                                    <a class="nav-link text-uppercase {{ $active }} "
                                        id="{{ str_replace(' ', '_', $key) }}-tab" data-bs-toggle="tab"
                                        href="#payment{{ $key }}" role="tab" aria-controls="payment"
                                        aria-selected="true">{{ $paymenttype }}</a>
                                </li>
                            @endforeach
                        @else
                            <h2>{{ 'Please contact to super admin for enable payments.' }}</h2>
                        @endif
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        @foreach ($paymentTypes as $key => $paymenttype)
                            @if (
                                $key == 'stripe' &&
                                    $adminPaymentSetting['stripesetting'] == 'on' &&
                                    !empty($adminPaymentSetting['stripe_key']) &&
                                    !empty($adminPaymentSetting['stripe_secret']))
                                @php
                                    $route = 'stripe.pending';
                                    $id = 'stripe-payment-form';
                                    $button_type = 'submit';
                                    $payment_description = $adminPaymentSetting['stripe_description'];
                                    $button_id = 'pay_with_' . $key;
                                @endphp
                            @elseif (
                                $key == 'paypal' &&
                                    $adminPaymentSetting['paypalsetting'] == 'on' &&
                                    !empty($adminPaymentSetting['paypal_client_id']) &&
                                    !empty($adminPaymentSetting['paypal_client_secret']))
                                @php
                                    $route = 'payprocessTransactionadmin';
                                    $id = 'payment-form';
                                    $button_type = 'submit';
                                    $payment_description = $adminPaymentSetting['paypal_description'];
                                    $button_id = 'pay_with_' . $key;
                                @endphp
                            @elseif (
                                $key == 'razorpay' &&
                                    isset($adminPaymentSetting['razorpaysetting']) &&
                                    $adminPaymentSetting['razorpaysetting'] == 'on')
                                @php
                                    $route = 'payrazorpay.payment';
                                    $id = 'razorpay-payment-form';
                                    $button_type = 'submit';
                                    $payment_description = $adminPaymentSetting['razorpay_description'];
                                    $button_id = 'pay_with_' . $key;
                                @endphp
                            @elseif (
                                $key == 'payumoney' &&
                                    isset($adminPaymentSetting['payumoneysetting']) &&
                                    $adminPaymentSetting['payumoneysetting'] == 'on')
                                @php
                                    $route = 'payumoney.payment.init';
                                    $id = 'payumoney-payment-form';
                                    $button_type = 'submit';
                                    $payment_description = $adminPaymentSetting['payumoney_description'];
                                    $button_id = 'pay_with_' . $key;
                                @endphp
                            @elseif ($key == 'paytm' && isset($adminPaymentSetting['paytmsetting']) && $adminPaymentSetting['paytmsetting'] == 'on')
                                @php
                                    $route = 'paypaytm.payment';
                                    $id = 'paytm-payment-form';
                                    $button_type = 'submit';
                                    $payment_description = $adminPaymentSetting['paytm_description'];
                                    $button_id = 'pay_with_' . $key;
                                @endphp
                            @elseif (
                                $key == 'paystack' &&
                                    isset($adminPaymentSetting['paystacksetting']) &&
                                    $adminPaymentSetting['paystacksetting'] == 'on')
                                @php
                                    $route = 'paypaystack.payment';
                                    $id = 'paystack-payment-form';
                                    $button_type = 'button';
                                    $payment_description = $adminPaymentSetting['paystack_description'];
                                    $button_id = 'pay_with_' . $key;
                                @endphp
                            @elseif (
                                $key == 'cashfree' &&
                                    isset($adminPaymentSetting['cashfreesetting']) &&
                                    $adminPaymentSetting['cashfreesetting'] == 'on')
                                @php
                                    $route = 'cashfree.payment.prepare';
                                    $id = 'cashfree-payment-form';
                                    $button_type = 'submit';
                                    $payment_description = $adminPaymentSetting['cashfree_description'];
                                    $button_id = 'pay_with_' . $key;
                                @endphp
                            @elseif (
                                $key == 'flutterwave' &&
                                    isset($adminPaymentSetting['flutterwavesetting']) &&
                                    $adminPaymentSetting['flutterwavesetting'] == 'on')
                                @php
                                    $route = 'payflutterwave.payment';
                                    $id = 'flutterwave-payment-form';
                                    $button_type = 'button';
                                    $payment_description = $adminPaymentSetting['flutterwave_description'];
                                    $button_id = 'pay_with_' . $key;
                                @endphp
                            @elseif (
                                $key == 'coingate' &&
                                    isset($adminPaymentSetting['coingatesetting']) &&
                                    $adminPaymentSetting['coingatesetting'] == 'on')
                                @php
                                    $route = 'coingate.payment.prepare';
                                    $id = 'coingate-payment-form';
                                    $button_type = 'submit';
                                    $payment_description = $adminPaymentSetting['coingate_description'];
                                    $button_id = 'pay_with_' . $key;
                                @endphp
                            @elseif (
                                $key == 'mercado' &&
                                    isset($adminPaymentSetting['mercadosetting']) &&
                                    $adminPaymentSetting['mercadosetting'] == 'on')
                                @php
                                    $route = 'mercado.payment.prepare';
                                    $id = 'payment-form';
                                    $button_type = 'submit';
                                    $payment_description = $adminPaymentSetting['mercado_description'];
                                    $button_id = 'pay_with_' . $key;
                                @endphp
                            @elseif ($key == 'sspay' && isset($adminPaymentSetting['sspaysetting']) && $adminPaymentSetting['sspaysetting'] == 'on')
                                @php
                                    $route = 'sspay.payment.init';
                                    $id = 'payment-form';
                                    $button_type = 'submit';
                                    $payment_description = $adminPaymentSetting['sspay_description'];
                                    $button_id = 'pay_with_' . $key;
                                @endphp
                            @elseif (
                                $key == 'paytab' &&
                                    isset($adminPaymentSetting['paytabsetting']) &&
                                    $adminPaymentSetting['paytabsetting'] == 'on')
                                @php
                                    $route = 'admin.pay.with.paytab';
                                    $id = 'paytab-payment-form';
                                    $button_type = 'submit';
                                    $payment_description = $adminPaymentSetting['paytab_description'];
                                    $button_id = 'pay_with_' . $key;
                                @endphp
                            @elseif ($key == 'bkash' && isset($adminPaymentSetting['bkashsetting']) && $adminPaymentSetting['bkashsetting'] == 'on')
                                @php
                                    $route = 'bkash.pay.init';
                                    $id = 'bkash-payment-form';
                                    $button_type = 'submit';
                                    $payment_description = $adminPaymentSetting['bkash_description'];
                                    $button_id = 'bKash_button';
                                @endphp
                            @elseif (
                                $key == 'offline' &&
                                    isset($adminPaymentSetting['offlinesetting']) &&
                                    $adminPaymentSetting['offlinesetting'] == 'on')
                                @php
                                    $route = 'offline.payment.request';
                                    $id = 'offline-payment-form';
                                    $button_type = 'submit';
                                    $payment_description = 'Offline payment';
                                    $button_id = 'pay_with_' . $key;
                                @endphp
                            @endif
                            @php
                                if (array_key_first($paymentTypes) == $key) {
                                    $active = 'active show';
                                } else {
                                    $active = '';
                                }
                            @endphp

                            <div class="tab-pane fade {{ $active }}" id="payment{{ $key }}" role="tabpanel"
                                aria-labelledby="{{ str_replace(' ', '_', $key) }}-tab">
                                <div id="stripe_payment">
                                    <div class="card-header d-flex justify-content-between">
                                        <h5>{{ __('Payment Methods') }}</h5>
                                        <h5 class="text-muted">{{ __($paymenttype) }}</h5>
                                    </div>
                                    <div class="tab-pane {{ ($adminPaymentSetting['stripesetting'] == 'on' && !empty($adminPaymentSetting['stripe_key']) && !empty($adminPaymentSetting['stripe_secret'])) == 'on' ? 'active' : '' }}"
                                        id="stripe_payment">
                                        <form role="form" action="{{ route($route) }}" method="post"
                                            class="require-validation" id="{{ $id }}">
                                            @csrf
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <div class="text-black form-label">
                                                                {{ __('Description') }} :
                                                                {{ isset($payment_description) ? $payment_description : '' }}
                                                            </div>
                                                        </div>
                                                        @if ($key == 'paytm')
                                                            <div class="form-group">
                                                                <label for="mobile_number"
                                                                    class="form-label">{{ __('Mobile Number') }}</label>
                                                                <input type="number" id="mobile_number" required
                                                                    name="mobile_number" class="form-control"
                                                                    placeholder="{{ __('Enter mobile number') }}">
                                                            </div>
                                                        @endif
                                                        <div class="d-flex align-items-center">
                                                            <div class="form-group w-100">
                                                                <label for="paypal_coupon"
                                                                    class="form-label">{{ __('Coupon') }}</label>
                                                                <input type="text" id="stripe_coupon" name="coupon"
                                                                    class="form-control coupon"
                                                                    placeholder="{{ __('Enter coupon code') }}">
                                                            </div>
                                                            <div class="mt-4 form-group ms-3">
                                                                <a href="#" class="text-muted "
                                                                    data-bs-toggle="tooltip" title="{{ __('Apply') }}"><i
                                                                        class="ti ti-square-check btn-apply"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <div class="text-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                                    <button type="{{ $button_type }}" id="{{ $button_id }}"
                                                        class="btn btn-primary">{{ __('Pay Now') }}</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="{{ asset('vendor/jquery-form/jquery.form.js') }}"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="https://www.paypal.com/sdk/js?client-id={{ UtilityFacades::keysettings('paypal_sandbox_client_id', 1) }}"></script>
    <script src="https://checkout.flutterwave.com/v3.js"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    @if (UtilityFacades::keysettings('paytm_environment', 1) == 'production')
        <script type="application/javascript" crossorigin="anonymous" src="https:\\securegw.paytm.in\merchantpgpui\checkoutjs\merchants\{{ UtilityFacades::keysettings('paytm_merchant_id', 1) }}.js" ></script>
    @else
        <script type="application/javascript" crossorigin="anonymous" src="https:\\securegw-stage.paytm.in\merchantpgpui\checkoutjs\merchants\{{ UtilityFacades::keysettings('paytm_merchant_id', 1) }}.js" ></script>
    @endif
    <script id="myScript" src="https://scripts.sandbox.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout-sandbox.js"></script>
    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        });
        $(document).ready(function() {
            $(document).on('click', '.btn-apply', function() {
                var ele = $(this);
                var coupon = ele.closest('.row').find('.coupon').val();
                $.ajax({
                    url: '{{ route('apply.coupon') }}',
                    datType: 'json',
                    data: {
                        plan_id: '{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}',
                        coupon: coupon
                    },
                    success: function(data) {
                        if (data.final_price) {
                            $('.discount_price').text(data.discount_price);
                            $('.final-price').text(data.final_price);
                        }
                        $('#stripe_coupon, #paypal_coupon').val(coupon);
                        if (data != '') {
                            if (data.is_success == true) {
                                showToStr('Successfully!', data.message, 'success');
                            } else {
                                showToStr('Error!', data.message, 'danger');
                            }
                        } else {
                            showToStr('Error!', "{{ __('Coupon code required.') }}",'danger');
                        }
                    }
                })
            });
        });
        @if (isset($adminPaymentSetting['stripesetting']) && $adminPaymentSetting['stripesetting'] == 'on')
            $(document).on("click", "#pay_with_stripe", function() {
                $('#stripe-payment-form').ajaxForm(function(res) {
                    if (res.error) {
                        showToStr('Error!', res.error, 'danger');
                    }
                    const stripe = Stripe("{{ $adminPaymentSetting['stripe_key'] }}");
                    createCheckoutSession(res.plan_id, res.order_id, res.coupon, res.total_price).then(
                        function(data) {
                            if (data.sessionId) {
                                stripe.redirectToCheckout({
                                    sessionId: data.sessionId,
                                }).then(handleResult);
                            } else {
                                handleResult(data);
                            }
                        });
                });
            }).submit();
            const createCheckoutSession = function(plan_id, order_id, coupon, amount) {
                return fetch("{{ route('stripe.session') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify({
                        createCheckoutSession: 1,
                        plan_id: plan_id,
                        order_id: order_id,
                        coupon: coupon,
                        amount: amount,
                    }),
                }).then(function(result) {
                    return result.json();
                });
            };
            const handleResult = function(result) {
                if (result.error) {
                    showMessage(result.error.message);
                }
                setLoading(false);
            };
        @endif
        @if (isset($adminPaymentSetting['razorpaysetting']) && $adminPaymentSetting['razorpaysetting'] == 'on')
            $(document).on("click", "#pay_with_razorpay", function() {
                $('#razorpay-payment-form').ajaxForm(function(res) {
                    var razorPay_callback = '{{ url('/razorpay/transaction/callback') }}';
                    var totalAmount = res.total_price * 100;
                    var coupon_id = res.coupon;
                    var options = {
                        "key": "{{ $adminPaymentSetting['razorpay_key'] }}", // your Razorpay Key Id
                        "amount": totalAmount,
                        "name": res.plan_name,
                        "currency": res.currency,
                        "description": "",
                        "handler": function(response) {
                            window.location.href = razorPay_callback + '/' + response
                                .razorpay_payment_id +
                                '/' + coupon_id +
                                '/' + res.plan_id
                        },
                        "theme": {
                            "color": "#528FF0"
                        }
                    };
                    var rzp1 = new Razorpay(options);
                    rzp1.open();
                }).submit();
            });
        @endif
        @if (isset($adminPaymentSetting['paystacksetting']) && $adminPaymentSetting['paystacksetting'] == 'on')
            $(document).on("click", "#pay_with_paystack", function() {
                $('#paystack-payment-form').ajaxForm(function(res) {
                    var paystack_callback = "{{ url('/paystack/transaction/callback') }}";
                    var order_id = '{{ time() }}';
                    var coupon_id = res.coupon;
                    var handler = PaystackPop.setup({
                        key: '{{ $adminPaymentSetting['paystack_key'] }}',
                        email: res.email,
                        amount: res.total_price * 100,
                        currency: res.currency,
                        ref: 'pay_ref_id' + Math.floor((Math.random() * 1000000000) +
                            1
                        ), // generates a pseudo-unique reference. Please replace with a reference you generated. Or remove the line entirely so our API will generate one for you
                        metadata: {
                            custom_fields: [{
                                display_name: "Email",
                                variable_name: "email",
                                value: res.email,
                            }]
                        },
                        callback: function(response) {
                            window.location.href = paystack_callback + '/' + response
                                .transaction +
                                '/' + coupon_id +
                                '/' + res.plan_id
                        },
                        onClose: function() {
                            showToStr('Failed!',
                                'Transaction was not completed, window closed.',
                                'danger');
                            modal.close();
                        }
                    });
                    handler.openIframe();
                }).submit();
            });
        @endif

        @if (isset($adminPaymentSetting['flutterwavesetting']) && $adminPaymentSetting['flutterwavesetting'] == 'on')
            $(document).on("click", "#pay_with_flutterwave", function() {
                $('#flutterwave-payment-form').ajaxForm(function(res) {
                    var coupon_id = res.coupon;
                    var API_publicKey = '{{ $adminPaymentSetting['flutterwave_key'] }}';
                    var flutter_callback = "{{ url('/flutterwave/transaction/callback') }}";
                    const modal = FlutterwaveCheckout({
                        public_key: API_publicKey,
                        tx_ref: "titanic-48981487343MDI0NzMx",
                        amount: res.total_price,
                        currency: res.currency,
                        payment_options: "card, banktransfer, ussd",
                        callback: function(response) {
                            window.location.href = flutter_callback + '/' + response
                                .transaction_id +
                                '/' + coupon_id +
                                '/' + res.plan_id
                            modal.close();
                        },
                        onclose: function() {
                            // window.location.href = '/plans';
                            showToStr('Failed!',
                                'Transaction was not completed, window closed.',
                                'danger');
                            modal.close();
                        },
                        meta: {
                            consumer_id: res.plan_id,
                            consumer_mac: "92a3-912ba-1192a",
                        },
                        customer: {
                            email: res.email,
                            phone_number: '7421052101',
                            name: 'dqw',
                        },
                        customizations: {
                            title: res.plan_name,
                            description: "Payment for an awesome cruise",
                            logo: "https://www.logolynx.com/images/logolynx/22/2239ca38f5505fbfce7e55bbc0604386.jpeg",
                        },
                    });
                }).submit();
            });
        @endif
        @if (isset($adminPaymentSetting['paytmsetting']) && $adminPaymentSetting['paytmsetting'] == 'on')
            $(document).on("click", "#pay_with_paytm", function() {
                $('#paytm-payment-form').ajaxForm(function(res) {
                    if (res.errors) {
                        showToStr('Error!', res.errors, 'danger');
                    }
                    window.Paytm.CheckoutJS.init({
                        "root": "",
                        "flow": "DEFAULT",
                        "data": {
                            "orderId": res.orderId,
                            "token": res.txnToken,
                            "tokenType": "TXN_TOKEN",
                            "amount": res.amount,
                        },
                        handler: {
                            transactionStatus: function(data) {},
                            notifyMerchant: function notifyMerchant(eventName, data) {
                                if (eventName == "APP_CLOSED") {
                                    $('.paytm-pg-loader').hide();
                                    $('.paytm-overlay').hide();
                                }
                            }
                        }
                    }).then(function() {
                        window.Paytm.CheckoutJS.invoke();
                    });
                });
            }).submit();
        @endif
        @if (isset($adminPaymentSetting['bkashsetting']) && $adminPaymentSetting['bkashsetting'] == 'on')
            var accessToken = '';
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{!! route('bkash.token') !!}",
                    type: 'POST',
                    contentType: 'application/json',
                    success: function(data) {
                        accessToken = JSON.stringify(data);
                    },
                    error: function() {
                        console.log('error');
                    }
                });
                var paymentConfig = {
                    createCheckoutURL: "{!! route('bkash.createpayment') !!}",
                    executeCheckoutURL: "{!! route('bkash.executepayment') !!}"
                };
                var paymentRequest;
                paymentRequest = {
                    amount: 20.00,
                    intent: 'sale',
                    invoice: 21
                };
                $('#bkash-payment-form').ajaxForm(function(res) {
                    paymentRequest.amount = parseFloat(res.total_price); // Update the amount
                    paymentRequest.invoice = parseInt(res.order_id); // Update the invoice
                    paymentRequest.intent = 'sale';
                });
                bKash.init({
                    paymentMode: 'checkout',
                    paymentRequest: paymentRequest,
                    createRequest: function(request) {
                        $.ajax({
                            url: paymentConfig.createCheckoutURL + "?amount=" + paymentRequest
                                .amount + "&invoice=" + paymentRequest.invoice,
                            type: 'GET',
                            contentType: 'application/json',
                            success: function(data) {
                                var obj = JSON.parse(data);
                                if (data && obj.paymentID != null) {
                                    paymentID = obj.paymentID;
                                    bKash.create().onSuccess(obj);
                                } else {
                                    bKash.create().onError();
                                }
                            },
                            error: function() {
                                bKash.create().onError();
                            }
                        });
                    },
                    executeRequestOnAuthorization: function() {
                        $.ajax({
                            url: paymentConfig.executeCheckoutURL + "?paymentID=" + paymentID +
                                "&order_id=" +
                                paymentRequest.invoice,
                            type: 'GET',
                            contentType: 'application/json',
                            success: function(data) {
                                data = JSON.parse(data);
                                if (data && data.paymentID != null) {
                                    window.location.href = "{!! route('plans.index') !!}";
                                } else {
                                    bKash.execute().onError();
                                }
                            },
                            error: function() {
                                bKash.execute().onError();
                            }
                        });
                    }
                });
            });

            function callReconfigure(val) {
                bKash.reconfigure(val);
            }

            function clickPayButton() {
                $("#bKash_button").trigger('click');
            }
        @endif
    </script>
@endpush
