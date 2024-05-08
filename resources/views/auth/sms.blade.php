@php
    $name = auth()->user()->name;
    $email = auth()->user()->email;
    $password = auth()->user()->password;
    $phone = auth()->user()->phone;
@endphp
@extends('layouts.app')
@section('title', __('SMS'))
@section('content')
    <div class="login-content-inner">
        <div class="login-title">
            <h3>{{ __('SMS Code') }}</h3>
        </div>
        {!! Form::open([
            'route' => 'sms.verification',
            'data-validate',
            'method' => 'POST',
            'class' => 'form-horizontal',
        ]) !!}
        <div class="form-group">
            {{ Form::label('code', __('Sms Code'), ['class' => 'form-label']) }}
            {!! Form::text('code', null, [
                'class' => 'form-control col-md-4',
                'id' => 'code',
                'placeholder' => __('Enter sms code'),
                'required',
            ]) !!}
        </div>
        <input type="hidden" name="email" value="{{ isset($email) ? $email : $_GET['email'] }}">
        <input type="hidden" name="password" value="{{ isset($password) ? $password : $_GET['password'] }}">
        <input type="hidden" name="phone" value="{{ isset($phone) ? $phone : $_GET['phone'] }}">
        <div class="d-grid">
            <button class="mt-2 btn btn-primary btn-block" type="submit">{{ __('Verify') }}</button>
        </div>
        {!! Form::close() !!}
        <p class="my-3 text-center">
            <a href="{{ route('logout') }}"
                onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                class="f-w-400">{{ __('Logout') }}</a>
        </p>
        {!! Form::open([
            'route' => 'logout',
            'method' => 'POST',
            'id' => 'logout-form',
            'class' => 'd-none',
        ]) !!}
        {!! Form::close() !!}
        <div class="form w-100 ">
            <div class="text-center fw-bold fs-5">
                <span class="text-muted me-1">{{ __('Didn’t get the code ?') }}</span>
                <p class="text-muted me-1" id="wait_message">{{ __('Please wait') }}
                    <span class="count_down"></span> {{ __('second until request a new one.') }}
                </p>
                {!! Form::open([
                    'method' => 'POST',
                    'route' => ['sms.verification.resend'],
                    'id' => 'phone_verification_resend',
                ]) !!}
                {!! Form::hidden('smstype', $smstype, [
                    'class' => 'form-control',
                ]) !!}
                <button class="mt-2 btn btn-link">{{ __('Resend') }}</button>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        var seconds = {{ auth()->user()->lastCodeRemainingSeconds() }};

        function timer(seconds) {
            $("#phone_verification_resend").addClass('d-none');
            $("#wait_message").removeClass('d-none');
            $("#wait_message .count_down").html(seconds);
            setTimeout(function() {
                $("#phone_verification_resend").removeClass('d-none');
                $("#wait_message").addClass('d-none');
            }, seconds * 1000);
            var interval = setInterval(function() {
                if (seconds == 0) {
                    clearInterval(interval);
                }
                seconds--;
                $("#wait_message .count_down").html(seconds);
            }, 1000)
        }
        timer(seconds);
    </script>
@endpush
