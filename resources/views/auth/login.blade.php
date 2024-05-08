@php
    config([
        'captcha.sitekey' => Utility::getsettings('recaptcha_key'),
        'captcha.secret' => Utility::getsettings('recaptcha_secret'),
    ]);
    $user = Auth::user();
@endphp
@extends('layouts.app')
@section('title', __('Sign in'))
@section('content')
    <div class="login-content-inner">
        <div class="login-title">
            <h3>{{ __('Sign In') }}</h3>
        </div>
        <form method="POST" data-validate action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label class="form-label" for="email">{{ __('Email Address') }}</label>
                <input type="email" id="email" class="form-control" placeholder="{{ __('Enter email address') }}"
                    name="email" tabindex="1" value="{{ old('email') }}" required autocomplete="email" autofocus>
            </div>
            <div class="form-group">
                <label class="form-label" for="password">{{ __('Enter Password') }}</label>
                <a href="{{ route('password.request') }}" class="float-end forget-password">
                    {{ __('Forgot Password ?') }}
                </a>
                <input id="password" type="password" class="form-control" placeholder="{{ __('Enter password') }}"
                    name="password" tabindex="2" required autocomplete="current-password">
            </div>
            @if (Utility::getsettings('login_recaptcha_status') == '1')
                <div class="my-3 text-center">
                    {!! NoCaptcha::renderJs() !!}
                    {!! NoCaptcha::display() !!}
                </div>
            @endif
            <div class="d-grid">
                <button type="submit" class="btn">{{ __('Sign In') }}</button>
            </div>
        </form>
        @if (Utility::getsettings('GOOGLESETTING') == 'on' ||
                Utility::getsettings('FACEBOOKSETTING') == 'on' ||
                Utility::getsettings('GITHUBSETTING') == 'on' ||
                Utility::getsettings('LINKEDINSETTING') == 'on')
            <div class="register-option">
                <p>{{ __('or register with') }}</p>
            </div>
        @endif
        <div class="social-media-icon">
            @if (Utility::getsettings('GOOGLESETTING') == 'on' ||
                    Utility::getsettings('FACEBOOKSETTING') == 'on' ||
                    Utility::getsettings('GITHUBSETTING') == 'on')
                <div class="mt-1 mb-4 row">
                    <div class="register-btn-wrapper">
                        @if (Utility::getsettings('GOOGLESETTING') == 'on')
                            <div class="col-4">
                                <div class="d-grid">
                                    <a href="{{ url('/redirect/google') }}" class="btn btn-light">
                                        {!! Form::image(asset('assets/images/auth/img-google.svg'), null, ['class' => 'img-fluid wid-25']) !!}
                                    </a>
                                </div>
                            </div>
                        @endif
                        @if (Utility::getsettings('FACEBOOKSETTING') == 'on')
                            <div class="col-4">
                                <div class="d-grid">
                                    <a href="{{ url('/redirect/facebook') }}" class="btn btn-light">
                                        {!! Form::image(asset('assets/images/auth/img-facebook.svg'), null, ['class' => 'img-fluid wid-25']) !!}
                                    </a>
                                </div>
                            </div>
                        @endif
                        @if (Utility::getsettings('GITHUBSETTING') == 'on')
                            <div class="col-4">
                                <div class="d-grid">
                                    <a href="{{ url('/redirect/github') }}" class="btn btn-light">
                                        {!! Form::image(asset('assets/images/auth/github.svg'), null, ['class' => 'img-fluid wid-25']) !!}
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
