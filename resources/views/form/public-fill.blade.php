@extends('layouts.form')
@section('title', __('Fill'))
@section('background_image', $backgroundImage)
@section('content')
    <div class="p-0 main-content">
        <section class="section">
            @include('form.public-multi-form')
        </section>
    </div>
@endsection
@push('style')
    <link href="{{ asset('assets/jqueryform/css/jquery.rateyo.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('vendor/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/formTheme/form.css') }}">

    <style>
        .bg-back {
            background-image: url({{ Storage::url($form->theme_background_image) }}) !important;
            background-size: cover !important;
            background-repeat: no-repeat !important;
        }

        .section-body.newform-2 {
            background-image: url({{ Storage::url($form->theme_background_image) }});
        }

        .bg-back .section-body.newform-2 {
            background-image: unset;
            background-size: unset;
            background-repeat: unset;
        }

        .tab {
            display: none;
        }

        #prevBtn {
            background-color: #bbbbbb;
        }

        .step {
            height: 15px;
            width: 15px;
            margin: 0 2px;
            background-color: #bbbbbb;
            border: none;
            border-radius: 50%;
            display: inline-block;
            opacity: 0.5;
        }

        .step.active {
            opacity: 1;
        }

        .step.finish {
            background-color: #394EEA;
        }
    </style>
