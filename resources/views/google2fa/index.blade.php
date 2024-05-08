@extends('layouts.app')
@section('title', __('Two Factor Authentication'))
@section('content')
    <div class="login-content-inner">
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="login-title">
            <h3>{{ __('Two Factor Authentication') }}</h3>
            <p class="mt-2">{{ __('Enter the pin from Google Authenticator app.') }}</p>
        </div>
        <form method="POST" data-validate action="{{ route('2faVerify') }}">
            @csrf
            <div class="form-group">
                {{ Form::label('one_time_password', __('One time Password'), ['class' => 'form-label']) }}
                {!! Form::text('one_time_password', null, [
                    'class' => 'form-control',
                    'id' => 'one_time_password',
                    'placeholder' => __('Enter one time password'),
                    'required',
                ]) !!}
            </div>
            <div class="d-grid">
                <button type="submit" class="btn">{{ __('Authenticate') }}</button>
            </div>
        </form>
    </div>
@endsection
