@php
    $authuser = auth()->user();
@endphp
@extends('layouts.app')
@section('title', __('SMS Notice'))
@section('content')
    <div class="login-content-inner">
        <div class="login-title">
            <h3>{{ __('SMS Notice') }}</h3>
        </div>
        {!! Form::open([
            'route' => 'sms.noticeverification',
            'data-validate',
            'method' => 'POST',
            'class' => 'form-horizontal',
        ]) !!}
        <div class="form-group">
            {{ Form::label('phone', __('Phone Number'), ['class' => 'form-label']) }}
            {!! Form::text('phone', $authuser->phone, [
                'autofocus' => '',
                'readonly',
                'required' => true,
                'autocomplete' => 'off',
                'placeholder' => 'Enter phone Number',
                'class' => 'form-control' . ($errors->has('phone') ? ' is-invalid' : null),
            ]) !!}
        </div>
        @if (Utility::keysettings('smssetting', 1) == 'fast2sms')
            <div class="form-group">
                {!! Form::radio('smstype', 'sms', true, [
                    'class' => 'btn-check',
                    'id' => 'smstype_sms',
                ]) !!}
                {{ Form::label('smstype_sms', __('SMS'), ['class' => 'btn btn-outline-primary']) }}
                {!! Form::radio('smstype', 'call', false, [
                    'class' => 'btn-check',
                    'id' => 'smstype_call',
                ]) !!}
                {{ Form::label('smstype_call', __('Call'), ['class' => 'btn btn-outline-primary']) }}
            </div>
        @endif
        <input type="hidden" name="email" value="{{ isset($authuser->email) ? $authuser->email : $_GET['email'] }}">
        <div class="d-grid">
            <button class="mt-2 btn btn-primary btn-block" type="submit">{{ __('Send Code') }}</button>
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
    </div>
@endsection