@endpush
@push('script')
    <script src="{{ asset('vendor/jqueryform/js/jquery.rateyo.min.js') }}"></script>
    <script src="{{ asset('vendor/js/jquery.payment.min.js') }}"></script>
    <script src="{{ asset('vendor/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>

    <script src="{{ asset('vendor/jqueryform/js/jquery.rateyo.min.js') }}"></script>
    <script src="{{ asset('vendor/js/jquery.payment.min.js') }}"></script>
    <script src="{{ asset('vendor/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>

    @if (Utility::getsettings('PAYTM_ENVIRONMENT') == 'production')
        <script type="application/javascript" crossorigin="anonymous" src="https:\\securegw.paytm.in\merchantpgpui\checkoutjs\merchants\{{ Utility::getsettings('PAYTM_MERCHANT_ID') }}.js" ></script>
    @else
        <script type="application/javascript" crossorigin="anonymous" src="https:\\securegw-stage.paytm.in\merchantpgpui\checkoutjs\merchants\{{ Utility::getsettings('PAYTM_MERCHANT_ID') }}.js" ></script>
    @endif

    @if ($form->payment_status == 1)
        @if ($form->payment_type == 'razorpay')
            <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        @elseif($form->payment_type == 'paypal')
            <script
                src="https://www.paypal.com/sdk/js?client-id={{ Utility::getsettings('PAYPAL_SANDBOX_CLIENT_ID') }}&currency={{ $form->currency_name }}">
            </script>
        @elseif($form->payment_type == 'flutterwave')
            <script src="https://checkout.flutterwave.com/v3.js"></script>
        @elseif($form->payment_type == 'paystack')
            <script src="https://js.paystack.co/v1/inline.js"></script>
        @endif
    @endif
    <script>
        $(document).ready(function() {
            var theme = '{{ $form->theme }}';
            var themeColor = '{{ $form->theme_color }}';
            $('body').removeClass();
            $('body').addClass(themeColor);
            if (theme === 'theme2') {
                $('.section-body').addClass('newform-1');
            } else if (theme === 'theme3') {
                $('body').addClass('bg-back');
                $('.section-body').addClass('newform-2');
            } else if (theme === 'theme4') {
                $('.section-body').addClass('newform-3');
            } else if (theme === 'theme5') {
                $('.section-body').addClass('newform-3 circle');
            } else if (theme === 'theme6') {
                $('.section-body').addClass('newform-3 circle-custom');
            } else if (theme === 'theme7') {
                $('.section-body').addClass('newform-1 newform-4');
            } else if (theme === 'theme8') {
                $('.section-body').addClass('newform-5');
            }
        });
        var form_value_id = $('#form_value_id').val();
        var SITEURL = '{{ URL::to('') }}';
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        ('restrictNumeric');
        $('.cc-number').payment('formatCardNumber');
        $('.cc-exp').payment('formatCardExpiry');
        $('.cc-cvc').payment('formatCardCVC');
        $.fn.toggleInputError = function(erred) {
            this.parent('.form-group').toggleClass('has-error', erred);
            return this;
        };
        var currentTab = 0; // Current tab is set to be the first tab (0)
        showTab(currentTab); // Display the current tab
        function showTab(n) {
            // This function will display the specified tab of the form...
            var x = document.getElementsByClassName("tab");
            x[n].style.display = "block";
            //... and fix the Previous/Next buttons:
            if (n == 0) {
                document.getElementById("prevBtn").style.display = "none";
            } else {
                document.getElementById("prevBtn").style.display = "inline";
            }
            if (n == (x.length - 1)) {
                $('.cap').show();
                $('.strip').show();
                $('.razorpay').show();
                $('.paypal').show();
                $('.paytm').show();
                $('.flutterwave').show();
                $('.paystack').show();
                $('payumoney').show();
                $('.coingate').show();
                $('.mercado').show();
                document.getElementById("nextBtn").innerHTML = "Submit";
            } else {
                $('.cap').hide();
                $('.strip').hide();
                $('.razorpay').hide();
                $('.paypal').hide();
                $('.paytm').hide();
                $('.flutterwave').hide();
                $('.paystack').hide();
                $('.payumoney').hide();
                $('.coingate').hide();
                $('.mercado').hide();
                document.getElementById("nextBtn").innerHTML = "Next";
            }
            //... and run a function that will display the correct step indicator:
            fixStepIndicator(n)
        }

        function make_payment() {
            var formData = new FormData($('#fill-form')[0]);
            if (form_value_id == '') {
                @if ($form->payment_status == 1)
                    @if ($form->payment_type == 'stripe')
                        stripe.createToken(card).then(function(result) {
                            if (result.error) {
                                // Inform the user if there was an error
                                var errorElement = document.getElementById('card-errors');
                                errorElement.textContent = result.error.message;
                            } else {
                                formData.append('stripeToken', result.token.id);
                            }
                        }).then(function() {
                            submitForm(formData);
                        });
                    @elseif ($form->payment_type == 'paytm')
                        var amount = '{{ $form->amount }}';
                        var name = '{{ $form->title }}';
                        var currency = '{{ $form->currency_name }}';
                        var form_id = '{{ $form->id }}';
                        var email = '{{ $form->email }}';
                        var succes_msg = '{{ $form->success_msg }}';
                        $.ajax({
                            type: 'POST',
                            url: "{{ route('paymentpaytm.payment') }}",
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'amount': amount,
                                'name': name,
                                'email': email,
                                'mobile': '123456789',
                                'succes_msg': succes_msg,
                                'form_id': form_id,
                            },
                            success: function(data) {
                                $('.paytm-pg-loader').show();
                                $('.paytm-overlay').show();

                                if (data.txnToken == "") {
                                    showToStr('Failed!', data.message, 'danger',
                                        '{{ asset('assets/images/notification/high_priority-48.png') }}',
                                        4000);
                                    $('.paytm-pg-loader').hide();
                                    $('.paytm-overlay').hide();
                                    return false;
                                }
                                invokeBlinkCheckoutPopup(data.orderId, data.txnToken, data.amount)
                            }
                        });

                        function invokeBlinkCheckoutPopup(orderId, txnToken, amount) {
                            window.Paytm.CheckoutJS.init({
                                "root": "",
                                "flow": "DEFAULT",
                                "data": {
                                    "orderId": orderId,
                                    "token": txnToken,
                                    "tokenType": "TXN_TOKEN",
                                    "amount": amount,
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
                                formData.append('payment_id', orderId);
                                submitForm(formData);
                            });
                        }
                    @elseif ($form->payment_type == 'flutterwave')
                        var amount = '{{ $form->amount }}';
                        var name = '{{ $form->title }}';
                        var email = '{{ $form->email }}';
                        var currency = '{{ $form->currency_name }}';
                        var form_id = '{{ $form->id }}';
                        var plan_name = '{{ $plans->name }}';
                        const modal = FlutterwaveCheckout({
                            public_key: "{{ Utility::getsettings('FLW_PUBLIC_KEY') }}",
                            tx_ref: "titanic-48981487343MDI0NzMx",
                            amount: amount,
                            currency: currency,
                            payment_options: "card, banktransfer, ussd",
                            callback: function(payment) {
                                // Send AJAX verification request to backend
                                formData.append('payment_id', payment.transaction_id);
                                modal.close();
                                submitForm(formData);
                            },
                            onclose: function(incomplete) {
                                modal.close();
                                showToStr('Failed!', 'Transaction was not completed, window closed.',
                                    'danger',
                                    '{{ asset('assets/images/notification/high_priority-48.png') }}', 4000);
                            },
                            meta: {
                                consumer_id: form_id,
                                consumer_mac: "92a3-912ba-1192a",
                            },
                            customer: {
                                email: email,
                                phone_number: "08102909304",
                                name: name,
                            },
                            customizations: {
                                title: plan_name,
                                description: "Payment for an awesome cruise",
                                logo: "https://www.logolynx.com/images/logolynx/22/2239ca38f5505fbfce7e55bbc0604386.jpeg",
                            },
                        });
                    @elseif ($form->payment_type == 'paystack')
                        var amount = '{{ $form->amount }}';
                        var name = '{{ $form->title }}';
                        var email = '{{ $form->email }}';
                        var currency = '{{ $form->currency_name }}';
                        var form_id = '{{ $form->id }}';
                        var handler = PaystackPop.setup({
                            key: "{{ Utility::getsettings('PAYSTACK_PUBLIC_KEY') }}", // Replace with your public key
                            email: email,
                            amount: (amount *
                                100
                            ), // the amount value is multiplied by 100 to convert to the lowest currency unit
                            currency: currency, // Use GHS for Ghana Cedis or USD for US Dollars
                            ref: '{{ Str::random(10) }}', // Replace with a reference you generated
                            callback: function(response) {
                                //this happens after the payment is completed successfully
                                formData.append('payment_id', response.transaction);
                                var reference = response.reference;
                                showToStr('Done!', 'Payment complete! Reference: ' + reference, 'success',
                                    '{{ asset('assets/images/notification/ok-48.png') }}', 4000);
                                submitForm(formData);
                            },
                            onClose: function() {
                                showToStr('Failed!', 'Transaction was not completed, window closed.',
                                    'danger',
                                    '{{ asset('assets/images/notification/high_priority-48.png') }}', 4000);
                            },
                        });
                        handler.openIframe();
                    @elseif ($form->payment_type == 'coingate')
                        var amount = '{{ $form->amount }}';
                        var currency = '{{ $form->currency_name }}';
                        var form_id = '{{ $form->id }}';
                        var created_by = '{{ $form->created_by }}';
                        $('#cg_currency').val(currency);
                        $('#cg_amount').val(amount);
                        $('#cg_form_id').val(form_id);
                        $('#cg_created_by').val(created_by);
                        $('#coingate_payment_frms').submit();
                        submitForm(formData);
                    @elseif ($form->payment_type == 'mercado')
                        var amount = '{{ $form->amount }}';
                        var currency = '{{ $form->currency_name }}';
                        var form_id = '{{ $form->id }}';
                        var created_by = '{{ $form->created_by }}';
                        $('#mercado_currency').val(currency);
                        $('#mercado_amount').val(amount);
                        $('#mercado_form_id').val(form_id);
                        $('#mercado_created_by').val(created_by);
                        $('#mercado_payment_frms').submit();
                        submitForm(formData);
                    @elseif ($form->payment_type == 'razorpay')
                        var amount = '{{ $form->amount }}';
                        var name = '{{ $form->title }}';
                        var currency = '{{ $form->currency_name }}';
                        var form_id = '{{ $form->id }}';
                        var data = {
                            "_token": "{{ csrf_token() }}",
                            'price': amount,
                            'name': name,
                            'currency': currency,
                            'form_id': form_id,
                        }
                        var options = {
                            "key": "{{ Utility::getsettings('RAZORPAY_KEY') }}",
                            "amount": (amount * 100),
                            "name": name,
                            'currency': currency,
                            "description": "",
                            "image": '',
                            "handler": function(response) {
                                formData.append('payment_id', response.razorpay_payment_id);
                                submitForm(formData);
                                '{{ Crypt::encrypt(['payment_id' => ',response.razorpay_payment_id,', 'plan_id' => 'plan_id', 'request_user_id' => 'user_id', 'order_id' => 'order_id', 'type' => 'razorpay']) }}';
                                // window.location.href = SITEURL + '/' + 'pre-payment-success/' + data;
                            },
                            "theme": {
                                "color": "#528FF0"
                            }
                        };
                        // setLoading(true);
                        var rzp1 = new Razorpay(options);
                        rzp1.open();
                        // e.preventDefault();
                    @else

                        submitForm(formData);
                    @endif
                @else
                    submitForm(formData);
                @endif
            } else {
                submitForm(formData);
            }
        }

        function nextPrev(n) {
            $('.step-' + currentTab).find('.tel').each(function() {
                if ($(this).attr('type') == 'tel') {
                    var tel = $(this).val();
                    var filter = /^\d*(?:\.\d{1,2})?$/;
                    if (filter.test(tel)) {
                        valid = true;
                    } else {
                        valid = false;
                        $(this).addClass('is-invalid');
                        return false;
                    }
                }
            });
            $('.step-' + currentTab).find('.email').each(function() {
                if ($(this).attr('type') == 'email') {
                    var emailStr = $(this).val();
                    var regex = /^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i;
                    if (regex.test(emailStr)) {
                        valid = true;
                    } else {
                        $(this).addClass('is-invalid');
                        valid = false;
                        return false;
                    }
                }
            });
            // This function will figure out which tab to display
            var x = document.getElementsByClassName("tab");
            // Exit the function if any field in the current tab is is-invalid:
            if (n == 1 && !validateForm()) return false;
            // Hide the current tab:
            $('.tab').hide();
            // x[currentTab].style.display = "none";
            // Increase or decrease the current tab by 1:
            currentTab = currentTab + n;
            // if you have reached the end of the form...
            if (currentTab >= x.length) {
                // ... the form gets submitted:
                var formData = new FormData($('#fill-form')[0]);
                var $this = $("#nextBtn");
                var loadingText = '<i class="fa fa-spinner fa-spin"></i> Submiting form';
                if ($("#nextBtn").html() !== loadingText) {
                    $this.data('original-text', $("#nextBtn").html());
                    $this.html(loadingText);
                }
                @if ($form->payment_type == 'paypal')
                    if ($('#payment_id').val() == '') {
                        var errorElement = document.getElementById('paypal-errors');
                        iziToast.error({
                            title: 'Error!',
                            message: "{{ 'Please make payment' }}",
                            position: 'topRight'
                        });
                        $('#nextBtn').removeAttr('disabled');
                        $('#nextBtn').html('Submit')
                        showTab(n);
                        return false;
                    }
                @endif
                make_payment();
                setLoading(false);
                // $("#fill-form").submit();
                return false;
            }
            // Otherwise, display the correct tab:
            showTab(currentTab);
        }

        function validateForm() {
            var check = [];
            $('.step-' + currentTab).find('.required').each(function() {
                var name = $(this).attr('name');
                if ($(this).val() == "") {
                    $(this).addClass('is-invalid');
                    check.push(false);
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).addClass('is-valid');
                    check.push(true);
                }
                if ($(this).attr('type') == 'hidden') {
                    if ($(this).parents('.signature-pad-body').length) {
                        if ($(this).val() == "") {
                            $(this).parents('.signature-pad-body').find('.signaturePad').addClass('is-invalid');
                            $(this).parents('.signature-pad-body').find('.signaturePad').removeClass('is-valid');
                            showToStr('Error!', '{{ __('Please save your signature.') }}', 'danger',
                                '{{ asset('assets/images/notification/high_priority-48.png') }}', 4000);
                            check.push(false);
                        } else {
                            $(this).parents('.signature-pad-body').find('.signaturePad').addClass('is-valid');
                            $(this).parents('.signature-pad-body').find('.signaturePad').removeClass('is-invalid');
                            check.push(true);
                        }
                    }
                    if ($(this).parents('.cam-buttons').length) {
                        var videoContainer = $(this).parents('.cam-buttons');
                        var videoCam = videoContainer.find('.video_cam');
                        if (videoContainer.find('input[name="media"]').val() == "") {
                            videoCam.addClass('is-invalid');
                            videoCam.removeClass('is-valid');
                            showToStr('Error!', '{{ __('Video recording field is required.') }}', 'danger',
                                '{{ asset('assets/images/notification/high_priority-48.png') }}', 4000);
                            check.push(false);
                        } else {
                            videoCam.addClass('is-valid');
                            videoCam.removeClass('is-invalid');
                            check.push(true);
                        }
                    }
                    if ($(this).parents('.selfie_screen').length) {
                        var videoContainer = $(this).parents('.selfie_screen');
                        var videoCam = videoContainer.find('.selfie_photo');
                        if (videoContainer.find('input[name="image"]').val() == "") {
                            videoCam.addClass('is-invalid');
                            videoCam.removeClass('is-valid');
                            showToStr('Error!', '{{ __('This selfie field is required.') }}', 'danger',
                                '{{ asset('assets/images/notification/high_priority-48.png') }}', 4000);
                            check.push(false);
                        } else {
                            videoCam.addClass('is-valid');
                            videoCam.removeClass('is-invalid');
                            check.push(true);
                        }
                    }
                }
                if ($(this).attr('type') == 'email') {
                    var emailStr = $(this).val();
                    var regex = /^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i;
                    if (regex.test(emailStr)) {
                        $(this).removeClass('is-invalid');
                        $(this).addClass('is-valid');
                        check.push(true);
                    } else {
                        $(this).addClass('is-invalid');
                        check.push(false);
                    }
                }
                if ($(this).attr('type') == 'tel') {
                    var tel = $(this).val();
                    var filter = /^\d*(?:\.\d{1,2})?$/;
                    if (filter.test(tel)) {
                        $(this).removeClass('is-invalid');
                        $(this).addClass('is-valid');
                        check.push(true);
                    } else {
                        $(this).addClass('invalid');
                        check.push(false);
                    }
                }
                if ($(this).attr('type') == 'radio') {
                    if ($('input[name="' + name + '"]:checked').length <= 0) {
                        $(this).addClass('is-invalid');
                        $('.required-radio').html('Select any one');
                        check.push(false);
                    } else {
                        $(this).removeClass('is-invalid');
                        $(this).addClass('is-valid');
                        $('.required-radio').html('');
                        check.push(true);
                    }
                }
                if ($(this).attr('type') == 'checkbox') {
                    if ($('input[name="' + name + '"]:checked').length <= 0) {
                        $(this).addClass('is-invalid');
                        $('.required-checkbox').html('Select any one');
                        check.push(false);
                    } else {
                        $(this).removeClass('is-invalid');
                        $(this).addClass('is-valid');
                        $('.required-checkbox').html('');
                        check.push(true);
                    }
                }
                if ($(this).attr('type') == 'number') {
                    var numval = parseInt($(this).val());
                    var min = parseInt($(this).attr('min'));
                    var max = parseInt($(this).attr('max'));
                    if ($(this).val() == "") {
                        $(this).addClass('is-invalid');
                        $(this).next().html('Please fill in this field.');
                        $(this).removeClass('is-valid');
                        check.push(false);
                    } else {
                        if (isNaN(min) == false && isNaN(max) == true && min > numval) {
                            $(this).addClass('is-invalid')
                            $(this).parent().find('.required-number').html('Sorry minimum number is ' + min + '.');
                            $(this).removeClass('is-valid');
                            check.push(false);
                        } else if (isNaN(min) == true && isNaN(max) == false && max < numval) {
                            $(this).addClass('is-invalid')
                            $(this).parent().find('.required-number').html('Sorry maximum number is ' + max + '.');
                            $(this).removeClass('is-valid');
                            check.push(false);
                        } else if (isNaN(min) == false && isNaN(max) == false && (min > numval || numval > max)) {
                            $(this).addClass('is-invalid')
                            $(this).parent().find('.required-number').html('Select between minimum number ' + min +
                                ' and maximum number ' + max + '.');
                            $(this).removeClass('is-valid');
                            check.push(false);
                        } else {
                            $(this).removeClass('is-invalid');
                            $(this).addClass('is-valid');
                            $(this).parent().find('.required-number').html('');
                            check.push(true);
                        }
                    }
                }
                if ($(this).attr('type') == 'file') {
                    var inp = $(this).val();
                    if (inp.length == 0) {
                        $(this).addClass('is-invalid');
                        $(this).next().html('Please select file in this field.');
                        check.push(false);
                    } else {
                        $(this).removeClass('is-invalid');
                        $(this).addClass('is-valid');
                        $(this).next().html('');
                        check.push(true);
                    }
                }
                if ($(this).attr('type') == 'date') {
                    var inp = $(this).val();
                    if (inp.length == 0) {
                        $(this).addClass('is-invalid');
                        $(this).next().html('Please select date in this field.');
                        check.push(false);
                    } else {
                        $(this).removeClass('is-invalid');
                        $(this).addClass('is-valid');
                        $(this).next().html('');
                        check.push(true);
                    }
                }
                if ($(this)[0].localName == 'textarea') {
                    var inp = $(this).val();
                    if (inp.length == 0) {
                        $(this).addClass('is-invalid');
                        $(this).next().html('Please fill in this field.');
                        check.push(false);
                    } else {
                        $(this).removeClass('is-invalid');
                        $(this).addClass('is-valid');
                        $(this).next().html('');
                        check.push(true);
                    }
                }
                if ($(this).attr('type') == 'text') {
                    var inp = $(this).val();
                    if (inp.length == 0) {
                        $(this).addClass('is-invalid');
                        $(this).next().html('Please fill in this field.');
                        check.push(false);
                    } else {
                        $(this).removeClass('is-invalid');
                        $(this).addClass('is-valid');
                        $(this).next().html('');
                        check.push(true);
                    }
                }
            });
            var valid = true;
            check.forEach(function(val) {
                if (val == false) {
                    valid = false;
                    return false;
                }
            });
            if (valid) {
                $('.step-' + currentTab).addClass('finish');
            }
            return valid; // return the valid status
        }

        function submitForm(formData) {
            formData.append('ajax', true);
            $.ajax({
                type: "POST",
                url: '{{ route('forms.fill.store', $form->id) }}',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.is_success) {
                        $('.form-card-body').html(
                            '<div class="text-center gallery" id="success_loader"> <img src="{{ asset('assets/images/success.gif') }}" class="" /><br><br><h2 class="w-100 ">' +
                            response.message + '</h2></div>');
                        $('#nextBtn').removeAttr('disabled');
                        $('#nextBtn').html(' Submit');
                    } else {
                        showToStr('Error!', response.message, 'danger',
                            '{{ asset('assets/images/notification/high_priority-48.png') }}', 4000);
                        $('#nextBtn').removeAttr('disabled');
                        $('#nextBtn').html('Submit')
                        showTab(0);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessage = '';
                        $.each(errors, function(key, value) {
                            errorMessage += value[0] + '<br>';
                        });
                        showToStr('Validation Error!', errorMessage, 'danger',
                            '{{ asset('assets/images/notification/high_priority-48.png') }}', 4000);
                    } else {
                        showToStr('Error!', response.message, 'danger',
                            '{{ asset('assets/images/notification/high_priority-48.png') }}', 4000);
                    }
                }
            });
        }

        $(document).on("click", "input[type='checkbox']", function() {
            var name = $(this).attr('name');
            checkCheckbox(name);
        });
        $("body input[type='checkbox']").each(function(i, item) {
            var name = $(item).attr('name');
            checkCheckbox(name);
        });

        function checkCheckbox(name) {
            if ($("input[name='" + name + "']:checked").length) {
                $("input[name='" + name + "']").removeAttr('required');
            } else {
                $("input[name='" + name + "']").attr('required', 'required');
            }
        }

        function fixStepIndicator(n) {
            // This function removes the "active" class of all steps...
            var i, x = document.getElementsByClassName("step");
            for (i = 0; i < x.length; i++) {
                x[i].className = x[i].className.replace(" active", "");
            }
            //... and adds the "active" class on the current step:
            x[n].className += " active";
        }
    </script>
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $("#setData").trigger('click');
            }, 30);
        });
        document.addEventListener('DOMContentLoaded', function() {
            var genericExamples = document.querySelectorAll('[data-trigger]');
            for (i = 0; i < genericExamples.length; ++i) {
                var element = genericExamples[i];
                new Choices(element, {
                    placeholderValue: 'Select Option',
                    searchPlaceholderValue: 'Select Option',
                });
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $(".custom_select").select2();
        })
        var $starRating = $('.starRating');
        if ($starRating.length) {
            $starRating.each(function() {
                var val = $(this).attr('data-value');
                var num_of_star = $(this).attr('data-num_of_star');
                $(this).rateYo({
                    rating: val,
                    halfStar: true,
                    numStars: num_of_star,
                    precision: 2,
                    onSet: function(rating, rateYoInstance) {
                        num_of_star = $(rateYoInstance.node).attr('data-num_of_star');
                        var input = ($(rateYoInstance.node).attr('id'));
                        if (num_of_star == 10) {
                            rating = rating * 2;
                        }
                        $('input[name="' + input + '"]').val(rating);
                    }
                })
            });
        }
        if ($(".ck_editor").length) {
            CKEDITOR.replace($('.ck_editor').attr('name'), {
                filebrowserUploadUrl: "{{ route('ckeditor.upload', ['_token' => csrf_token()]) }}",
                filebrowserUploadMethod: 'form'
            });
        }
    </script>
    @if ($form->payment_status == 1)
        <script>
            var stripe, card;
            $(document).ready(function() {
                @if ($form->payment_status == 1)
                    @if ($form->payment_type == 'stripe')
                        stripe = Stripe('{{ Utility::getsettings('STRIPE_KEY') }}');
                        var elements = stripe.elements();
                        var style = {
                            base: {
                                color: '#32325d',
                                lineHeight: '24px',
                                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                                fontSmoothing: 'antialiased',
                                fontSize: '18px',
                                '::placeholder': {
                                    color: '#aab7c4'
                                }
                            },
                            invalid: {
                                color: '#fa755a',
                                iconColor: '#fa755a'
                            }
                        };
                        // Create an instance of the card Element
                        card = elements.create('card', {
                            style: style
                        });
                        // Add an instance of the card Element into the `card-element` <div>
                        card.mount('#card-element');
                        // Handle real-time validation errors from the card Element.
                        card.addEventListener('change', function(event) {
                            var displayError = document.getElementById('card-errors');
                            if (event.error) {
                                displayError.textContent = event.error.message;
                            } else {
                                displayError.textContent = '';
                            }
                        });
                    @endif
                    @if ($form->payment_type == 'paypal')
                        var amount = '{{ $form->amount }}';
                        var name = '{{ $form->title }}';
                        var currency = '{{ $form->currency_name }}';
                        var form_id = '{{ $form->id }}';
                        paypal.Buttons({
                            // Set up the transaction
                            createOrder: function(data, actions) {
                                return actions.order.create({
                                    purchase_units: [{
                                        amount: {
                                            value: amount
                                        }
                                    }]
                                });
                            },
                            // Finalize the transaction
                            onApprove: function(data, actions) {
                                return actions.order.capture().then(function(orderData) {
                                    // Successful capture! For demo purposes:
                                    console.log('Capture result', orderData, JSON.stringify(
                                        orderData, null, 2));
                                    var transaction = orderData.purchase_units[0].payments.captures[
                                        0];
                                    $('#payment_id').val(transaction.id);
                                    var errorElement = document.getElementById('paypal-errors');
                                    errorElement.textContent = '';
                                    $('#paypal-button-container').html('')
                                });
                            }
                        }).render('#paypal-button-container');
                    @endif
                @endif
            })
        </script>
        <script type="text/javascript" src="https://js.stripe.com/v3/"></script>
        <script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>
    @endif
@endpush
