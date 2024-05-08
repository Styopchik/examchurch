@php
    config([
        'captcha.sitekey' => Utility::getsettings('recaptcha_key'),
        'captcha.secret' => Utility::getsettings('recaptcha_secret'),
    ]);
@endphp
@extends('layouts.app')
@section('title', __('Sign Up'))
@section('content')
    <div class="login-content-inner create-request">
        <div class="login-title">
            <h3>{{ __('Sign Up') }}</h3>
        </div>
        <form method="POST" data-validate action="{{ route('register') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">{{ __('Name') }}</label>
                <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}"
                    placeholder="{{ __('Enter name') }}" required autocomplete="name" autofocus />
            </div>
            <div class="form-group">
                <label class="form-label">{{ __('E-mail address') }}</label>
                <input type="email" class="form-control" name="email" value="{{ old('email') }}" id="email"
                    placeholder="{{ __('Enter email address') }}" required autocomplete="email" />
            </div>
            <div class="form-group">
                <label class="form-label">{{ __('Password') }}</label>
                <input type="password" class="form-control pwstrength" name="password" id="password"
                    data-indicator="pwindicator" placeholder="{{ __('Enter password') }}" required />
            </div>
            <div class="form-group">
                <label class="form-label">{{ __('Confirm Password') }}</label>
                <input type="password" class="form-control" name="password_confirmation" id="password-confirm"
                    placeholder="{{ __('Enter confirm password') }}" required autocomplete="new-password" />
            </div>
            <div class="form-group">
                {{ Form::label('country_code', __('Country Code'), ['class' => 'd-block form-label']) }}
                <select id="country_code" name="country_code"class="form-control" data-trigger>
                    @foreach (\App\Core\Data::getCountriesList() as $key => $value)
                        <option data-kt-flag="{{ $value['flag'] }}" value="{{ $key }}">
                            +{{ $value['phone_code'] }} {{ $value['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                {{ Form::label('phone', __('Phone Number'), ['class' => 'form-label']) }}
                {!! Form::number('phone', null, [
                    'autofocus' => '',
                    'required' => true,
                    'autocomplete' => 'off',
                    'placeholder' => 'Enter phone Number',
                    'class' => 'form-control',
                ]) !!}
            </div>
            <div class="form-group">
                {{ Form::label('role', __('Role'), ['class' => 'form-label']) }}
                {!! Form::text('role', Utility::getsettings('roles'), [
                    'class' => 'form-control',
                ]) !!}
            </div>
            <div class="form-group">
                <div class="form-check check-box">
                    <label class="switch">
                        <input type="checkbox" name="terms" id="flexCheckChecked">
                        <span class="slider round"></span>
                    </label>
                    <label for="flexCheckChecked" class="form-label lbl-check-box">{{ __('I accept the ') }}
                        <a href="{{ route('description.page', 'terms') }}">{{ __('Terms & conditions') }}</a></label>
                </div>
            </div>
            @if (Utility::getsettings('login_recaptcha_status') == '1')
                <div class="my-3 text-center">
                    {!! NoCaptcha::renderJs() !!}
                    {!! NoCaptcha::display() !!}
                </div>
            @endif
            <div class="d-grid">
                <button type="submit" class="btn">{{ __('Sign Up') }}</button>
            </div>
        </form>
    </div>
@endsection
@push('script')
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
    </script>
@endpush
