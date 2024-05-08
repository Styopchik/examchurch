@extends('layouts.app')
@section('title', __('Reset Password'))
@section('content')
    <div class="login-content-inner">
        <div class="login-title">
            <h3>{{ __('Reset Password') }}</h3>
        </div>
        {!! Form::open([
            'route' => 'password.update',
            'method' => 'POST',
            'data-validate',
        ]) !!}
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div class="form-group">
            <label for="email" class="form-label">{{ __('E-mail address') }}</label>
            <input type="email" class="form-control" name="email" value="{{ old('email', $request->email) }}"
                id="email" placeholder="{{ __('E-mail address') }}" required autocomplete="email" autofocus />
        </div>
        <div class="form-group">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input type="password" class="form-control" name="password" id="password"
                placeholder="{{ __('New Password') }}" required autocomplete="new-password" />
        </div>
        <div class="form-group">
            <label for="password-confirm" class="form-label">{{ __('Confirm Password') }}</label>
            <input type="password" class="form-control" name="password_confirmation" id="password-confirm"
                placeholder="{{ __('New Password') }}" required autocomplete="new-password" />
        </div>
        <div class="d-grid">
            <button type="submit" class="mt-2 btn btn-primary btn-block">
                {{ __('Reset Password') }} </button>
        </div>
        {!! Form::close() !!}
    </div>
@endsection
