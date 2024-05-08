@extends('layouts.main')
@section('title', __('Recaptcha Setting'))
@section('breadcrumb')
    <div class="col-md-12">
        <div class="page-header-title">
            <h4 class="m-b-10">{{ __('Recaptcha Setting') }}</h4>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">{!! Html::link(route('home'), __('Dashboard'), ['']) !!}</li>
            <li class="breadcrumb-item">{{ __('Recaptcha Setting') }}</li>
        </ul>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="card sticky-top">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            @include('landing-page.landingpage-sidebar')
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    <div class="card">
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="apps-setting" role="tabpanel"
                                aria-labelledby="landing-apps-setting">
                                {!! Form::open([
                                    'route' => ['landing.recaptcha.store'],
                                    'method' => 'Post',
                                    'id' => 'froentend-form',
                                    'enctype' => 'multipart/form-data',
                                    'data-validate',
                                    'novalidate',
                                ]) !!}
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <h5 class="mb-0">{{ __('Recaptcha Setting') }}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label
                                                    for="contact_us_recaptcha_status">{{ __('Contact Us Recaptcha Status') }}</label>
                                                <label class="mt-2 form-switch float-end custom-switch-v1">
                                                    {!! Form::checkbox(
                                                        'contact_us_recaptcha_status',
                                                        null,
                                                        Utility::keysettings('contact_us_recaptcha_status',1) ? true : false,
                                                        [
                                                            'class' => 'form-check-input input-primary',
                                                            'id' => 'contact_us_recaptcha_status',
                                                        ],
                                                    ) !!}
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <label for="login_recaptcha_status">{{ __('LogIn Recaptcha Status') }}</label>
                                                <label class="mt-2 form-switch float-end custom-switch-v1">
                                                    {!! Form::checkbox(
                                                        'login_recaptcha_status',
                                                        null,
                                                        Utility::keysettings('login_recaptcha_status',1) ? true : false,
                                                        [
                                                            'class' => 'form-check-input input-primary',
                                                            'id' => 'login_recaptcha_status',
                                                        ],
                                                    ) !!}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                {{ Form::label('recaptcha_key', __('Recaptcha Key'), ['class' => 'col-form-label']) }}
                                                {!! Form::text('recaptcha_key', Utility::keysettings('recaptcha_key',1), [
                                                    'class' => 'form-control',
                                                    'placeholder' => __('Enter recaptcha key'),
                                                ]) !!}
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                {{ Form::label('recaptcha_secret', __('Recaptcha Secret'), ['class' => 'col-form-label']) }}
                                                {!! Form::text('recaptcha_secret', Utility::keysettings('recaptcha_secret',1), [
                                                    'class' => 'form-control',
                                                    'placeholder' => __('Enter recaptcha secret'),
                                                ]) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="text-end">
                                        {{ Form::button(__('Save'), ['type' => 'submit',  'class' => 'btn btn-primary']) }}
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
