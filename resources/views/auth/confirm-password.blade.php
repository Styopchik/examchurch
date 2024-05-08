@extends('layouts.app')
@section('title', __('Confirm Password'))
@section('content')
    <div class="login-content-inner">
        <div class="login-title">
            <h3>{{ __('Confirm Password') }}</h3>
        </div>
        {!! Form::open([
            'route' => 'password.confirm',
            'method' => 'POST',
            'class' => 'needs-validation',
            'data-validate',
        ]) !!}
        <div class="form-group">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input id="password" type="password" class="form-control"
                name="password" required autocomplete="current-password">
        </div>
        <div class="d-grid row">
            <div class="col-md-8 offset-md-4">
                <button type="submit" class="mt-2 btn btn-primary btn-block">
                    {{ __('Confirm Password') }}
                </button>
                @if (Route::has('password.request'))
                    <a class="btn btn-link" href="{{ route('password.request') }}">
                        {{ __('Forgot Your Password ?') }}
                    </a>
                @endif
            </div>
        </div>
        {!! Form::close() !!}
    </div>
@endsection
