@extends('layouts.main-landing')
@section('title', __('Subdcription'))
@section('content')
    <section class="blog-page-banner"
        style="background-image: url({{ Storage::url(Utility::keysettings('background_image', 1)) }});" width="100% "
        height="100%">
        <div class="container">
            <div class="common-banner-content">
                <div class="section-title">
                    <h2> {{ __('Latest Subscription') }} </h2>
                </div>
                <ul class="back-cat-btn d-flex align-items-center justify-content-center">
                    <li><a href="{{ route('home') }}">{{ __('Home') }}</a>
                        <span>/</span>
                    </li>
                    <li>{{ __('Subdcription') }}</li>
                </ul>
            </div>
        </div>
    </section>
    <section class="plan-content">
        <div class="container">
            <div class="mt-2 row">
                <div class="col-lg-12">
                    <div class="mt-2 card">
                        <div class="card-header">
                            <h5>{{ __('Billing Address') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row justify-content-between">
                                <div class="mb-3 col-md-6 col-12 mb-md-0">
                                    <div class="flex-equal">
                                        <table class="table m-0 fs-6 gs-0 gy-2 gx-2">
                                            <tr>
                                                <td class="text-muted">{{ __('Bill to') }}:</td>
                                                <td>
                                                    <span
                                                        class="text-gray-800 text-hover-primary">{{ $requestUser->email }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">{{ __('Name') }}:</td>
                                                <td class="">{{ $requestUser->name }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="flex-equal">
                                        <table class="table m-0 fs-6 gs-0 gy-2 gx-2">
                                            <tr>
                                                <td class="text-muted">{{ __('Subscription plan') }}:</td>
                                                <td class="">
                                                    <span
                                                        class="text-gray-800 text-hover-primary">{{ $plan->name }}</span>
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
                                                <td class="final-prize">
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
                                    <h4>{{ 'Please contact to super admin for enable payments.' }}</h4>
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
                                            $route = 'pre.stripe.pending';
                                            $id = 'stripe-payment-form';
                                            $button_type = 'submit';
                                            $button_id = 'pay_with_' . $key;
                                        @endphp
                                    @elseif (
                                        $key == 'paypal' &&
                                            $adminPaymentSetting['paypalsetting'] == 'on' &&
                                            !empty($adminPaymentSetting['paypal_client_id']) &&
                                            !empty($adminPaymentSetting['paypal_client_secret']))
                                        @php
                                            $route = 'processTransaction';
                                            $id = 'paypal-payment-form';
                                            $button_type = 'submit';
                                            $button_id = 'pay_with_' . $key;
                                        @endphp
                                    @elseif (
                                        $key == 'razorpay' &&
                                            isset($adminPaymentSetting['razorpaysetting']) &&
                                            $adminPaymentSetting['razorpaysetting'] == 'on')
                                        @php
                                            $route = 'paysrazorpay.payment';
                                            $id = 'razorpay-payment-form';
                                            $button_type = 'button';
                                            $button_id = 'pay_with_' . $key;
                                        @endphp
                                    @elseif (
                                        $key == 'payumoney' &&
                                            isset($adminPaymentSetting['payumoneysetting']) &&
                                            $adminPaymentSetting['payumoneysetting'] == 'on')
                                        @php
                                            $route = 'front.payumoney.payment.init';
                                            $id = 'payumoney-payment-form';
                                            $button_type = 'submit';
                                            $payment_description = $adminPaymentSetting['payumoney_description'];
                                            $button_id = 'pay_with_' . $key;
                                        @endphp
                                    @elseif ($key == 'paytm' && isset($adminPaymentSetting['paytmsetting']) && $adminPaymentSetting['paytmsetting'] == 'on')
                                        @php
                                            $route = 'paytm.payment';
                                            $id = 'paytm-payment-form';
                                            $button_type = 'submit';
                                            $button_id = 'pay_with_' . $key;
                                        @endphp
                                    @elseif (
                                        $key == 'paystack' &&
                                            isset($adminPaymentSetting['paystacksetting']) &&
                                            $adminPaymentSetting['paystacksetting'] == 'on')
                                        @php
                                            $route = 'paymentpaystack.payment';
                                            $id = 'paystack-payment-form';
                                            $button_type = 'button';
                                            $button_id = 'pay_with_' . $key;
                                        @endphp
                                    @elseif (
                                        $key == 'cashfree' &&
                                            isset($adminPaymentSetting['cashfreesetting']) &&
                                            $adminPaymentSetting['cashfreesetting'] == 'on')
                                        @php
                                            $route = 'cashfree.prepare';
                                            $id = 'cashfree-payment-form';
                                            $button_type = 'submit';
                                            $button_id = 'pay_with_' . $key;
                                        @endphp
                                    @elseif (
                                        $key == 'flutterwave' &&
                                            isset($adminPaymentSetting['flutterwavesetting']) &&
                                            $adminPaymentSetting['flutterwavesetting'] == 'on')
                                        @php
                                            $route = 'paysflutterwave.payment';
                                            $id = 'flutterwave-payment-form';
                                            $button_type = 'button';
                                            $button_id = 'pay_with_' . $key;
                                        @endphp
                                    @elseif (
                                        $key == 'coingate' &&
                                            isset($adminPaymentSetting['coingatesetting']) &&
                                            $adminPaymentSetting['coingatesetting'] == 'on')
                                        @php
                                            $route = 'coingate.payment';
                                            $id = 'coingate-payment-form';
                                            $button_type = 'submit';
                                            $button_id = 'pay_with_' . $key;
                                        @endphp
                                    @elseif (
                                        $key == 'mercado' &&
                                            isset($adminPaymentSetting['mercadosetting']) &&
                                            $adminPaymentSetting['mercadosetting'] == 'on')
                                        @php
                                            $route = 'mercadopago.payment';
                                            $id = 'mercado-payment-form';
                                            $button_type = 'submit';
                                            $button_id = 'pay_with_' . $key;
                                        @endphp
                                    @elseif ($key == 'sspay' && isset($adminPaymentSetting['sspaysetting']) && $adminPaymentSetting['sspaysetting'] == 'on')
                                        @php
                                            $route = 'sspay.init';
                                            $id = 'sspay-payment-form';
                                            $button_type = 'submit';
                                            $button_id = 'pay_with_' . $key;
                                        @endphp
                                    @elseif (
                                        $key == 'paytab' &&
                                            isset($adminPaymentSetting['paytabsetting']) &&
                                            $adminPaymentSetting['paytabsetting'] == 'on')
                                        @php
                                            $route = 'plan.pay.with.paytab';
                                            $id = 'paytab-payment-form';
                                            $button_type = 'submit';
                                            $button_id = 'pay_with_' . $key;
                                        @endphp
                                    @elseif ($key == 'bkash' && isset($adminPaymentSetting['bkashsetting']) && $adminPaymentSetting['bkashsetting'] == 'on')
                                        @php
                                            $route = 'bkash.pay.payment.init';
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
                                            $route = 'offline.payment.entry';
                                            $id = 'offline-payment-form';
                                            $button_type = 'submit';
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
                                    <div class="tab-pane fade {{ $active }}" id="payment{{ $key }}"
                                        role="tabpanel" aria-labelledby="{{ str_replace(' ', '_', $key) }}-tab">
                                        <div class="card-header d-flex justify-content-between">
                                            <h5>{{ $paymenttype }}</h5>
                                        </div>
                                        <form role="form" action="{{ route($route) }}" method="post"
                                            class="w3-container w3-display-middle w3-card-4" id="{{ $id }}">
                                            @csrf
                                            <input type="hidden" name="plan_id"
                                                value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="mt-4 col-md-12">
                                                        @if ($key == 'paytm')
                                                            <div class="form-group">
                                                                <label for="mobile_number"
                                                                    class="form-label">{{ __('Mobile Number') }}</label>
                                                                <input type="number" id="mobile_number" required
                                                                    name="mobile_number" class="form-control"
                                                                    placeholder="{{ __('Enter mobile number') }}">
                                                            </div>
                                                        @endif
                                                        <div class="d-flex coupne-code align-items-center">
                                                            <div class="form-group">
                                                                <label for="paypal_coupon"
                                                                    class="form-label">{{ __('Coupon') }}</label>
                                                                <input type="text" id="stripe_coupon" name="coupon"
                                                                    class="form-control coupon"
                                                                    placeholder="{{ __('Enter coupon code') }}">
                                                            </div>
                                                            <div class="mt-4 form-group ms-3">
                                                                <a href="#" class="text-muted"
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
                                                    <button type="{{ $button_type }}" id="{{ $button_id }}"
                                                        class="btn btn-primary">{{ __('Pay Now') }}</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @endforeach

                            </div>
                        </div>

                    </div>
                </div>
            </div>
    </section>
@endsection
@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/landing-page2/css/payment.css') }}" id="main-style-link">
@endpush
@push('script')
    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-form/jquery.form.js') }}"></script>
    <script src="https://checkout.flutterwave.com/v3.js"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    @if (Utility::keysettings('paytm_environment', 1) == 'production')
        <script type="application/javascript" crossorigin="anonymous" src="https:\\securegw.paytm.in\merchantpgpui\checkoutjs\merchants\{{ Utility::keysettings('paytm_merchant_id', 1) }}.js" ></script>
    @else
        <script type="application/javascript" crossorigin="anonymous" src="https:\\securegw-stage.paytm.in\merchantpgpui\checkoutjs\merchants\{{ Utility::keysettings('paytm_merchant_id', 1) }}.js" ></script>
    @endif
    <script id="myScript" src="https://scripts.sandbox.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout-sandbox.js">
    </script>
    <script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var genericExamples = document.querySelectorAll('[data-trigger]');
            for (i = 0; i < genericExamples.length; ++i) {
                var element = genericExamples[i];
                new Choices(element, {
                    placeholderValue: 'This is a placeholder set in the config',
                    searchPlaceholderValue: 'This is a search placeholder',
                });
            }
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
                            $('.final-prize').text(data.final_price);
                            $('.discount_price').text(data.discount_price);
                        }
                        $('#stripe_coupon, #paypal_coupon').val(coupon);
                        if (data != '') {
                            if (data.is_success == true) {
                                showToStr('Successfully!', data.message, 'success');
                            } else {
                                showToStr('Error!', data.message, 'danger');
                            }
                        } else {
                            showToStr('Error!', "{{ __('Coupon code required.') }}",
                                'danger');
                        }
                    }
                })
            });
            @if (isset($adminPaymentSetting['stripesetting']) && $adminPaymentSetting['stripesetting'] == 'on')
                $(document).on("click", "#pay_with_stripe", function() {
                    $('#stripe-payment-form').ajaxForm(function(res) {
                        if (res.error) {
                            showToStr('Error!', res.error, 'danger');
                        }
                        const stripe = Stripe("{{ $adminPaymentSetting['stripe_key'] }}");
                        createCheckoutSession(res.plan_id, res.order_id, res.coupon, res
                            .total_price, res.request_user_id).then(function(data) {
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
                const createCheckoutSession = function(plan_id, order_id, coupon, amount, request_user_id) {
                    return fetch("{{ route('pre.stripe.session') }}", {
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
                            request_user_id: request_user_id,
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
                        var razorPay_callback = '{{ url('/paysrazorpay/callback') }}';
                        var totalAmount = res.total_price * 100;
                        var coupon_id = res.coupon;
                        var options = {
                            "key": "{{ $adminPaymentSetting['razorpay_key'] }}", // your Razorpay Key Id
                            "amount": totalAmount,
                            "name": res.plan_name,
                            "currency": res.currency,
                            "description": "",
                            "handler": function(response) {
                                window.location.href = razorPay_callback + '/' + res
                                    .order_id + '/' +
                                    response.razorpay_payment_id +
                                    '/' + res.request_user_id +
                                    '/' + coupon_id
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
                        var paystack_callback = "{{ url('/paymentpaystack/callback/') }}";
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
                                window.location.href = paystack_callback + '/' +
                                    res.order_id + '/' +
                                    response
                                    .transaction +
                                    '/' + res.request_user_id +
                                    '/' + coupon_id
                            },
                            onClose: function() {
                                alert('window closed');
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
                        var flutter_callback = "{{ url('/paysflutterwave/callback') }}";
                        const modal = FlutterwaveCheckout({
                            public_key: API_publicKey,
                            tx_ref: "titanic-48981487343MDI0NzMx",
                            amount: res.total_price,
                            currency: res.currency,
                            payment_options: "card, banktransfer, ussd",
                            callback: function(response) {
                                window.location.href = flutter_callback + '/' + res
                                    .order_id + '/' +
                                    response.transaction_id +
                                    '/' + res.request_user_id +
                                    '/' + coupon_id
                                modal.close();
                            },
                            onclose: function(incomplete) {
                                modal.close();
                                showToStr('Failed!',
                                    'Transaction was not completed, window closed.',
                                    'danger');
                            },
                            meta: {
                                consumer_id: res.plan_id,
                                consumer_mac: "92a3-912ba-1192a",
                            },
                            customer: {
                                email: res.email,
                                phone_number: '7421052101',
                                name: res.plan_name,
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
                        url: "{!! route('bkash.payment.token') !!}",
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
                        createCheckoutURL: "{!! route('bkash.payment.createpayment') !!}",
                        executeCheckoutURL: "{!! route('bkash.payment.executepayment') !!}"
                    };
                    var paymentRequest, request_user_id;
                    paymentRequest = {
                        amount: 20.00,
                        intent: 'sale',
                        invoice: 21
                    };
                    $('#bkash-payment-form').ajaxForm(function(res) {
                        paymentRequest.amount = parseFloat(res.total_price); // Update the amount
                        paymentRequest.invoice = parseInt(res.order_id); // Update the invoice
                        paymentRequest.intent = 'sale';
                        request_user_id = res.request_user_id;
                    });
                    bKash.init({
                        paymentMode: 'checkout',
                        paymentRequest: paymentRequest,
                        createRequest: function(request) {
                            $.ajax({
                                url: paymentConfig.createCheckoutURL + "?amount=" +
                                    paymentRequest
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
                                url: paymentConfig.executeCheckoutURL + "?paymentID=" +
                                    paymentID +
                                    "&order_id=" +
                                    paymentRequest.invoice + "&request_user_id=" +
                                    request_user_id + "&coupon=" +
                                    coupon,
                                type: 'GET',
                                contentType: 'application/json',
                                success: function(data) {
                                    data = JSON.parse(data);
                                    if (data && data.paymentID != null) {
                                        window.location.href =
                                            "{!! route('landingpage') !!}";
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
        });
    </script>
@endpush
