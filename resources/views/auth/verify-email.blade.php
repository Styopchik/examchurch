@extends('layouts.app')
@section('title', __('Email verify'))
@section('content')
    <div class="login-content-inner">
        <div class="login-title">
            <h3>{{ __('Verify Your Email Address') }}</h3>
        </div>
        @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success" role="alert">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
        @endif
        <p>{{ __('Before proceeding, please check your email for a verification link.') }}
            {{ __('If you did not receive the email') }},</p>
        <br>
        <div class="text-center">
            <form class="d-inline" method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="mt-2 btn btn-link">{{ __('Resend Verification Email') }}</button>
            </form>
            <p class="my-3 text-center">
                <a onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                    class="f-w-400">{{ __('Logout') }}</a>
            </p>
            {!! Form::open([
                'route' => 'logout',
                'method' => 'POST',
                'id' => 'logout-form',
                'class' => 'd-none',
            ]) !!}
            {!! Form::close() !!}
        </div>
    </div>
@endsection
