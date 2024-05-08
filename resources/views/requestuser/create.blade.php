@php
    config([
        'captcha.sitekey' => Utility::getsettings('recaptcha_key'),
        'captcha.secret' => Utility::getsettings('recaptcha_secret'),
    ]);
@endphp
@extends('layouts.app')
@section('title', __('Register'))
@section('content')
    <div class="login-content-inner create-request">
        <div class="login-title">
            <h3>{{ __('Register') }}</h3>
        </div>
        {!! Form::open([
            'route' => 'requestuser.store',
            'method' => 'POST',
            'id' => 'request_form',
            'data-validate',
            'novalidate',
        ]) !!}
        <div class="form-group">
            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}
            {!! Form::text('name', null, [
                'class' => 'form-control',
                'id' => 'name',
                'placeholder' => __('Enter name'),
                'required',
                'autofocus',
            ]) !!}
        </div>
        <div class="form-group">
            {{ Form::label('email', __('Email Address'), ['class' => 'form-label']) }}
            {!! Form::email('email', null, [
                'class' => 'form-control',
                'id' => 'email',
                'placeholder' => __('Enter email address'),
                'required',
            ]) !!}
        </div>
        <div class="form-group">
            {{ Form::label('password', __('Password'), ['class' => 'form-label']) }}
            {!! Form::password('password', [
                'class' => 'form-control pwstrength',
                'id' => 'password',
                'placeholder' => __('Enter password'),
                'data-indicator' => 'pwindicator',
                'required',
            ]) !!}
            <div id="pwindicator" class="pwindicator">
                <div class="bar"></div>
                <div class="label"></div>
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('password_confirmation', __('Confirm Password'), ['class' => 'form-label']) }}
            {!! Form::password('password_confirmation', [
                'class' => 'form-control',
                'id' => 'password_confirmation',
                'placeholder' => __('Enter confirm password'),
                'required',
            ]) !!}
        </div>
        <div class="form-group">
            {{ Form::label('country_code', __('Country Code'), ['class' => 'form-label']) }}
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
            @if ($planId == 1)
                <div class="mb-4">
                    <div class="form-check check-box">
                        <label class="switch">
                            <input type="checkbox" required name="agree" id="flexCheckChecked">
                            <span class="slider round"></span>
                        </label>
                        <label for="flexCheckChecked" class="form-label lbl-check-box">{{ __('I agree with the') }}
                            <a
                                href="{{ route('description.page', 'terms') }}">{{ __('Terms and conditions') }}</a></label>
                    </div>
                </div>
                @if (Utility::getsettings('login_recaptcha_status') == '1')
                    <div class="my-3 text-center">
                        {!! NoCaptcha::renderJs() !!}
                        {!! NoCaptcha::display() !!}
                    </div>
                @endif
                <div class="d-grid">
                    {{ Form::hidden('plan_id', $planId, ['id' => 'plan_id']) }}
                    {{ Form::button(__('Register'), ['type' => 'submit', 'class' => 'btn btn-primary btn-block mt-2']) }}
                </div>
            @else
                <div class="in_btn_wrapper">
                    <div class="mb-4">
                        <div class="form-check check-box">
                            <label class="switch">
                                <input type="checkbox" required name="agree" id="flexCheckChecked">
                                <span class="slider round"></span>
                            </label>
                            <label for="flexCheckChecked" class="form-label lbl-check-box">{{ __('I agree with the') }}
                                <a
                                    href="{{ route('description.page', 'terms') }}">{{ __('Terms and conditions') }}</a></label>
                        </div>
                        <div class="error-message" id="bouncer-error_agree_on"></div>
                    </div>
                    @if (Utility::getsettings('login_recaptcha_status') == '1')
                        <div class="my-3 text-center">
                            {!! NoCaptcha::renderJs() !!}
                            {!! NoCaptcha::display() !!}
                        </div>
                    @endif
                    <div class="d-grid">
                        {{ Form::hidden('plan_id', $planId, ['id' => 'plan_id']) }}
                        {{ Form::button(__('Register'), ['type' => 'submit', 'class' => 'btn btn-primary btn-block mt-2']) }}
                    </div>
                </div>
            @endif
        </div>
        {!! Form::close() !!}
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
