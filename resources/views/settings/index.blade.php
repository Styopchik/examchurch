@php
    use App\Facades\UtilityFacades;
    $user = Auth::user();
    $lang = \App\Facades\UtilityFacades::getValByName('default_language');
    $primaryColor = $user->theme_color;
    if (isset($primaryColor)) {
        $color = $primaryColor;
    } else {
        $color = 'theme-4';
    }
    $roles = App\Models\Role::whereNotIn('name', ['Super Admin'])
        ->pluck('name', 'name')
        ->all();
@endphp
@extends('layouts.main')
@section('title', __('Settings'))
@section('breadcrumb')
    <div class="col-md-12">
        <div class="page-header-title">
            <h4 class="m-b-10">{{ __('Settings') }}</h4>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">{!! Html::link(route('home'), __('Dashboard'), ['']) !!}</li>
            <li class="breadcrumb-item">{{ __('Settings') }}</li>
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
                            @if (\Auth::user()->type == 'Super Admin')
                                <a href="#app-setting"
                                    class="border-0 list-group-item list-group-item-action">{{ __('App Setting') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                                <a href="#storage-setting"
                                    class="border-0 list-group-item list-group-item-action">{{ __('Storage Setting') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                                <a href="#pusher-setting"
                                    class="border-0 list-group-item list-group-item-action">{{ __('Pusher Setting') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                                <a href="#social-setting"
                                    class="border-0 list-group-item list-group-item-action">{{ __('Social Setting') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                                <a href="#notification_setting"
                                    class="border-0 list-group-item list-group-item-action">{{ __('Notification Setting') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif
                            <a href="#general-setting"
                                class="border-0 list-group-item list-group-item-action">{{ __('General Setting') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#email-setting"
                                class="border-0 list-group-item list-group-item-action">{{ __('Email Setting') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            @if (\Auth::user()->type == 'Admin')
                                <a href="#captcha-setting"
                                    class="border-0 list-group-item list-group-item-action">{{ __('Captcha Setting') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                                <a href="#notification_setting"
                                    class="border-0 list-group-item list-group-item-action">{{ __('Notification Setting') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                                <a href="#google-calender-setting"
                                    class="border-0 list-group-item list-group-item-action">{{ __('Google calender Setting') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                                <a href="#google-map-setting"
                                    class="border-0 list-group-item list-group-item-action">{{ __('Google Map Setting') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif
                            <a href="#payment-setting"
                                class="border-0 list-group-item list-group-item-action">{{ __('Payment Setting') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#sms_setting"
                                class="border-0 list-group-item list-group-item-action">{{ __('Sms Setting') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            @if (\Auth::user()->type == 'Super Admin')
                                <a href="#cookie_setting"
                                    class="border-0 list-group-item list-group-item-action">{{ __('Cookie Setting') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                                <a href="#cache_setting"
                                    class="border-0 list-group-item list-group-item-action">{{ __('Cache Setting') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                                <a href="#seo-setting"
                                    class="border-0 list-group-item list-group-item-action">{{ __('SEO Setting') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                                <a href="#pwa-setting"
                                    class="border-0 list-group-item list-group-item-action">{{ __('PWA Setting') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    @if (\Auth::user()->type == 'Super Admin')
                        <div id="app-setting" class="card">
                            {!! Form::open([
                                'route' => 'settings.appName.update',
                                'method' => 'POST',
                                'enctype' => 'multipart/form-data',
                                'data-validate',
                                'novalidate',
                            ]) !!}
                            <div class="card-header">
                                <h5> {{ __('App Setting') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="pt-0 row">
                                    <div class="col-lg-4 col-sm-6 col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5>{{ __('Dark Logo') }}</h5>
                                            </div>
                                            <div class="pt-0 card-body">
                                                <div class="inner-content">
                                                    <div class="py-1 mt-4 text-center logo-content">
                                                        <a href="{{ Utility::getsettings('app_dark_logo') ? Storage::url('app-logo/app-dark-logo.png') : Storage::url('not-exists-data-images/78x78.png') }}"
                                                            target="_blank">
                                                            <img src="{{ Utility::getsettings('app_dark_logo') ? Storage::url('app-logo/app-dark-logo.png') : Storage::url('not-exists-data-images/78x78.png') }}"
                                                                id="blah2">
                                                        </a>
                                                    </div>
                                                    <div class="mt-5 text-center choose-files">
                                                        <label for="logo" class="form-label d-block">
                                                            <div class="m-auto bg-primary">
                                                                <i
                                                                    class="px-1 ti ti-upload"></i>{{ __('Choose file here') }}
                                                                {{ Form::file('app_dark_logo', ['class' => 'form-control file', 'value' => 'Select Dark Logo']) }}
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-6 col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5>{{ __('Light Logo') }}</h5>
                                            </div>
                                            <div class="pt-0 card-body bg-primary">
                                                <div class="inner-content">
                                                    <div class="py-1 mt-4 text-center logo-content">
                                                        <a href="{{ Utility::getsettings('app_logo') ? Storage::url('app-logo/app-logo.png') : Storage::url('not-exists-data-images/78x78.png') }}"
                                                            target="_blank">
                                                            <img src="{{ Utility::getsettings('app_logo') ? Storage::url('app-logo/app-logo.png') : Storage::url('not-exists-data-images/78x78.png') }}"
                                                                id="app-dark-logo">
                                                        </a>
                                                    </div>
                                                    <div class="mt-5 text-center choose-files">
                                                        <label for="white_logo" class="form-label d-block">
                                                            <div class="m-auto w-logo">
                                                                <i
                                                                    class="px-1 ti ti-upload"></i>{{ __('Choose file here') }}
                                                                {{ Form::file('app_logo', ['class' => 'form-control file', 'value' => 'Select Logo']) }}
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-6 col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5>{{ __('Favicon Logo') }}</h5>
                                            </div>
                                            <div class="pt-0 card-body">
                                                <div class="inner-content">
                                                    <div class="py-1 mt-4 text-center logo-content">
                                                        <a href="{{ Utility::getsettings('favicon_logo') ? Storage::url('app-logo/app-favicon-logo.png') : Storage::url('not-exists-data-images/78x78.png') }}"
                                                            target="_blank">
                                                            <img height="35px"
                                                                src="{{ Utility::getsettings('favicon_logo') ? Storage::url('app-logo/app-favicon-logo.png') : Storage::url('not-exists-data-images/78x78.png') }}"
                                                                id="blah">
                                                        </a>
                                                    </div>
                                                    <div class="mt-5 text-center choose-files">
                                                        <label for="favicon" class="form-label d-block">
                                                            <div class="m-auto bg-primary">
                                                                <i
                                                                    class="px-1 ti ti-upload"></i>{{ __('Choose file here') }}
                                                                {{ Form::file('favicon_logo', ['class' => 'form-control file', 'value' => 'Select Favicon Logo']) }}
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {{ Form::label('app_name', __('Application Name'), ['class' => 'form-label']) }}
                                        {!! Form::text('app_name', Utility::getsettings('app_name'), [
                                            'class' => 'form-control',
                                            'required',
                                            'placeholder' => __('Enter application name'),
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="text-end">
                                    {{ Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) }}
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                        <div id="storage-setting" class="card">
                            {!! Form::open([
                                'route' => 'settings.wasabiSetting.update',
                                'method' => 'POST',
                                'enctype' => 'multipart/form-data',
                                'data-validate',
                                'novalidate',
                            ]) !!}
                            <div class="card-header">
                                <h5> {{ __('Storage Setting') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pb-3">
                                            <p class="text-danger">
                                                {{ __('Note :- If you Add S3 & wasabi Storage settings then you have to store all images First.') }}
                                            </p>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::radio('storage_type', 'local', Utility::getsettings('storage_type') == 'local' ? true : false, [
                                                'class' => 'btn-check',
                                                'id' => 'storage_type_local',
                                            ]) !!}
                                            {{ Form::label('storage_type_local', __('Local'), ['class' => 'btn btn-outline-primary']) }}

                                            {!! Form::radio('storage_type', 's3', Utility::getsettings('storage_type') == 's3' ? true : false, [
                                                'class' => 'btn-check',
                                                'id' => 'storage_type_s3',
                                            ]) !!}
                                            {{ Form::label('storage_type_s3', __('S3 setting'), ['class' => 'btn btn-outline-primary']) }}

                                            {!! Form::radio('storage_type', 'wasabi', Utility::getsettings('storage_type') == 'wasabi' ? true : false, [
                                                'class' => 'btn-check',
                                                'id' => 'storage_type_wasabi',
                                            ]) !!}
                                            {{ Form::label('storage_type_wasabi', __('Wasabi'), ['class' => 'btn btn-outline-primary']) }}
                                        </div>
                                        <div id="s3"
                                            class="desc {{ Utility::getsettings('storage_type') == 's3' ? '' : 'd-none' }}">
                                            <div class="">
                                                <div class="row">
                                                    <div class="form-group">
                                                        {{ Form::label('s3_key', __('S3 Key'), ['class' => 'form-label']) }}
                                                        {!! Form::text('s3_key', Utility::getsettings('s3_key'), [
                                                            'placeholder' => __('Enter s3 key'),
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                    </div>
                                                    <div class="form-group">
                                                        {{ Form::label('s3_secret', __('S3 Secret'), ['class' => 'form-label']) }}
                                                        {!! Form::text('s3_secret', Utility::getsettings('s3_secret'), [
                                                            'placeholder' => __('Enter s3 secret'),
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                    </div>
                                                    <div class="form-group">
                                                        {{ Form::label('s3_region', __('S3 Region'), ['class' => 'form-label']) }}
                                                        {!! Form::text('s3_region', Utility::getsettings('s3_region'), [
                                                            'placeholder' => __('Enter s3 region'),
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                    </div>
                                                    <div class="form-group">
                                                        {{ Form::label('s3_bucket', __('s3 bucket'), ['class' => 'form-label']) }}
                                                        {!! Form::text('s3_bucket', Utility::getsettings('s3_bucket'), [
                                                            'placeholder' => __('Enter S3 Bucket'),
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                    </div>
                                                    <div class="form-group">
                                                        {{ Form::label('s3 URL', __('s3 URL'), ['class' => 'form-label']) }}
                                                        {!! Form::text('s3_url', Utility::getsettings('s3_url'), [
                                                            'placeholder' => __('Enter s3 URL'),
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                    </div>
                                                    <div class="form-group">
                                                        {{ Form::label('s3_endpoint', __('S3 Endpoint'), ['class' => 'form-label']) }}
                                                        {!! Form::text('s3_endpoint', Utility::getsettings('s3_endpoint'), [
                                                            'placeholder' => __('Entre  S3 Endpoint'),
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="wasabi"
                                            class="desc {{ Utility::getsettings('storage_type') == 'wasabi' ? '' : 'd-none' }}">
                                            <div class="">
                                                <div class="row">
                                                    <div class="form-group">
                                                        {{ Form::label('wasabi_key', __('Wasabi Key'), ['class' => 'form-label']) }}
                                                        {!! Form::text('wasabi_key', Utility::getsettings('wasabi_key'), [
                                                            'placeholder' => __('Enter Wasabi key'),
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                    </div>
                                                    <div class="form-group">
                                                        {{ Form::label('wasabi_secret', __('Wasabi Secret'), ['class' => 'form-label']) }}
                                                        {!! Form::text('wasabi_secret', Utility::getsettings('wasabi_secret'), [
                                                            'placeholder' => __('Enter Wasabi Secret'),
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                    </div>
                                                    <div class="form-group">
                                                        {{ Form::label('wasabi_region', __('Wasabi Region'), ['class' => 'form-label']) }}
                                                        {!! Form::text('wasabi_region', Utility::getsettings('wasabi_region'), [
                                                            'placeholder' => __('Enter Wasabi region'),
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                    </div>
                                                    <div class="form-group">
                                                        {{ Form::label('wasabi_bucket', __('Enter Wasabi bucket'), ['class' => 'form-label']) }}
                                                        {!! Form::text('wasabi_bucket', Utility::getsettings('wasabi_bucket'), [
                                                            'placeholder' => __('wasabi Bucket'),
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                    </div>
                                                    <div class="form-group">
                                                        {{ Form::label('wasabi_url', __('Wasabi URL'), ['class' => 'form-label']) }}
                                                        {!! Form::text('wasabi_url', Utility::getsettings('wasabi_url'), [
                                                            'placeholder' => __('Enter wasabi URL'),
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                    </div>
                                                    <div class="form-group">
                                                        {{ Form::label('wasabi_root', __('Wasabi Endpoint'), ['class' => 'form-label']) }}
                                                        {!! Form::text('wasabi_root', Utility::getsettings('wasabi_root'), [
                                                            'placeholder' => __('Enter Wasabi endpoint'),
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="text-end">
                                    {{ Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) }}
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                        <div id="pusher-setting" class="card">
                            {!! Form::open([
                                'route' => 'settings.pusherSetting.update',
                                'method' => 'POST',
                                'enctype' => 'multipart/form-data',
                                'data-validate',
                                'novalidate',
                            ]) !!}
                            <div class="card-header">
                                <h5>{{ __('Pusher Setting') }}</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted"> {{ __('Pusher Setting') }}
                                    {!! Html::link('https://pusher.com/', __('Document'), ['target' => '_blank']) !!}
                                </p>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-group">
                                            {{ Form::label('pusher_id', __('Pusher App ID'), ['class' => 'form-label']) }}
                                            {!! Form::text('pusher_id', Utility::getsettings('pusher_id'), [
                                                'placeholder' => __('Enter pusher app id'),
                                                'class' => 'form-control',
                                            ]) !!}
                                        </div>
                                        <div class="form-group">
                                            {{ Form::label('pusher_key', __('Pusher Key'), ['class' => 'form-label']) }}
                                            {!! Form::text('pusher_key', Utility::getsettings('pusher_key'), [
                                                'placeholder' => __('Enter pusher key'),
                                                'class' => 'form-control',
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-group">
                                            {{ Form::label('pusher_secret', __('Pusher Secret'), ['class' => 'form-label']) }}
                                            {!! Form::text('pusher_secret', Utility::getsettings('pusher_secret'), [
                                                'placeholder' => __('Enter pusher secret'),
                                                'class' => 'form-control',
                                            ]) !!}
                                        </div>
                                        <div class="form-group">
                                            {{ Form::label('pusher_cluster', __('Pusher Cluster'), ['class' => 'form-label']) }}
                                            {!! Form::text('pusher_cluster', Utility::getsettings('pusher_cluster'), [
                                                'placeholder' => __('Enter pusher cluster'),
                                                'class' => 'form-control',
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-9 col-sm-6">
                                                {{ Form::label('pusher_status', __('Status'), ['class' => 'form-label']) }}
                                            </div>
                                            <div class="col-md-3 col-sm-6">
                                                <label class="form-switch float-end">
                                                    {!! Form::checkbox('pusher_status', null, Utility::getsettings('pusher_status') ? true : false, [
                                                        'class' => 'form-check-input input-primary',
                                                        'id' => 'pusher_status',
                                                    ]) !!}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="text-end">
                                    {{ Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) }}
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                        <div id="social-setting" class="card faq">
                            <div class="card-header">
                                <h5> {{ __('Social Settings') }}</h5>
                            </div>
                            {!! Form::open([
                                'route' => ['settings.socialSetting.update'],
                                'method' => 'POST',
                                'enctype' => 'multipart/form-data',
                                'data-validate',
                                'novalidate',
                            ]) !!}
                            <div class="p-4 card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="accordion accordion-flush" id="accordionExamples">
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="google">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseone"
                                                        aria-expanded="true" aria-controls="collapseone">
                                                        <span class="flex-1 d-flex align-items-center">
                                                            <i class="ti ti-brand-google text-primary"></i>
                                                            {{ __('Google Setting') }}
                                                        </span>
                                                        @if (Utility::getsettings('googlesetting') == 'on')
                                                            <a
                                                                class="text-white btn btn-sm btn-primary float-end me-3">{{ __('Active') }}</a>
                                                        @endif
                                                    </button>
                                                </h2>
                                                <div id="collapseone" class="accordion-collapse collapse"
                                                    aria-labelledby="google" data-bs-parent="#accordionExamples">
                                                    <div class="accordion-body">
                                                        <div class="col-12 d-flex justify-content-between">
                                                            <small
                                                                class="">{{ __('How To Enable Login With Google') }}
                                                                {!! Html::link(Storage::url('pdf/login with google.pdf'), __('Document'), [
                                                                    'target' => '_blank',
                                                                ]) !!}
                                                            </small>
                                                            <div class="form-check form-switch d-inline-block">
                                                                {!! Form::checkbox('socialsetting[]', 'google', Utility::getsettings('googlesetting') == 'on' ? true : false, [
                                                                    'class' => 'form-check-input',
                                                                    'id' => 'socialsetting_google',
                                                                ]) !!}
                                                                {{ Form::label('socialsetting_google', __('Enable'), ['class' => 'custom-control-label form-control-label']) }}
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="form-group">
                                                                {{ Form::label('google_client_id', __('Google Client Id'), ['class' => 'form-label']) }}
                                                                {!! Form::text(
                                                                    'google_client_id',
                                                                    Utility::getsettings('google_client_id') ? Utility::getsettings('google_client_id') : null,
                                                                    [
                                                                        'placeholder' => __('Enter google client id'),
                                                                        'class' => 'form-control',
                                                                    ],
                                                                ) !!}
                                                            </div>
                                                            <div class="form-group">
                                                                {{ Form::label('google_client_secret', __('Google Client Secret'), ['class' => 'form-label']) }}
                                                                {!! Form::text(
                                                                    'google_client_secret',
                                                                    Utility::getsettings('google_client_secret') ? Utility::getsettings('google_client_secret') : null,
                                                                    [
                                                                        'placeholder' => __('Enter google client secret'),
                                                                        'class' => 'form-control',
                                                                    ],
                                                                ) !!}
                                                            </div>
                                                            <div class="form-group">
                                                                {{ Form::label('google_redirect', __('Google Redirect Url'), ['class' => 'form-label']) }}
                                                                {!! Form::text(
                                                                    'google_redirect',
                                                                    Utility::getsettings('google_redirect') ? Utility::getsettings('google_redirect') : null,
                                                                    [
                                                                        'placeholder' => __('https://demo.test.com/callback/google'),
                                                                        'class' => 'form-control',
                                                                    ],
                                                                ) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="facebook">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapsetwo"
                                                        aria-expanded="true" aria-controls="collapsetwo">
                                                        <span class="flex-1 d-flex align-items-center">
                                                            <i class="ti ti-brand-facebook text-primary"></i>
                                                            {{ __('Facebook Setting') }}
                                                        </span>
                                                        @if (Utility::getsettings('facebooksetting') == 'on')
                                                            <a
                                                                class="text-white btn btn-sm btn-primary float-end me-3">{{ __('Active') }}</a>
                                                        @endif
                                                    </button>
                                                </h2>
                                                <div id="collapsetwo" class="accordion-collapse collapse"
                                                    aria-labelledby="facebook" data-bs-parent="#accordionExamples">
                                                    <div class="accordion-body">
                                                        <div class="col-12 d-flex justify-content-between">
                                                            <small
                                                                class="">{{ __('How To Enable Login With Facebook') }}
                                                                {!! Html::link(Storage::url('pdf/login with facebook.pdf'), __('Document'), [
                                                                    'target' => '_blank',
                                                                ]) !!}</small>
                                                            <div class="form-check form-switch d-inline-block">
                                                                {!! Form::checkbox(
                                                                    'socialsetting[]',
                                                                    'facebook',
                                                                    Utility::getsettings('facebooksetting') == 'on' ? true : false,
                                                                    [
                                                                        'class' => 'form-check-input',
                                                                        'id' => 'socialsetting_facebook',
                                                                    ],
                                                                ) !!}
                                                                {{ Form::label('socialsetting_facebook', __('Enable'), ['class' => 'custom-control-label form-control-label']) }}
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="form-group">
                                                                {{ Form::label('facebook_client_id', __('Facebook Client Id'), ['class' => 'form-label']) }}
                                                                {!! Form::text(
                                                                    'facebook_client_id',
                                                                    Utility::getsettings('facebook_client_id') ? Utility::getsettings('facebook_client_id') : null,
                                                                    [
                                                                        'placeholder' => __('Enter facebook client id'),
                                                                        'class' => 'form-control',
                                                                    ],
                                                                ) !!}
                                                            </div>
                                                            <div class="form-group">
                                                                {{ Form::label('facebook_client_secret', __('Facebook Client Secret'), ['class' => 'form-label']) }}
                                                                {!! Form::text(
                                                                    'facebook_client_secret',
                                                                    Utility::getsettings('facebook_client_secret') ? Utility::getsettings('facebook_client_secret') : null,
                                                                    [
                                                                        'placeholder' => __('Enter facebook client secret'),
                                                                        'class' => 'form-control',
                                                                    ],
                                                                ) !!}
                                                            </div>
                                                            <div class="form-group">
                                                                {{ Form::label('facebook_redirect', __('Facebook Redirect Url'), ['class' => 'form-label']) }}
                                                                {!! Form::text(
                                                                    'facebook_redirect',
                                                                    Utility::getsettings('facebook_redirect') ? Utility::getsettings('facebook_redirect') : null,
                                                                    [
                                                                        'placeholder' => __('https://demo.test.com/callback/facebook'),
                                                                        'class' => 'form-control',
                                                                    ],
                                                                ) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="github">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapsethree"
                                                        aria-expanded="true" aria-controls="collapsethree">
                                                        <span class="flex-1 d-flex align-items-center">
                                                            <i class="ti ti-brand-github text-primary"></i>
                                                            {{ __('Github Setting') }}
                                                        </span>
                                                        @if (Utility::getsettings('githubsetting') == 'on')
                                                            <a
                                                                class="text-white btn btn-sm btn-primary float-end me-3">{{ __('Active') }}</a>
                                                        @endif
                                                    </button>
                                                </h2>
                                                <div id="collapsethree" class="accordion-collapse collapse"
                                                    aria-labelledby="github" data-bs-parent="#accordionExamples">
                                                    <div class="accordion-body">
                                                        <div class="col-12 d-flex justify-content-between">
                                                            <small
                                                                class="">{{ __('How To Enable Login With Github') }}
                                                                {!! Html::link(Storage::url('pdf/login with github.pdf'), __('Document'), [
                                                                    'target' => '_blank',
                                                                ]) !!}
                                                            </small>
                                                            <div class="form-check form-switch d-inline-block">
                                                                {!! Form::checkbox('socialsetting[]', 'github', Utility::getsettings('githubsetting') == 'on' ? true : false, [
                                                                    'class' => 'form-check-input',
                                                                    'id' => 'socialsetting_github',
                                                                ]) !!}
                                                                {{ Form::label('socialsetting_github', __('Enable'), ['class' => 'custom-control-label form-control-label']) }}
                                                            </div>
                                                        </div>
                                                        <div class="mt-2 row">
                                                            <div class="form-group">
                                                                {{ Form::label('github_client_id', __('Github Client Id'), ['class' => 'form-label']) }}
                                                                {!! Form::text(
                                                                    'github_client_id',
                                                                    Utility::getsettings('github_client_id') ? Utility::getsettings('github_client_id') : null,
                                                                    [
                                                                        'placeholder' => __('Enter github client id'),
                                                                        'class' => 'form-control',
                                                                    ],
                                                                ) !!}
                                                            </div>
                                                            <div class="form-group">
                                                                {{ Form::label('github_client_secret', __('Github Client Secret'), ['class' => 'form-label']) }}
                                                                {!! Form::text(
                                                                    'github_client_secret',
                                                                    Utility::getsettings('github_client_secret') ? Utility::getsettings('github_client_secret') : null,
                                                                    [
                                                                        'placeholder' => __('Enter github client secret'),
                                                                        'class' => 'form-control',
                                                                    ],
                                                                ) !!}
                                                            </div>
                                                            <div class="form-group">
                                                                {{ Form::label('github_redirect', __('Github Redirect Url'), ['class' => 'form-label']) }}
                                                                {!! Form::text(
                                                                    'github_redirect',
                                                                    Utility::getsettings('github_redirect') ? Utility::getsettings('github_redirect') : null,
                                                                    [
                                                                        'placeholder' => __('https://demo.test.com/callback/github'),
                                                                        'class' => 'form-control',
                                                                    ],
                                                                ) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="text-end">
                                    {{ Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) }}
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>

                        <div id="notification_setting" class="card">
                            <div class="card-header">
                                <h5>{{ __('Notifications Setting ') }}</h5>
                            </div>
                            <div class="pt-0 card-body">
                                <div class="mt-0 table-responsive">
                                    <table class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Title') }}</th>
                                                <th class="w-auto text-end">{{ __('Email') }}</th>
                                                <th class="w-auto text-end">{{ __('Notification') }}</th>
                                            </tr>
                                        </thead>
                                        @foreach ($notificationsSettings as $notificationsSetting)
                                            @if ($notificationsSetting->title != 'Form Create' && $notificationsSetting->title != 'new survey details')
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <div>
                                                                <span name="title" class="form-control"
                                                                    placeholder="Enter title"
                                                                    value="{{ $notificationsSetting->id }}">
                                                                    {{ $notificationsSetting->title }}</span>
                                                            </div>
                                                        </td>
                                                        @if ($notificationsSetting->email_notification != 2)
                                                            <td class="text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    {!! Form::checkbox('email_notification', null, $notificationsSetting->email_notification == 1 ? true : false, [
                                                                        'class' => 'form-check-input chnageEmailNotifyStatus',
                                                                        'data-url' => route('notification.status.change', $notificationsSetting->id),
                                                                    ]) !!}
                                                                </div>
                                                            </td>
                                                        @else
                                                            <td></td>
                                                        @endif
                                                        <td class="text-end">
                                                            <div class="form-check form-switch d-inline-block">
                                                                {!! Form::checkbox('notify', null, $notificationsSetting->notify == 1 ? true : false, [
                                                                    'class' => 'form-check-input chnageNotifyStatus',
                                                                    'data-url' => route('notification.status.change', $notificationsSetting->id),
                                                                ]) !!}
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            @endif
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div id="general-setting" class="card">
                        {!! Form::open([
                            'route' => 'settings.generalSetting.update',
                            'method' => 'POST',
                            'enctype' => 'multipart/form-data',
                            'data-validate',
                            'novalidate',
                        ]) !!}
                        <div class="card-header">
                            <h5> {{ __('General Setting') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group row">
                                        <div class="col-md-8 col-sm-6 col-12">
                                            <strong class="d-block">{{ __('Two Factor Authentication') }}</strong>
                                            {{ !Utility::getsettings('2fa') ? __('Activate') : __('Deactivate') }}
                                            {{ __('Two Factor Authentication For Application.') }}
                                        </div>
                                        <div class="col-md-4 col-sm-6 col-12 form-check form-switch custom-switch-v1">
                                            <label class="mt-2 custom-switch float-end">
                                                {!! Form::checkbox('two_factor_auth', null, Utility::getsettings('2fa') ? true : false, [
                                                    'class' => 'custom-control custom-switch form-check-input input-primary',
                                                    'data-onstyle' => 'primary',
                                                    'data-toggle' => 'switchbutton',
                                                ]) !!}
                                            </label>
                                        </div>
                                        @if (!extension_loaded('imagick'))
                                            <small>
                                                {{ __('Note: for 2FA your server must have Imagick.') }}
                                                {!! Html::link('https://www.php.net/manual/en/book.imagick.php', __('Imagick Document'), ['target' => '_new']) !!}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group row">
                                        <div class="col-md-8 col-sm-6 col-12">
                                            <strong class="d-block">{{ __('Email Verification') }}</strong>
                                            {{ Utility::getsettings('email_verification') == 0 ? __('Activate') : __('Deactivate') }}
                                            {{ __('Email Verification For Application') }}
                                        </div>
                                        <div class="col-md-4 col-sm-6 col-12 form-check form-switch custom-switch-v1">
                                            <label class="mt-2 custom-switch float-end">
                                                {!! Form::checkbox(
                                                    'email_verification',
                                                    null,
                                                    Utility::getsettings('email_verification') == '1' ? true : false,
                                                    [
                                                        'data-onstyle' => 'primary',
                                                        'class' => 'custom-control custom-switch form-check-input input-primary',
                                                        'data-toggle' => 'switchbutton',
                                                    ],
                                                ) !!}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group row">
                                        <div class="col-md-8 col-sm-6 col-12">
                                            <strong class="d-block">{{ __('Sms Verification') }}</strong>
                                            {{ Utility::getsettings('sms_verification') == 0 ? __('Activate') : __('Deactivate') }}
                                            {{ __('Sms Verification For Application') }}
                                        </div>
                                        <div class="col-md-4 col-sm-6 col-12 form-check form-switch custom-switch-v1">
                                            <label class="mt-2 custom-switch float-end">
                                                {!! Form::checkbox('sms_verification', null, Utility::getsettings('sms_verification') == '1' ? true : false, [
                                                    'data-onstyle' => 'primary',
                                                    'class' => 'custom-control custom-switch form-check-input input-primary',
                                                    'data-toggle' => 'switchbutton',
                                                ]) !!}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group row">
                                        <div class="col-md-8 col-sm-6 col-12">
                                            <strong class="d-block">{{ __('RTL Setting') }}</strong>
                                            {{ Utility::getsettings('rtl') == '1' ? __('Deactivate') : __('Activate') }}
                                            {{ __('Rtl Setting For Application.') }}
                                        </div>
                                        <div class="col-md-4 col-sm-6 col-12">
                                            <label class="mt-2 form-switch float-end custom-switch-v1">
                                                {!! Form::checkbox('rtl_setting', null, Utility::getsettings('rtl') == '1' ? true : false, [
                                                    'data-onstyle' => 'primary',
                                                    'id' => 'site_rtl',
                                                    'class' => 'custom-control custom-switch form-check-input input-primary',
                                                    'data-toggle' => 'switchbutton',
                                                ]) !!}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @if (\Auth::user()->type == 'Super Admin')
                                    <div class="col-sm-6">
                                        <div class="form-group row">
                                            <div class="col-md-8">
                                                <strong class="d-block">{{ __('Landing Page') }}</strong>
                                                {{ Utility::getsettings('landing_page_status') == '1' ? __('Deactivate') : __('Activate') }}
                                                {{ __('Landing Page For Application.') }}
                                            </div>
                                            <div class="col-md-4">
                                                <label class="mt-2 form-switch float-end custom-switch-v1">
                                                    {!! Form::checkbox(
                                                        'landing_page_status',
                                                        null,
                                                        Utility::getsettings('landing_page_status') == '1' ? true : false,
                                                        [
                                                            'data-onstyle' => 'primary',
                                                            'class' => 'custom-control custom-switch form-check-input input-primary',
                                                            'data-toggle' => 'switchbutton',
                                                        ],
                                                    ) !!}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="mt-2 col-sm-12">
                                    <div class="form-group d-flex align-items-center row">
                                        <h4 class="small-title">{{ __('Theme Customizer') }}</h4>
                                        <div class="setting-card setting-logo-box">
                                            <div class="row">
                                                <div class="col-lg-4 col-xl-4 col-md-4">
                                                    <h6 class="mt-2">
                                                        <i data-feather="credit-card"
                                                            class="me-2"></i>{{ __('Primary color settings') }}
                                                    </h6>
                                                    <hr class="my-2" />
                                                    <div class="theme-color themes-color">
                                                        <a href="#!"
                                                            class="{{ $color == 'theme-1' ? 'active_color' : '' }}"
                                                            data-value="theme-1" onclick="check_theme('theme-1')"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-1">
                                                        <a href="#!"
                                                            class="{{ $color == 'theme-2' ? 'active_color' : '' }}"
                                                            data-value="theme-2" onclick="check_theme('theme-2')"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-2">
                                                        <a href="#!"
                                                            class="{{ $color == 'theme-3' ? 'active_color' : '' }}"
                                                            data-value="theme-3" onclick="check_theme('theme-3')"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-3">
                                                        <a href="#!"
                                                            class="{{ $color == 'theme-4' ? 'active_color' : '' }}"
                                                            data-value="theme-4" onclick="check_theme('theme-4')"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-4">
                                                        <a href="#!"
                                                            class="{{ $color == 'theme-5' ? 'active_color' : '' }}"
                                                            data-value="theme-5" onclick="check_theme('theme-5')"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-5">
                                                        <br>
                                                        <a href="#!"
                                                            class="{{ $color == 'theme-6' ? 'active_color' : '' }}"
                                                            data-value="theme-6" onclick="check_theme('theme-6')"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-6">
                                                        <a href="#!"
                                                            class="{{ $color == 'theme-7' ? 'active_color' : '' }}"
                                                            data-value="theme-7" onclick="check_theme('theme-7')"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-7">
                                                        <a href="#!"
                                                            class="{{ $color == 'theme-8' ? 'active_color' : '' }}"
                                                            data-value="theme-8" onclick="check_theme('theme-8')"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-8">
                                                        <a href="#!"
                                                            class="{{ $color == 'theme-9' ? 'active_color' : '' }}"
                                                            data-value="theme-9" onclick="check_theme('theme-9')"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-9">
                                                        <a href="#!"
                                                            class="{{ $color == 'theme-10' ? 'active_color' : '' }}"
                                                            data-value="theme-10" onclick="check_theme('theme-10')"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-10">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-xl-4 col-md-4">
                                                    <h6 class="mt-2">
                                                        <i data-feather="layout"
                                                            class="me-2"></i>{{ __('Sidebar settings') }}
                                                    </h6>
                                                    <hr class="my-2" />
                                                    <div class="form-check form-switch">
                                                        {!! Form::checkbox(
                                                            'transparent_layout',
                                                            null,
                                                            Utility::getsettings('transparent_layout') == 'on' ? 'checked' : '',
                                                            [
                                                                'data-onstyle' => 'primary',
                                                                'id' => 'cust-theme-bg',
                                                                'class' => 'form-check-input',
                                                            ],
                                                        ) !!}
                                                        {!! Form::label('cust-theme-bg', __('Transparent Layout'), ['class' => 'form-check-label f-w-600 pl-1']) !!}
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-xl-4 col-md-4">
                                                    <h6 class="mt-2">
                                                        <i data-feather="sun"
                                                            class="me-2"></i>{{ __('Layout settings') }}
                                                    </h6>
                                                    <hr class="my-2" />
                                                    <div class="mt-2 form-check form-switch">
                                                        {!! Form::checkbox('dark_mode', null, Utility::getsettings('dark_mode') == 'on' ? true : false, [
                                                            'id' => 'cust-darklayout',
                                                            'class' => 'form-check-input',
                                                        ]) !!}
                                                        {!! Form::label('cust-darklayout', __('Dark Layout'), ['class' => 'form-check-label f-w-600 pl-1']) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        {{ Form::label('default_language', __('Default Language'), ['class' => 'form-label']) }}
                                        <select name="default_language" data-trigger class="form-control"
                                            id="default_language">
                                            @foreach (\App\Facades\UtilityFacades::languages() as $language)
                                                <option @if ($lang == $language) selected @endif
                                                    value="{{ $language }}">
                                                    {{ Str::upper($language) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        {{ Form::label('date_format', __('Date Format'), ['class' => 'form-label']) }}
                                        <select name="date_format" class="form-control" data-trigger id="date_format">
                                            <option value="M j, Y"
                                                {{ Utility::getsettings('date_format') == 'M j, Y' ? 'selected' : '' }}>
                                                {{ __('Jan 1, 2020') }}</option>
                                            <option value="d-M-y"
                                                {{ Utility::getsettings('date_format') == 'd-M-y' ? 'selected' : '' }}>
                                                {{ __('01-Jan-20') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        {{ Form::label('time_format', __('Time Format'), ['class' => 'form-label']) }}
                                        <select name="time_format" class="form-control" data-trigger id="time_format">
                                            <option value="g:i A"
                                                {{ Utility::getsettings('time_format') == 'g:i A' ? 'selected' : '' }}>
                                                {{ __('hh:mm AM/PM') }}</option>
                                            <option value="H:i:s"
                                                {{ Utility::getsettings('time_format') == 'H:i:s' ? 'selected' : '' }}>
                                                {{ __('HH:mm:ss') }}</option>
                                        </select>
                                    </div>
                                </div>
                                @hasrole('Super Admin')
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            {{ Form::label('gtag', __('Gtag Tracking ID'), ['class' => 'form-label']) }}
                                            {!! Html::link(
                                                'https://support.google.com/analytics/answer/1008080?hl=en#zippy=%2Cin-this-article',
                                                __('Document'),
                                                ['target' => '_blank'],
                                            ) !!}
                                            {!! Form::text('gtag', Utility::getsettings('gtag'), [
                                                'class' => 'form-control',
                                                'placeholder' => __('Enter gtag tracking id'),
                                            ]) !!}
                                        </div>
                                    </div>
                                @endhasrole
                                @if (\Auth::user()->type == 'Super Admin')
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label"
                                                for="name">{{ __('Approved Request') }}</label>
                                            <select name="approve_type" class="form-control" data-trigger
                                                placeholder="{{ __('Select option') }}">
                                                <option value="Manually"
                                                    {{ Utility::getsettings('approve_type') == __('Manually') ? 'selected' : '' }}>
                                                    {{ __('Manually') }}</option>
                                                <option value="Auto"
                                                    {{ Utility::getsettings('approve_type') == __('Auto') ? 'selected' : '' }}>
                                                    {{ __('Auto') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Social Login Role') }}</label>
                                            {!! Form::select('roles', $roles, Utility::getsettings('roles'), ['class' => 'form-control', 'data-trigger']) !!}
                                            <div class="invalid-feedback">
                                                {{ __('Role is required') }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="text-end">
                                {{ Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) }}
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                    <div id="email-setting" class="card">
                        <div class="card-header">
                            {!! Form::open([
                                'route' => 'settings.emailSetting.update',
                                'method' => 'POST',
                                'enctype' => 'multipart/form-data',
                                'data-validate',
                                'novalidate',
                            ]) !!}
                            <div class="row align-items-center">
                                <div class="col-lg-8">
                                    <h5> {{ __('Email Setting') }}</h5>
                                </div>
                                <div class="col-lg-4 d-flex justify-content-end">
                                    <div class="form-switch custom-switch-v1 d-inline-block">
                                        {!! Form::checkbox(
                                            'email_setting_enable',
                                            null,
                                            UtilityFacades::getsettings('email_setting_enable') == 'on' ? true : false,
                                            [
                                                'class' => 'custom-control custom-switch form-check-input input-primary',
                                                'id' => 'emailSettingEnableBtn',
                                                'data-onstyle' => 'primary',
                                                'data-toggle' => 'switchbutton',
                                            ],
                                        ) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body emailSettingEnableBtn">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        {{ Form::label('mail_mailer', __('Mail Mailer'), ['class' => 'form-label']) }}
                                        {!! Form::text('mail_mailer', UtilityFacades::getsettings('mail_mailer'), [
                                            'class' => 'form-control',
                                            'placeholder' => __('Enter mail mailer'),
                                        ]) !!}
                                    </div>
                                    <div class="form-group">
                                        {{ Form::label('mail_port', __('Mail Port'), ['class' => 'form-label']) }}
                                        {!! Form::text('mail_port', UtilityFacades::getsettings('mail_port'), [
                                            'class' => 'form-control',
                                            'placeholder' => __('Enter mail port'),
                                        ]) !!}
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        {{ Form::label('mail_host', __('Mail Host'), ['class' => 'form-label']) }}
                                        {!! Form::text('mail_host', UtilityFacades::getsettings('mail_host'), [
                                            'class' => 'form-control',
                                            'placeholder' => __('Enter mail host'),
                                        ]) !!}
                                    </div>
                                    <div class="form-group">
                                        {{ Form::label('mail_username', __('Mail Username'), ['class' => 'form-label']) }}
                                        {!! Form::text('mail_username', UtilityFacades::getsettings('mail_username'), [
                                            'class' => 'form-control',
                                            'placeholder' => __('Enter mail username'),
                                        ]) !!}
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        {{ Form::label('mail_password', __('Mail Password'), ['class' => 'form-label']) }}
                                        <input type="password" name="mail_password" class="form-control"
                                            value="{{ UtilityFacades::getsettings('mail_password') }}"
                                            id="mail_password" placeholder="{{ __('Enter mail password') }}">
                                    </div>
                                    <div class="form-group">
                                        {{ Form::label('mail_from_address', __('Mail From Address'), ['class' => 'form-label']) }}
                                        {!! Form::text('mail_from_address', UtilityFacades::getsettings('mail_from_address'), [
                                            'class' => 'form-control',
                                            'placeholder' => __('Enter mail from address'),
                                        ]) !!}
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        {{ Form::label('mail_encryption', __('Mail Encryption'), ['class' => 'form-label']) }}
                                        {!! Form::text('mail_encryption', UtilityFacades::getsettings('mail_encryption'), [
                                            'class' => 'form-control',
                                            'placeholder' => __('Enter mail encryption'),
                                        ]) !!}
                                    </div>
                                    <div class="form-group">
                                        {{ Form::label('mail_from_name', __('Mail From Name'), ['class' => 'form-label']) }}
                                        {!! Form::text('mail_from_name', UtilityFacades::getsettings('mail_from_name'), [
                                            'class' => 'form-control',
                                            'placeholder' => __('Enter mail from name'),
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="">
                                @if (UtilityFacades::getsettings('email_setting_enable') == 'on')
                                    {!! Form::button(__('Send Test Mail'), [
                                        'class' => 'btn btn-info send_mail float-start',
                                        'data-url' => route('test.mail'),
                                        'id' => 'test-mail',
                                    ]) !!}
                                @endif
                                {!! Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary float-end']) !!}
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                    @if (\Auth::user()->type == 'Admin')
                        <div id="captcha-setting" class="card">
                            {!! Form::open([
                                'route' => 'settings.captchaSetting.update',
                                'method' => 'POST',
                                'enctype' => 'multipart/form-data',
                                'data-validate',
                                'novalidate',
                            ]) !!}
                            <div class="card-header">
                                <div class="row d-flex align-items-center">
                                    <div class="col-6 d-flex justify-content-start">
                                        <h5>{{ __('Capcha Settings') }}</h5>
                                    </div>
                                    <div class="col-6 d-flex justify-content-end">
                                        <div class="form-switch custom-switch-v1 d-inline-block">
                                            {!! Form::checkbox(
                                                'captcha_enable',
                                                null,
                                                UtilityFacades::getsettings('captcha_enable') == 'on' ? true : false,
                                                [
                                                    'class' => 'custom-control custom-switch form-check-input input-primary',
                                                    'id' => 'captchaEnableButton',
                                                    'data-onstyle' => 'primary',
                                                    'data-toggle' => 'switchbutton',
                                                ],
                                            ) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body captchaSetting">
                                <div class="row" id="captchaSetting">
                                    <div class="form-group">
                                        {!! Form::radio('captcha', 'recaptcha', Utility::getsettings('captcha') == 'recaptcha' ? true : false, [
                                            'class' => 'btn-check',
                                            'id' => 'recahptchasetting',
                                        ]) !!}
                                        {!! Form::label('recahptchasetting', __('Recaptcha Setting'), ['class' => 'btn btn-outline-primary']) !!}
                                        {!! Form::radio('captcha', 'hcaptcha', Utility::getsettings('captcha') == 'hcaptcha' ? true : false, [
                                            'class' => 'btn-check',
                                            'id' => 'hcaptchasetting',
                                        ]) !!}
                                        {!! Form::label('hcaptchasetting', __('Hcaptcha Setting'), ['class' => 'btn btn-outline-primary']) !!}
                                    </div>
                                    <div id="recaptcha"
                                        class="desc {{ Utility::getsettings('captcha') != 'hcaptcha' ? 'd-block' : 'd-none' }}">
                                        <p class="text-muted"> {{ __('Recaptcha Setting') }}
                                            {!! Html::link('https://www.google.com/recaptcha/admin', __('Document'), [
                                                'target' => '_blank',
                                            ]) !!}
                                        </p>
                                        <div class="row">
                                            <div class="form-group">
                                                {{ Form::label('recaptcha_key', __('Recaptcha Key'), ['class' => 'form-label']) }}
                                                {!! Form::text('recaptcha_key', Utility::getsettings('recaptcha_key'), [
                                                    'class' => 'form-control',
                                                    'placeholder' => __('Enter recaptcha key'),
                                                ]) !!}
                                            </div>
                                            <div class="form-group">
                                                {{ Form::label('recaptcha_secret', __('Recaptcha Secret'), ['class' => 'form-label']) }}
                                                {!! Form::text('recaptcha_secret', Utility::getsettings('recaptcha_secret'), [
                                                    'class' => 'form-control',
                                                    'placeholder' => __('Enter recaptcha secret'),
                                                ]) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div id="hcaptcha"
                                        class="desc {{ Utility::getsettings('captcha') == 'hcaptcha' ? 'd-block' : 'd-none' }}">
                                        <p class="text-muted"> {{ __('Hcaptcha Setting') }}
                                            {!! Html::link('https://docs.hcaptcha.com/switch', __('Document'), ['target' => '_blank']) !!}
                                        </p>
                                        <div class="row">
                                            <div class="form-group">
                                                {{ Form::label('hcaptcha_key', __('Hcaptcha Key'), ['class' => 'form-label']) }}
                                                {!! Form::text('hcaptcha_key', Utility::getsettings('hcaptcha_key'), [
                                                    'class' => 'form-control',
                                                    'placeholder' => __('Enter hcaptcha key'),
                                                ]) !!}
                                            </div>
                                            <div class="form-group">
                                                {{ Form::label('hcaptcha_secret', __('Hcaptcha Secret'), ['class' => 'form-label']) }}
                                                {!! Form::text('hcaptcha_secret', Utility::getsettings('hcaptcha_secret'), [
                                                    'class' => 'form-control',
                                                    'placeholder' => __('Enter hcaptcha secret'),
                                                ]) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="text-end">
                                    {!! Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) !!}
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>

                        <div id="notification_setting" class="card">
                            <div class="card-header">
                                <h5>{{ __('Notifications setting') }}</h5>
                            </div>
                            <div class="pt-0 card-body">
                                <div class="mt-0 table-responsive">
                                    <table class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Title') }}</th>
                                                <th class="w-auto text-end">{{ __('Email') }}</th>
                                                <th class="w-auto text-end">{{ __('Notification') }}</th>
                                            </tr>
                                        </thead>
                                        @foreach ($notificationsSettings as $notificationsSetting)
                                            @if ($notificationsSetting->title != 'testing purpose')
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <div>
                                                                <span name="title" class="form-control"
                                                                    placeholder="Enter title"
                                                                    value="{{ $notificationsSetting->id }}">
                                                                    {{ $notificationsSetting->title }}</span>
                                                            </div>
                                                        </td>
                                                        @if ($notificationsSetting->email_notification != 2)
                                                            <td class="text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    {!! Form::checkbox('email_notification', null, $notificationsSetting->email_notification == 1 ? true : false, [
                                                                        'class' => 'form-check-input chnageEmailNotifyStatus',
                                                                        'data-url' => route('notification.status.change', $notificationsSetting->id),
                                                                    ]) !!}
                                                                </div>
                                                            </td>
                                                        @else
                                                            <td></td>
                                                        @endif
                                                        <td class="text-end">
                                                            <div class="form-check form-switch d-inline-block">
                                                                {!! Form::checkbox('notify', null, $notificationsSetting->notify == 1 ? true : false, [
                                                                    'class' => 'form-check-input chnageNotifyStatus',
                                                                    'data-url' => route('notification.status.change', $notificationsSetting->id),
                                                                ]) !!}
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            @endif
                                        @endforeach

                                    </table>
                                </div>
                            </div>
                        </div>
                        <div id="google-calender-setting" class="card">
                            <div class="col-md-12">
                                {{ Form::open(['route' => 'settings.googleCalender.update', 'enctype' => 'multipart/form-data', 'data-validate', 'novalidate']) }}
                                <div class="card-header">
                                    <div class="row d-flex align-items-center">
                                        <div class="col-6">
                                            <h5 class="mb-2">{{ __('Google Calendar Setting') }}</h5>
                                        </div>
                                        <div class="col-6 text-end">
                                            <div class="form-switch custom-switch-v1 d-inline-block">
                                                {!! Form::checkbox(
                                                    'google_calendar_enable',
                                                    null,
                                                    UtilityFacades::getsettings('google_calendar_enable') == 'on' ? true : false,
                                                    [
                                                        'class' => 'custom-switch custom-control form-check-input input-primary',
                                                        'data-toggle' => 'switchbutton',
                                                        'id' => 'google_calender',
                                                    ],
                                                ) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body google_calender ">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                                            {{ Form::label('google_calendar_id', __('Google Calendar Id'), ['class' => 'form-label']) }}
                                            {{ Form::text('google_calendar_id', UtilityFacades::getsettings('google_calendar_id'), ['class' => 'form-control ', 'placeholder' => 'Google Calendar Id', 'required' => 'required']) }}
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                                            {{ Form::label('google_calendar_json_file', __('Google Calendar json File'), ['class' => 'form-label']) }}
                                            {{ Form::file('google_calendar_json_file', ['class' => 'form-control', 'required' => 'required']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="text-end">
                                        {!! Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) !!}
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>

                        <div id="google-map-setting" class="card">
                            <div class="col-md-12">
                                {{ Form::open(['route' => 'settings.googleMap.update', 'enctype' => 'multipart/form-data', 'data-validate', 'novalidate']) }}
                                <div class="card-header">
                                    <div class="row d-flex align-items-center">
                                        <div class="col-6">
                                            <h5 class="mb-2">{{ __('Google Map Setting') }}</h5>
                                        </div>
                                        <div class="col-6 text-end">
                                            <div class="form-switch custom-switch-v1 d-inline-block">
                                                {!! Form::checkbox(
                                                    'google_map_enable',
                                                    null,
                                                    UtilityFacades::getsettings('google_map_enable') == 'on' ? true : false,
                                                    [
                                                        'class' => 'custom-switch custom-control form-check-input input-primary',
                                                        'data-toggle' => 'switchbutton',
                                                        'id' => 'google_map',
                                                    ],
                                                ) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body google_map ">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                                            {{ Form::label('google_map_api', __('Google Map Api Kay'), ['class' => 'form-label']) }}
                                            {{ Form::text('google_map_api', UtilityFacades::getsettings('google_map_api'), ['class' => 'form-control ', 'placeholder' => 'Enter MAp API key', 'required' => 'required']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="text-end">
                                        {!! Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) !!}
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    @endif

                    <div id="payment-setting" class="card">
                        {!! Form::open([
                            'route' => 'settings.paymentSetting.update',
                            'method' => 'POST',
                            'enctype' => 'multipart/form-data',
                            'data-validate',
                            'novalidate',
                        ]) !!}
                        <div class="card-header">
                            <h5> {{ __('Payment Setting') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="faq justify-content-center">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-group">
                                            {{ Form::label('currency', __('Currency'), ['class' => 'form-label']) }}
                                            {!! Form::text('currency', env('CURRENCY'), [
                                                'class' => 'form-control',
                                                'placeholder' => __('Enter currency'),
                                                'required',
                                            ]) !!}
                                            <small class="text-xs">
                                                {{ __('Note: Add currency code as per three-letter ISO code.') }}
                                                <a href="https://stripe.com/docs/currencies"
                                                    target="_blank">{{ __('You can find out how to do that here.') }}</a>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-group">
                                            {{ Form::label('currency_symbol', __('Currency Symbol'), ['class' => 'form-label']) }}
                                            {!! Form::text('currency_symbol', env('CURRENCY_SYMBOL'), [
                                                'class' => 'form-control',
                                                'placeholder' => __('Enter currency symbol'),
                                                'required',
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-xxl-12">
                                        <div class="accordion accordion-flush" id="accordionExample">
                                            <!-- Stripe -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-2">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse1"
                                                        aria-expanded="true" aria-controls="collapse1">
                                                        <span class="flex-1 d-flex align-items-center">
                                                            <i class="ti ti-credit-card text-primary"></i>
                                                            {{ __('Stripe') }}
                                                        </span>
                                                        @if (Utility::getsettings('stripesetting') == 'on')
                                                            <a
                                                                class="text-white btn btn-sm btn-primary float-end me-3">{{ __('Active') }}</a>
                                                        @endif
                                                    </button>
                                                </h2>
                                                <div id="collapse1" class="accordion-collapse collapse"
                                                    aria-labelledby="heading-2-2" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="py-1 col-6">
                                                            </div>
                                                            <div class="py-1 col-6 text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    {!! Form::checkbox(
                                                                        'paymentsetting[]',
                                                                        'stripe',
                                                                        UtilityFacades::getsettings('stripesetting') == 'on' ? true : false,
                                                                        [
                                                                            'class' => 'form-check-input mx-2',
                                                                            'id' => 'payment_stripe',
                                                                        ],
                                                                    ) !!}
                                                                    {{ Form::label('payment_stripe', __('Enable'), ['class' => 'form-check-label']) }}
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                <div class="form-group">
                                                                    {{ Form::label('stripe_key', __('Stripe Key'), ['class' => 'form-label']) }}
                                                                    {{ Form::text('stripe_key', UtilityFacades::getsettings('stripe_key'), ['class' => 'form-control', 'placeholder' => __('Enter stripe key')]) }}
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                <div class="form-group">
                                                                    {{ Form::label('stripe_secret', __('Stripe Secret'), ['class' => 'form-label']) }}
                                                                    {{ Form::text('stripe_secret', UtilityFacades::getsettings('stripe_secret'), ['class' => 'form-control ', 'placeholder' => __('Enter stripe secret')]) }}
                                                                </div>
                                                            </div>
                                                            @if (Auth::user()->type == 'Super Admin')
                                                                <div class="col-lg-12">
                                                                    <div class="form-group">
                                                                        {{ Form::label('stripe_description', __('Description'), ['class' => 'form-label']) }}
                                                                        {{ Form::text('stripe_description', UtilityFacades::getsettings('stripe_description'), ['class' => 'form-control ', 'placeholder' => __('Enter description')]) }}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Razorpay -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-3">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse2"
                                                        aria-expanded="true" aria-controls="collapse2">
                                                        <span class="flex-1 d-flex align-items-center">
                                                            <i class="ti ti-credit-card text-primary"></i>
                                                            {{ __('Razorpay') }}
                                                        </span>
                                                        @if (UtilityFacades::getsettings('razorpaysetting') == 'on')
                                                            <a
                                                                class="text-white btn btn-sm btn-primary float-end me-3">{{ __('Active') }}</a>
                                                        @endif
                                                    </button>
                                                </h2>
                                                <div id="collapse2" class="accordion-collapse collapse"
                                                    aria-labelledby="heading-2-3" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="py-1 col-6">
                                                            </div>
                                                            <div class="py-1 col-6 text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    {!! Form::checkbox(
                                                                        'paymentsetting[]',
                                                                        'razorpay',
                                                                        UtilityFacades::getsettings('razorpaysetting') == 'on' ? true : false,
                                                                        [
                                                                            'class' => 'form-check-input mx-2',
                                                                            'id' => 'payment_razorpay',
                                                                        ],
                                                                    ) !!}
                                                                    {{ Form::label('payment_razorpay', __('Enable'), ['class' => 'form-check-label']) }}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{ Form::label('razorpay_key', __('Razorpay Key'), ['class' => 'form-label']) }}
                                                                    {!! Form::text('razorpay_key', UtilityFacades::getsettings('razorpay_key'), [
                                                                        'class' => 'form-control',
                                                                        'placeholder' => __('Enter razorpay key'),
                                                                    ]) !!}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{ Form::label('razorpay_secret', __('Razorpay Secret'), ['class' => 'form-label']) }}
                                                                    {!! Form::text('razorpay_secret', UtilityFacades::getsettings('razorpay_secret'), [
                                                                        'class' => 'form-control',
                                                                        'placeholder' => __('Enter razorpay secret'),
                                                                    ]) !!}
                                                                </div>
                                                            </div>
                                                            @if (Auth::user()->type == 'Super Admin')
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        {{ Form::label('razorpay_description', __('Description'), ['class' => 'form-label']) }}
                                                                        {{ Form::text('razorpay_description', UtilityFacades::getsettings('razorpay_description'), ['class' => 'form-control ', 'placeholder' => __('Enter description')]) }}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Paypal -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-4">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse3"
                                                        aria-expanded="true" aria-controls="collapse3">
                                                        <span class="flex-1 d-flex align-items-center">
                                                            <i class="ti ti-credit-card text-primary"></i>
                                                            {{ __('Paypal') }}
                                                        </span>
                                                        @if (UtilityFacades::getsettings('paypalsetting') == 'on')
                                                            <a
                                                                class="text-white btn btn-sm btn-primary float-end me-3">{{ __('Active') }}</a>
                                                        @endif
                                                    </button>
                                                </h2>
                                                <div id="collapse3" class="accordion-collapse collapse"
                                                    aria-labelledby="heading-2-4" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-12 text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    {!! Form::checkbox(
                                                                        'paymentsetting[]',
                                                                        'paypal',
                                                                        UtilityFacades::getsettings('paypalsetting') == 'on' ? true : false,
                                                                        [
                                                                            'class' => 'form-check-input mx-2',
                                                                            'id' => 'payment_paypal',
                                                                        ],
                                                                    ) !!}
                                                                    {{ Form::label('payment_paypal', __('Enable'), ['class' => 'form-check-label']) }}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                {{ Form::label('paypal_mode', __('Paypal Environment'), ['class' => 'paypal-label form-label']) }}
                                                                <br>
                                                                <div class="d-flex">
                                                                    <div class="ms-2">
                                                                        <div class="p-3 border card">
                                                                            <div class="form-check">
                                                                                <label class="form-check-labe text-dark">
                                                                                    {!! Form::radio(
                                                                                        'paypal_mode',
                                                                                        'sandbox',
                                                                                        UtilityFacades::getsettings('paypal_mode') == 'sandbox' ? true : false,
                                                                                        ['class' => 'form-check-input', 'id' => 'paypal'],
                                                                                    ) !!}{{ __('Sandbox') }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="ms-2">
                                                                        <div class="p-3 border card">
                                                                            <div class="form-check">
                                                                                <label class="form-check-labe text-dark">
                                                                                    {!! Form::radio('paypal_mode', 'live', UtilityFacades::getsettings('paypal_mode') == 'live' ? true : false, [
                                                                                        'class' => 'form-check-input',
                                                                                    ]) !!}{{ __('Live') }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{ Form::label('client_id', __('Paypal Key'), ['class' => 'form-label']) }}
                                                                    {!! Form::text('client_id', UtilityFacades::getsettings('paypal_sandbox_client_id'), [
                                                                        'class' => 'form-control',
                                                                        'placeholder' => __('Enter paypal key'),
                                                                    ]) !!}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{ Form::label('client_secret', __('Paypal Secret'), ['class' => 'form-label']) }}
                                                                    {!! Form::text('client_secret', UtilityFacades::getsettings('paypal_sandbox_client_secret'), [
                                                                        'class' => 'form-control',
                                                                        'placeholder' => __('Enter paypal secret'),
                                                                    ]) !!}
                                                                </div>
                                                            </div>
                                                            @if (Auth::user()->type == 'Super Admin')
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        {{ Form::label('paypal_description', __('Description'), ['class' => 'form-label']) }}
                                                                        {{ Form::text('paypal_description', UtilityFacades::getsettings('paypal_description'), ['class' => 'form-control ', 'placeholder' => __('Enter description')]) }}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="paytm">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapsepaytm"
                                                        aria-expanded="true" aria-controls="collapsepaytm">
                                                        <span class="flex-1 d-flex align-items-center">
                                                            <i class="ti ti-credit-card text-primary"></i>
                                                            {{ __('Paytm') }}
                                                        </span>
                                                        @if (UtilityFacades::getsettings('paytmsetting') == 'on')
                                                            <a
                                                                class="text-white btn btn-sm btn-primary float-end me-3">{{ __('Active') }}</a>
                                                        @endif
                                                    </button>
                                                </h2>
                                                <div id="collapsepaytm" class="accordion-collapse collapse"
                                                    aria-labelledby="paytm" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-12 d-flex justify-content-end text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    {{ Form::label('payment_paytm', __('Enable'), ['class' => 'custom-control-label form-control-label']) }}
                                                                    {!! Form::checkbox(
                                                                        'paymentsetting[]',
                                                                        'paytm',
                                                                        UtilityFacades::getsettings('paytmsetting') == 'on' ? true : false,
                                                                        [
                                                                            'class' => 'form-check-input',
                                                                            'id' => 'payment_paytm',
                                                                        ],
                                                                    ) !!}
                                                                </div>
                                                            </div>
                                                            <div class="mt-2 row">
                                                                <div class="col-md-12">
                                                                    {{ Form::label('paytm_mode', __('Paytm Environment'), ['class' => 'paypal-label form-label']) }}
                                                                    <div class="d-flex">
                                                                        <div class="ms-2">
                                                                            <div class="p-3 border card">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-labe text-dark">
                                                                                        {!! Form::radio(
                                                                                            'paytm_environment',
                                                                                            'local',
                                                                                            UtilityFacades::getsettings('paytm_environment') == 'local' ? true : false,
                                                                                            ['class' => 'form-check-input'],
                                                                                        ) !!}{{ __('Local') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="ms-2">
                                                                            <div class="p-3 border card">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-labe text-dark">
                                                                                        {!! Form::radio(
                                                                                            'paytm_environment',
                                                                                            'production',
                                                                                            UtilityFacades::getsettings('paytm_environment') == 'production' ? true : false,
                                                                                            ['class' => 'form-check-input'],
                                                                                        ) !!}{{ __('Production') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="form-group">
                                                                        {{ Form::label('merchant_id', __('Paytm Merchant Id'), ['class' => 'form-label']) }}
                                                                        {!! Form::text('merchant_id', Utility::getsettings('paytm_merchant_id'), [
                                                                            'class' => 'form-control',
                                                                            'placeholder' => __('Enter paytm merchant id'),
                                                                        ]) !!}
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="form-group">
                                                                        {{ Form::label('merchant_key', __('Paytm Merchant Key'), ['class' => 'form-label']) }}
                                                                        {!! Form::text('merchant_key', Utility::getsettings('paytm_merchant_key'), [
                                                                            'class' => 'form-control',
                                                                            'placeholder' => __('Enter paytm merchant key'),
                                                                        ]) !!}
                                                                    </div>
                                                                </div>
                                                                @if (Auth::user()->type == 'Super Admin')
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('paytm_description', __('Description'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('paytm_description', Utility::getsettings('paytm_description'), ['class' => 'form-control ', 'placeholder' => __('Enter description')]) }}
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        {{ Form::label('paytm_currency', __('Paytm Currency'), ['class' => 'form-label']) }}
                                                                        <select name="paytm_currency" class="form-select"
                                                                            data-trigger id="paytm_currency">
                                                                            <option value="INR"> {{ __('INR') }}
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-5">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse4"
                                                        aria-expanded="true" aria-controls="collapse4">
                                                        <span class="flex-1 d-flex align-items-center">
                                                            <i class="ti ti-credit-card text-primary"></i>
                                                            {{ __('Flutterwave') }}
                                                        </span>
                                                        @if (Utility::getsettings('flutterwavesetting') == 'on')
                                                            <a
                                                                class="text-white btn btn-sm btn-primary float-end me-3">{{ __('Active') }}</a>
                                                        @endif
                                                    </button>
                                                </h2>
                                                <div id="collapse4" class="accordion-collapse collapse"
                                                    aria-labelledby="heading-2-5" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="py-1 col-6">
                                                            </div>
                                                            <div class="py-1 col-6 text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    {!! Form::checkbox(
                                                                        'paymentsetting[]',
                                                                        'flutterwave',
                                                                        Utility::getsettings('flutterwavesetting') == 'on' ? true : false,
                                                                        [
                                                                            'class' => 'form-check-input mx-2',
                                                                            'id' => 'payment_flutterwave',
                                                                        ],
                                                                    ) !!}
                                                                    {{ Form::label('payment_flutterwave', __('Enable'), ['class' => 'form-check-label']) }}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{ Form::label('flw_public_key', __('Flutterwave Key'), ['class' => 'form-label']) }}
                                                                    {!! Form::text('flw_public_key', Utility::getsettings('flw_public_key'), [
                                                                        'class' => 'form-control',
                                                                        'placeholder' => __('Enter flutterwave key'),
                                                                    ]) !!}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{ Form::label('flw_secret_key', __('Flutterwave Secret'), ['class' => 'form-label']) }}
                                                                    {!! Form::text('flw_secret_key', Utility::getsettings('flw_secret_key'), [
                                                                        'class' => 'form-control',
                                                                        'placeholder' => __('Enter flutterwave secret'),
                                                                    ]) !!}
                                                                </div>
                                                            </div>
                                                            @if (Auth::user()->type == 'Super Admin')
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        {{ Form::label('flutterwave_description', __('Description'), ['class' => 'form-label']) }}
                                                                        {{ Form::text('flutterwave_description', Utility::getsettings('flutterwave_description'), ['class' => 'form-control ', 'placeholder' => __('Enter description')]) }}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- paystack -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-7">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse6"
                                                        aria-expanded="true" aria-controls="collapse6">
                                                        <span class="flex-1 d-flex align-items-center">
                                                            <i class="ti ti-credit-card text-primary"></i>
                                                            {{ __('Paystack') }}
                                                        </span>
                                                        @if (Utility::getsettings('paystacksetting') == 'on')
                                                            <a
                                                                class="text-white btn btn-sm btn-primary float-end me-3">{{ __('Active') }}</a>
                                                        @endif
                                                    </button>
                                                </h2>
                                                <div id="collapse6" class="accordion-collapse collapse"
                                                    aria-labelledby="heading-2-7" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="py-1 col-6">
                                                            </div>
                                                            <div class="py-1 col-6 text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    {!! Form::checkbox(
                                                                        'paymentsetting[]',
                                                                        'paystack',
                                                                        Utility::getsettings('paystacksetting') == 'on' ? true : false,
                                                                        [
                                                                            'class' => 'form-check-input mx-2',
                                                                            'id' => 'payment_paystack',
                                                                        ],
                                                                    ) !!}
                                                                    {{ Form::label('payment_paystack', __('Enable'), ['class' => 'form-check-label']) }}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{ Form::label('paystack_public_key', __('Public key'), ['class' => 'form-label']) }}
                                                                    {!! Form::text('paystack_public_key', Utility::getsettings('paystack_public_key'), [
                                                                        'class' => 'form-control',
                                                                        'placeholder' => __('Enter public key'),
                                                                    ]) !!}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{ Form::label('paystack_secret_key', __('Enter secret key'), ['class' => 'form-label']) }}
                                                                    {!! Form::text('paystack_secret_key', Utility::getsettings('paystack_secret_key'), [
                                                                        'class' => 'form-control',
                                                                        'placeholder' => __('Enter secret key'),
                                                                    ]) !!}
                                                                </div>
                                                            </div>
                                                            @if (Auth::user()->type == 'Super Admin')
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        {{ Form::label('paystack_description', __('Description'), ['class' => 'form-label']) }}
                                                                        {{ Form::text('paystack_description', Utility::getsettings('paystack_description'), ['class' => 'form-control ', 'placeholder' => __('Enter description')]) }}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{ Form::label('paystack_currency', __('Paystack Currency'), ['class' => 'form-label']) }}
                                                                    <select name="paystack_currency" class="form-select"
                                                                        data-trigger id="paystack_currency">
                                                                        <option value="NGN"> {{ __('NGN') }}
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Cashfree -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-13">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse13"
                                                        aria-expanded="true" aria-controls="collapse13">
                                                        <span class="flex-1 d-flex align-items-center">
                                                            <i class="ti ti-credit-card text-primary"></i>
                                                            {{ __('Cashfree') }}
                                                        </span>
                                                        @if (UtilityFacades::getsettings('cashfreesetting') == 'on')
                                                            <a
                                                                class="text-white btn btn-sm btn-primary float-end me-3">{{ __('Active') }}</a>
                                                        @endif
                                                    </button>
                                                </h2>
                                                <div id="collapse13" class="accordion-collapse collapse"
                                                    aria-labelledby="heading-2-13" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-md-12 d-flex justify-content-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    {!! Form::checkbox(
                                                                        'paymentsetting[]',
                                                                        'cashfree',
                                                                        UtilityFacades::getsettings('cashfreesetting') == 'on' ? true : false,
                                                                        [
                                                                            'class' => 'form-check-input',
                                                                            'id' => 'payment_cashfree',
                                                                        ],
                                                                    ) !!}
                                                                    {{ Form::label('payment_cashfree', __('Enable'), ['class' => 'custom-control-label form-control-label']) }}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{ Form::label('cashfree_app_id', __('Cashfree App Id'), ['class' => 'form-label']) }}
                                                                    {!! Form::text('cashfree_app_id', UtilityFacades::getsettings('cashfree_app_id'), [
                                                                        'class' => 'form-control',
                                                                        'placeholder' => __('Enter cashfree app id'),
                                                                        'id' => 'cashfree_app_id',
                                                                    ]) !!}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{ Form::label('cashfree_secret_key', __('Cashfree Secret Key'), ['class' => 'form-label']) }}
                                                                    {!! Form::text('cashfree_secret_key', UtilityFacades::getsettings('cashfree_secret_key'), [
                                                                        'class' => 'form-control',
                                                                        'placeholder' => __('Enter cashfree secret key'),
                                                                        'id' => 'cashfree_secret_key',
                                                                    ]) !!}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{ Form::label('cashfree_url', __('Cashfree Url'), ['class' => 'form-label']) }}
                                                                    {!! Form::text('cashfree_url', UtilityFacades::getsettings('cashfree_url'), [
                                                                        'class' => 'form-control',
                                                                        'placeholder' => __('Enter cashfree url'),
                                                                        'id' => 'cashfree_url',
                                                                    ]) !!}
                                                                </div>
                                                            </div>
                                                            @if (Auth::user()->type == 'Super Admin')
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        {{ Form::label('cashfree_description', __('Description'), ['class' => 'form-label']) }}
                                                                        {{ Form::text('cashfree_description', UtilityFacades::getsettings('cashfree_description'), ['class' => 'form-control ', 'placeholder' => __('Enter description')]) }}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- mercado -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-11">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse11"
                                                        aria-expanded="true" aria-controls="collapse11">
                                                        <span class="flex-1 d-flex align-items-center">
                                                            <i class="ti ti-credit-card text-primary"></i>
                                                            {{ __('Mercado Pago') }}
                                                        </span>
                                                        @if (Utility::getsettings('mercadosetting') == 'on')
                                                            <a
                                                                class="text-white btn btn-sm btn-primary float-end me-3">{{ __('Active') }}</a>
                                                        @endif
                                                    </button>
                                                </h2>
                                                <div id="collapse11" class="accordion-collapse collapse"
                                                    aria-labelledby="heading-2-11" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="py-1 col-12 text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    {!! Form::checkbox(
                                                                        'paymentsetting[]',
                                                                        'mercado',
                                                                        Utility::getsettings('mercadosetting') == 'on' ? true : false,
                                                                        [
                                                                            'class' => 'form-check-input mx-2',
                                                                            'id' => 'payment_mercado',
                                                                        ],
                                                                    ) !!}
                                                                    {{ Form::label('payment_mercado', __('Enable'), ['class' => 'form-check-label']) }}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                {{ Form::label('mercado_mode', __('Mercado Environment'), ['class' => 'paypal-label form-label']) }}
                                                                <br>
                                                                <div class="d-flex">
                                                                    <div class="ms-2">
                                                                        <div class="p-3 border card">
                                                                            <div class="form-check">
                                                                                <label class="form-check-labe text-dark">
                                                                                    {!! Form::radio('mercado_mode', 'sandbox', Utility::getsettings('mercado_mode') == 'sandbox' ? true : false, [
                                                                                        'class' => 'form-check-input',
                                                                                        'id' => 'mercado',
                                                                                    ]) !!}{{ __('Sandbox') }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="ms-2">
                                                                        <div class="p-3 border card">
                                                                            <div class="form-check">
                                                                                <label class="form-check-labe text-dark">
                                                                                    {!! Form::radio('mercado_mode', 'live', Utility::getsettings('mercado_mode') == 'live' ? true : false, [
                                                                                        'class' => 'form-check-input',
                                                                                    ]) !!}{{ __('Live') }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{ Form::label('mercado_access_token', __('Mercado Access Token'), ['class' => 'form-label']) }}
                                                                    {!! Form::text('mercado_access_token', Utility::getsettings('mercado_access_token'), [
                                                                        'class' => 'form-control',
                                                                        'placeholder' => __('Enter mercado access token'),
                                                                        'id' => 'mercado_access_token',
                                                                    ]) !!}
                                                                </div>
                                                            </div>
                                                            @if (Auth::user()->type == 'Super Admin')
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        {{ Form::label('mercado_description', __('Description'), ['class' => 'form-label']) }}
                                                                        {{ Form::text('mercado_description', Utility::getsettings('mercado_description'), [
                                                                            'class' => 'form-control ',
                                                                            'placeholder' => __('Enter description'),
                                                                        ]) }}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-11">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse10"
                                                        aria-expanded="true" aria-controls="collapse10">
                                                        <span class="flex-1 d-flex align-items-center">
                                                            <i class="ti ti-credit-card text-primary"></i>
                                                            {{ __('Coingate') }}
                                                        </span>
                                                        @if (UtilityFacades::getsettings('coingatesetting') == 'on')
                                                            <a
                                                                class="text-white btn btn-sm btn-primary float-end me-3">{{ __('Active') }}</a>
                                                        @endif
                                                    </button>
                                                </h2>
                                                <div id="collapse10" class="accordion-collapse collapse"
                                                    aria-labelledby="heading-2-11" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-md-12 d-flex justify-content-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    {!! Form::checkbox(
                                                                        'paymentsetting[]',
                                                                        'coingate',
                                                                        UtilityFacades::getsettings('coingatesetting') == 'on' ? true : false,
                                                                        [
                                                                            'class' => 'form-check-input',
                                                                            'id' => 'payment_coingate',
                                                                        ],
                                                                    ) !!}
                                                                    {{ Form::label('payment_coingate', __('Enable'), ['class' => 'custom-control-label form-control-label']) }}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                {{ Form::label('coingate_mode', __('CoinGate Mode'), ['class' => 'form-label']) }}
                                                                <br>
                                                                <div class="d-flex">
                                                                    <div class="ms-2">
                                                                        <div class="p-3 border card">
                                                                            <div class="form-check">
                                                                                <label class="form-check-labe text-dark">
                                                                                    {!! Form::radio(
                                                                                        'coingate_mode',
                                                                                        'sandbox',
                                                                                        UtilityFacades::getsettings('coingate_environment') == 'sandbox' ? true : false,
                                                                                        ['class' => 'form-check-input', 'id' => 'coingate'],
                                                                                    ) !!}{{ __('Sandbox') }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="ms-2">
                                                                        <div class="p-3 border card">
                                                                            <div class="form-check">
                                                                                <label class="form-check-labe text-dark">
                                                                                    {!! Form::radio(
                                                                                        'coingate_mode',
                                                                                        'live',
                                                                                        UtilityFacades::getsettings('coingate_environment') == 'live' ? true : false,
                                                                                        ['class' => 'form-check-input'],
                                                                                    ) !!}{{ __('Live') }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{ Form::label('coingate_auth_token', __('CoinGate Auth Token'), ['class' => 'form-label']) }}
                                                                    {!! Form::text('coingate_auth_token', UtilityFacades::getsettings('coingate_auth_token'), [
                                                                        'class' => 'form-control',
                                                                        'placeholder' => __('Enter coinGate auth token'),
                                                                        'id' => 'coingate_auth_token',
                                                                    ]) !!}
                                                                </div>
                                                            </div>
                                                            @if (Auth::user()->type == 'Super Admin')
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        {{ Form::label('coingate_description', __('Description'), ['class' => 'form-label']) }}
                                                                        {{ Form::text('coingate_description', UtilityFacades::getsettings('coingate_description'), ['class' => 'form-control ', 'placeholder' => __('Enter description')]) }}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-sspay">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse-sspay"
                                                        aria-expanded="true" aria-controls="collapse-sspay">
                                                        <span class="flex-1 d-flex align-items-center">
                                                            <i class="ti ti-credit-card text-primary"></i>
                                                            {{ __('SSPay') }}
                                                        </span>
                                                        @if (UtilityFacades::getsettings('sspaysetting') == 'on')
                                                            <a
                                                                class="text-white btn btn-sm btn-primary float-end me-3">{{ __('Active') }}</a>
                                                        @endif
                                                    </button>
                                                </h2>
                                                <div id="collapse-sspay" class="accordion-collapse collapse"
                                                    aria-labelledby="heading-2-sspay" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-md-12 d-flex justify-content-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    {!! Form::checkbox(
                                                                        'paymentsetting[]',
                                                                        'sspay',
                                                                        UtilityFacades::getsettings('sspaysetting') == 'on' ? true : false,
                                                                        [
                                                                            'class' => 'form-check-input',
                                                                            'id' => 'payment_sspay',
                                                                        ],
                                                                    ) !!}
                                                                    {{ Form::label('payment_sspay', __('Enable'), ['class' => 'custom-control-label form-control-label']) }}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{ Form::label('sspay_category_code', __('SSPay Category Code'), ['class' => 'form-label']) }}
                                                                    {!! Form::text('sspay_category_code', UtilityFacades::getsettings('sspay_category_code'), [
                                                                        'class' => 'form-control',
                                                                        'placeholder' => __('Enter sspay category code'),
                                                                    ]) !!}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{ Form::label('sspay_secret_key', __('SSPay Secret Key'), ['class' => 'form-label']) }}
                                                                    {!! Form::text('sspay_secret_key', UtilityFacades::getsettings('sspay_secret_key'), [
                                                                        'class' => 'form-control',
                                                                        'placeholder' => __('Enter sspay secret key'),
                                                                    ]) !!}
                                                                </div>
                                                            </div>
                                                            @if (Auth::user()->type == 'Super Admin')
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        {{ Form::label('sspay_description', __('Description'), ['class' => 'form-label']) }}
                                                                        {{ Form::text('sspay_description', UtilityFacades::getsettings('sspay_description'), ['class' => 'form-control ', 'placeholder' => __('Enter description')]) }}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Paytab -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-paytab">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse-paytab"
                                                        aria-expanded="true" aria-controls="collapse-paytab">
                                                        <span class="flex-1 d-flex align-items-center">
                                                            <i class="ti ti-credit-card text-primary"></i>
                                                            {{ __('Paytab') }}
                                                        </span>
                                                        @if (UtilityFacades::getsettings('paytabsetting') == 'on')
                                                            <a
                                                                class="text-white btn btn-sm btn-primary float-end me-3">{{ __('Active') }}</a>
                                                        @endif
                                                    </button>
                                                </h2>
                                                <div id="collapse-paytab" class="accordion-collapse collapse"
                                                    aria-labelledby="heading-2-paytab" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-md-12 d-flex justify-content-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    {!! Form::checkbox(
                                                                        'paymentsetting[]',
                                                                        'paytab',
                                                                        UtilityFacades::getsettings('paytabsetting') == 'on' ? true : false,
                                                                        [
                                                                            'class' => 'form-check-input',
                                                                            'id' => 'payment_paytab',
                                                                        ],
                                                                    ) !!}
                                                                    {{ Form::label('payment_paytab', __('Enable'), ['class' => 'custom-control-label form-control-label']) }}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{ Form::label('paytab_profile_id', __('Paytab Profile Id'), ['class' => 'form-label']) }}
                                                                    {!! Form::text('paytab_profile_id', UtilityFacades::getsettings('paytab_profile_id'), [
                                                                        'class' => 'form-control',
                                                                        'placeholder' => __('Enter Paytab Profile Id'),
                                                                    ]) !!}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{ Form::label('paytab_server_key', __('Paytab Server Key'), ['class' => 'form-label']) }}
                                                                    {!! Form::text('paytab_server_key', UtilityFacades::getsettings('paytab_server_key'), [
                                                                        'class' => 'form-control',
                                                                        'placeholder' => __('Enter Paytab Server Key'),
                                                                    ]) !!}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{ Form::label('paytab_region', __('Paytab Region'), ['class' => 'form-label']) }}
                                                                    {!! Form::text('paytab_region', UtilityFacades::getsettings('paytab_region'), [
                                                                        'class' => 'form-control',
                                                                        'placeholder' => __('Enter Paytab Region'),
                                                                    ]) !!}
                                                                </div>
                                                            </div>
                                                            @if (Auth::user()->type == 'Super Admin')
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        {{ Form::label('paytab_description', __('Description'), ['class' => 'form-label']) }}
                                                                        {{ Form::text('paytab_description', UtilityFacades::getsettings('paytab_description'), ['class' => 'form-control ', 'placeholder' => __('Enter description')]) }}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{ Form::label('paytab_currency', __('PayTab Currency'), ['class' => 'form-label']) }}
                                                                    <select name="paytab_currency" class="form-select"
                                                                        data-trigger id="paytab_currency">
                                                                        <option value="INR"> {{ __('INR') }}
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- PayUMoney -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-payumoney">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse-payumoney"
                                                        aria-expanded="true" aria-controls="collapse-payumoney">
                                                        <span class="flex-1 d-flex align-items-center">
                                                            <i class="ti ti-credit-card text-primary"></i>
                                                            {{ __('PayUMoney') }}
                                                        </span>
                                                        @if (UtilityFacades::getsettings('payumoneysetting') == 'on')
                                                            <a
                                                                class="text-white btn btn-sm btn-primary float-end me-3">{{ __('Active') }}</a>
                                                        @endif
                                                    </button>
                                                </h2>
                                                <div id="collapse-payumoney" class="accordion-collapse collapse"
                                                    aria-labelledby="heading-2-payumoney"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="py-1 col-12 text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    {!! Form::checkbox(
                                                                        'paymentsetting[]',
                                                                        'payumoney',
                                                                        UtilityFacades::getsettings('payumoneysetting') == 'on' ? true : false,
                                                                        [
                                                                            'class' => 'form-check-input mx-2',
                                                                            'id' => 'payment_payumoney',
                                                                        ],
                                                                    ) !!}
                                                                    {{ Form::label('payment_payumoney', __('Enable'), ['class' => 'form-check-label']) }}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                {{ Form::label('payumoney_mode', __('PayUMoney Mode'), ['class' => 'paypal-label form-label']) }}
                                                                <br>
                                                                <div class="d-flex">
                                                                    <div class="ms-2">
                                                                        <div class="p-3 border card">
                                                                            <div class="form-check">
                                                                                <label class="form-check-labe text-dark">
                                                                                    {!! Form::radio(
                                                                                        'payumoney_mode',
                                                                                        'sandbox',
                                                                                        Utility::getsettings('payumoney_mode') == 'sandbox' ? true : false,
                                                                                        [
                                                                                            'class' => 'form-check-input',
                                                                                            'id' => 'payumoney_sandbox',
                                                                                        ],
                                                                                    ) !!}{{ __('Sandbox') }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="ms-2">
                                                                        <div class="p-3 border card">
                                                                            <div class="form-check">
                                                                                <label class="form-check-labe text-dark">
                                                                                    {!! Form::radio(
                                                                                        'payumoney_mode',
                                                                                        'production',
                                                                                        Utility::getsettings('payumoney_mode') == 'production' ? true : false,
                                                                                        [
                                                                                            'class' => 'form-check-input',
                                                                                            'id' => 'payumoney_production',
                                                                                        ],
                                                                                    ) !!}{{ __('Production') }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{ Form::label('payumoney_merchant_key', __('PayUMoney Merchant Key'), ['class' => 'form-label']) }}
                                                                    {!! Form::text('payumoney_merchant_key', UtilityFacades::getsettings('payumoney_merchant_key'), [
                                                                        'class' => 'form-control',
                                                                        'placeholder' => __('Enter payumoney merchant key'),
                                                                    ]) !!}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{ Form::label('payumoney_salt_key', __('PayUMoney Salt Key'), ['class' => 'form-label']) }}
                                                                    {!! Form::text('payumoney_salt_key', UtilityFacades::getsettings('payumoney_salt_key'), [
                                                                        'class' => 'form-control',
                                                                        'placeholder' => __('Enter payumoney salt key'),
                                                                    ]) !!}
                                                                </div>
                                                            </div>
                                                            @if (Auth::user()->type == 'Super Admin')
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        {{ Form::label('payumoney_description', __('Description'), ['class' => 'form-label']) }}
                                                                        {{ Form::text('payumoney_description', UtilityFacades::getsettings('payumoney_description'), ['class' => 'form-control ', 'placeholder' => __('Enter description')]) }}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- @if (Auth::user()->type != 'Super Admin') --}}
                                                {{-- offline payment --}}
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="mercado">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#collapsofflinepayment" aria-expanded="true"
                                                            aria-controls="collapsofflinepayment">
                                                            <span class="flex-1 d-flex align-items-center">
                                                                <i class="ti ti-credit-card text-primary"></i>
                                                                {{ __('Offline Payment') }}
                                                            </span>
                                                            @if (Utility::getsettings('offlinepaymentsetting') == 'on')
                                                                <a
                                                                    class="text-white btn btn-sm btn-primary float-end me-3">{{ __('Active') }}</a>
                                                            @endif
                                                        </button>
                                                    </h2>
                                                    <div id="collapsofflinepayment" class="accordion-collapse collapse"
                                                        aria-labelledby="offline_payment"
                                                        data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="col-12 d-flex justify-content-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    {!! Form::checkbox(
                                                                        'paymentsetting[]',
                                                                        'offlinepayment',
                                                                        Utility::getsettings('offlinepaymentsetting') == 'on' ? true : false,
                                                                        [
                                                                            'class' => 'form-check-input',
                                                                            'id' => 'is_offline_payment',
                                                                        ],
                                                                    ) !!}
                                                                    {{ Form::label('is_offline_payment', __('Enable'), ['class' => 'custom-control-label form-control-label']) }}
                                                                </div>
                                                            </div>

                                                            <div class="mt-2 row">
                                                                <div class="form-group">
                                                                    {{ Form::label('offline_payment_details', __('Payment Details'), ['class' => 'form-label']) }}
                                                                    {!! Form::textarea('offline_payment_details', UtilityFacades::getsettings('offline_payment_details'), [
                                                                        'class' => 'form-control',
                                                                        'placeholder' => __('Enter Payment Details'),
                                                                        'id' => 'offline_payment_details',
                                                                        'rows' => '3',
                                                                    ]) !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            {{-- @endif --}}
                                            @if (Auth::user()->type == 'Super Admin')
                                                <!-- Bkash -->
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading-2-bkash">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse-bksh"
                                                            aria-expanded="true" aria-controls="collapse-bksh">
                                                            <span class="flex-1 d-flex align-items-center">
                                                                <i class="ti ti-credit-card text-primary"></i>
                                                                {{ __('BKash') }}
                                                            </span>
                                                            @if (UtilityFacades::getsettings('bkashsetting') == 'on')
                                                                <a
                                                                    class="text-white btn btn-sm btn-primary float-end me-3">{{ __('Active') }}</a>
                                                            @endif
                                                        </button>
                                                    </h2>
                                                    <div id="collapse-bksh" class="accordion-collapse collapse"
                                                        aria-labelledby="heading-2-bkash"
                                                        data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="py-1 col-6">
                                                                </div>
                                                                <div class="py-1 col-6 text-end">
                                                                    <div class="form-check form-switch d-inline-block">
                                                                        {!! Form::checkbox(
                                                                            'paymentsetting[]',
                                                                            'bkash',
                                                                            UtilityFacades::getsettings('bkashsetting') == 'on' ? true : false,
                                                                            [
                                                                                'class' => 'form-check-input mx-2',
                                                                                'id' => 'payment_bkash',
                                                                            ],
                                                                        ) !!}
                                                                        {{ Form::label('payment_bkash', __('Enable'), ['class' => 'form-check-label']) }}
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        {{ Form::label('bkash_json_file', __('BKash Json File'), ['class' => 'form-label']) }}
                                                                        {{ Form::file('bkash_json_file', ['class' => 'form-control']) }}
                                                                    </div>
                                                                </div>
                                                                @if (Auth::user()->type == 'Super Admin')
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('bkash_description', __('Description'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('bkash_description', UtilityFacades::getsettings('bkash_description'), ['class' => 'form-control ', 'placeholder' => __('Enter description')]) }}
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        {{ Form::label('bkash_currency', __('BKash Currency'), ['class' => 'form-label']) }}
                                                                        <select name="bkash_currency"
                                                                            class="form-select" data-trigger
                                                                            id="bkash_currency">
                                                                            <option value="BDT"> {{ __('BDT') }}
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <!-- OFFLINE -->
                                            @if (Auth::user()->type == 'Super Admin')
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading-2-6">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse5"
                                                            aria-expanded="true" aria-controls="collapse5">
                                                            <span class="flex-1 d-flex align-items-center">
                                                                <i class="ti ti-credit-card text-primary"></i>
                                                                {{ __('Offline') }}
                                                            </span>
                                                            @if (UtilityFacades::getsettings('offlinesetting') == 'on')
                                                                <a
                                                                    class="text-white btn btn-sm btn-primary float-end me-3">{{ __('Active') }}</a>
                                                            @endif
                                                        </button>
                                                    </h2>
                                                    <div id="collapse5" class="accordion-collapse collapse"
                                                        aria-labelledby="heading-2-6"
                                                        data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="py-1 col-6">
                                                                </div>
                                                                <div class="py-1 col-6 text-end">
                                                                    <div class="form-check form-switch d-inline-block">
                                                                        {!! Form::checkbox(
                                                                            'paymentsetting[]',
                                                                            'offline',
                                                                            UtilityFacades::getsettings('offlinesetting') == 'on' ? true : false,
                                                                            [
                                                                                'class' => 'form-check-input mx-2',
                                                                                'id' => 'payment_offline',
                                                                            ],
                                                                        ) !!}
                                                                        {{ Form::label('payment_offline', __('Enable'), ['class' => 'form-check-label']) }}
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    {{ Form::label('payment_details', __('Payment Details'), ['class' => 'form-label']) }}
                                                                    {!! Form::textarea('payment_details', UtilityFacades::getsettings('payment_details'), [
                                                                        'class' => 'form-control',
                                                                        'rows' => '3',
                                                                        'placeholder' => __('Enter payment details'),
                                                                    ]) !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="text-end">
                                {{ Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) }}
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                    <div id="sms_setting" class="card">
                        {!! Form::open([
                            'route' => 'settings.smsSetting.update',
                            'method' => 'POST',
                            'enctype' => 'multipart/form-data',
                            'data-validate',
                            'novalidate',
                        ]) !!}
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-lg-8 d-flex justify-content-start">
                                    <h5> {{ __('Sms Setting') }}</h5>
                                </div>
                                <div class="col-lg-4 d-flex justify-content-end">
                                    <div class="form-switch custom-switch-v1 d-inline-block">
                                        {!! Form::checkbox(
                                            'multisms_setting',
                                            null,
                                            UtilityFacades::getsettings('multisms_setting') == 'on' ? true : false,
                                            [
                                                'class' => 'custom-control custom-switch form-check-input input-primary',
                                                'id' => 'multi_sms',
                                                'data-toggle' => 'switchbutton',
                                            ],
                                        ) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div
                                    class="col-sm-12 multi_sms {{ UtilityFacades::getsettings('multisms_setting') == 'on' ? '' : 'd-none' }}">
                                    <div class="form-group">
                                        {!! Form::radio('smssetting', 'twilio', Utility::getsettings('smssetting') == 'twilio' ? true : false, [
                                            'class' => 'btn-check',
                                            'id' => 'smssetting_twilio',
                                        ]) !!}
                                        {{ Form::label('smssetting_twilio', __('Twilio'), ['class' => 'btn btn-outline-primary']) }}


                                        {!! Form::radio('smssetting', 'nexmo', Utility::getsettings('smssetting') == 'nexmo' ? true : false, [
                                            'class' => 'btn-check',
                                            'id' => 'smssetting_nexmo',
                                        ]) !!}
                                        {{ Form::label('smssetting_nexmo', __('Nexmo'), ['class' => 'btn btn-outline-primary']) }}

                                        {!! Form::radio('smssetting', 'fast2sms', Utility::getsettings('smssetting') == 'fast2sms' ? true : false, [
                                            'class' => 'btn-check',
                                            'id' => 'smssetting_fast2sms',
                                        ]) !!}
                                        {{ Form::label('smssetting_fast2sms', __('FAST2SMS'), ['class' => 'btn btn-outline-primary']) }}
                                    </div>
                                    <div id="twilio"
                                        class="desc {{ Utility::getsettings('smssetting') == 'twilio' ? 'block' : 'd-none' }}">
                                        <div class="row">
                                            <div class="form-group">
                                                {{ Form::label('twilio_sid', __('Twilio SID'), ['class' => 'form-label']) }}
                                                {!! Form::text('twilio_sid', Utility::getsettings('twilio_sid'), [
                                                    'placeholder' => __('Enter twilio sid'),
                                                    'class' => 'form-control',
                                                ]) !!}
                                            </div>
                                            <div class="form-group">
                                                {{ Form::label('twilio_auth_token', __('Twilio Auth Token'), ['class' => 'form-label']) }}
                                                {!! Form::text('twilio_auth_token', Utility::getsettings('twilio_auth_token'), [
                                                    'placeholder' => __('Enter twilio auth token'),
                                                    'class' => 'form-control',
                                                ]) !!}
                                            </div>
                                            <div class="form-group">
                                                {{ Form::label('twilio_verify_sid', __('Twilio Verify SID'), ['class' => 'form-label']) }}
                                                {!! Form::text('twilio_verify_sid', Utility::getsettings('twilio_verify_sid'), [
                                                    'placeholder' => __('Enter twilio verify sid'),
                                                    'class' => 'form-control',
                                                ]) !!}
                                            </div>
                                            <div class="form-group">
                                                {{ Form::label('twilio_number', __('Twilio Number'), ['class' => 'form-label']) }}
                                                {!! Form::text('twilio_number', Utility::getsettings('twilio_number'), [
                                                    'placeholder' => __('Enter twilio number'),
                                                    'class' => 'form-control',
                                                ]) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div id="nexmo"
                                        class="desc {{ Utility::getsettings('smssetting') == 'nexmo' ? 'block' : 'd-none' }}">
                                        <div class="row">
                                            <div class="form-group">
                                                {{ Form::label('nexmo_key', __('Nexmo Key'), ['class' => 'form-label']) }}
                                                {!! Form::text('nexmo_key', Utility::getsettings('nexmo_key'), [
                                                    'placeholder' => __('Enter nexmo key'),
                                                    'class' => 'form-control',
                                                ]) !!}
                                            </div>
                                            <div class="form-group">
                                                {{ Form::label('nexmo_secret', __('Nexmo Secret'), ['class' => 'form-label']) }}
                                                {!! Form::text('nexmo_secret', Utility::getsettings('nexmo_secret'), [
                                                    'placeholder' => __('Enter nexmo secret'),
                                                    'class' => 'form-control',
                                                ]) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div id="fast2sms"
                                        class="desc {{ Utility::getsettings('smssetting') == 'fast2sms' ? 'block' : 'd-none' }}">
                                        <div class="row">
                                            <div class="form-group">
                                                {{ Form::label('fast2sms_api_key', __('FAST2SMS Api Key'), ['class' => 'form-label']) }}
                                                {!! Form::text('fast2sms_api_key', Utility::getsettings('fast2sms_api_key'), [
                                                    'placeholder' => __('Enter fast2sms api key'),
                                                    'class' => 'form-control',
                                                ]) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="text-end">
                                {{ Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) }}
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                    @if (\Auth::user()->type == 'Super Admin')

                        <div id="cookie_setting" class="card">
                            <div class="card-header">
                                {!! Form::open([
                                    'route' => 'settings.cookieSetting.update',
                                    'method' => 'Post',
                                    'enctype' => 'multipart/form-data',
                                    'data-validate',
                                ]) !!}
                                <div class="row">
                                    <div class="col-lg-8 d-flex align-items-center">
                                        <h5> {{ __('Cookie Setting') }}</h5>
                                    </div>
                                    <div class="col-lg-4 d-flex justify-content-end">
                                        <div class="form-switch custom-switch-v1 d-inline-block">
                                            {!! Form::checkbox(
                                                'cookie_setting_enable',
                                                null,
                                                Utility::getsettings('cookie_setting_enable') == 'on' ? true : false,
                                                [
                                                    'class' => 'custom-control custom-switch form-check-input input-primary',
                                                    'id' => 'cookieSettingEnableBtn',
                                                    'data-onstyle' => 'primary',
                                                    'data-toggle' => 'switchbutton',
                                                ],
                                            ) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="my-2 form-check form-switch" id="cookie_log">
                                            <input type="checkbox" name="cookie_logging"
                                                class="form-check-input input-primary cookie_setting"
                                                id="cookie_logging"
                                                {{ Utility::getsettings('cookie_logging') == 'on' ? ' checked ' : '' }}>
                                            <label class="form-check-label"
                                                for="cookie_logging">{{ __('Enable logging') }}</label>
                                        </div>
                                        <div class="form-group">
                                            {{ Form::label('cookie_title', __('Cookie Title'), ['class' => 'form-label']) }}
                                            {!! Form::text('cookie_title', Utility::getsettings('cookie_title'), [
                                                'class' => 'form-control',
                                                'placeholder' => __('Enter cookie title'),
                                            ]) !!}
                                        </div>
                                        <div class="form-group">
                                            {{ Form::label('cookie_description', __('Cookie Description'), ['class' => 'form-label']) }}
                                            {!! Form::text('cookie_description', Utility::getsettings('cookie_description'), [
                                                'class' => 'form-control',
                                                'placeholder' => __('Enter cookie description'),
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="my-2 form-check form-switch">
                                            <input type="checkbox" name="necessary_cookies"
                                                class="form-check-input input-primary cookie_setting"
                                                id="necessary_cookies"
                                                {{ Utility::getsettings('necessary_cookies') == 'on' ? ' checked ' : '' }}>
                                            <label class="form-check-label"
                                                for="necessary_cookies">{{ __('Strictly necessary cookies') }}</label>
                                        </div>
                                        <div class="form-group">
                                            {{ Form::label('strictly_cookie_title', __('Strictly Cookie Title'), ['class' => 'form-label']) }}
                                            {!! Form::text('strictly_cookie_title', Utility::getsettings('strictly_cookie_title'), [
                                                'class' => 'form-control',
                                                'placeholder' => __('Enter strictly cookie title'),
                                            ]) !!}
                                        </div>
                                        <div class="form-group">
                                            {{ Form::label('strictly_cookie_description', __('Strictly Cookie Description'), ['class' => 'form-label']) }}
                                            {!! Form::text('strictly_cookie_description', Utility::getsettings('strictly_cookie_description'), [
                                                'class' => 'form-control',
                                                'placeholder' => __('Enter strictly cookie description'),
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-8">
                                        <h5> {{ __('More Information') }}</h5>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {{ Form::label('contact_us_description', __('Contact Us Description'), ['class' => 'form-label']) }}
                                            {!! Form::text('contact_us_description', Utility::getsettings('contact_us_description'), [
                                                'class' => 'form-control',
                                                'placeholder' => __('Enter contact us description'),
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {{ Form::label('contact_us_url', __('Contact Us Url'), ['class' => 'form-label']) }}
                                            {!! Form::text('contact_us_url', Utility::getsettings('contact_us_url'), [
                                                'class' => 'form-control',
                                                'placeholder' => __('Enter contact us url'),
                                            ]) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-6">
                                        @if (Utility::getsettings('cookie_logging') == 'on')
                                            @if (Storage::url('cookie-csv/cookie-data.csv'))
                                                <label for="file"
                                                    class="form-label">{{ __('Download cookie accepted data') }}</label>
                                                <a href="{{ Storage::url('cookie-csv/cookie-data.csv') }}"
                                                    class="mr-3 btn btn-primary">
                                                    <i class="ti ti-download"></i>
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="col-6 text-end">
                                        <input class="btn btn-print-invoice btn-primary cookie_btn" type="submit"
                                            value="{{ __('Save') }}">
                                    </div>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                        <div id="cache_setting" class="card">
                            <div class="card-header">
                                <h5> {{ __('Cache Setting') }}</h5>
                            </div>
                            {!! Form::open([
                                'route' => 'config.cache',
                                'method' => 'Post',
                                'enctype' => 'multipart/form-data',
                                'data-validate',
                            ]) !!}
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 form-group">
                                        {{ Form::label('current_cache_size', __('Current cache size'), ['class' => 'form-label']) }}
                                        <div class="input-group">
                                            <input type="text" class="form-control"
                                                value="{{ Utility::cacheSize() }}" id="current_cache_size" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">{{ __('MB') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="text-end">
                                    {{ Form::button(__('Clear Cache'), ['type' => 'submit', 'class' => 'btn btn-primary']) }}
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                        <div id="seo-setting" class="pt-0 card">
                            {!! Form::open([
                                'route' => ['settings.seoSetting.update'],
                                'enctype' => 'multipart/form-data',
                            ]) !!}
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-lg-8">
                                        <h5>{{ __('SEO Setting') }}</h5>
                                    </div>
                                    <div class="col-lg-4 d-flex justify-content-end">
                                        <div class="form-switch custom-switch-v1 d-inline-block">
                                            {!! Form::checkbox('seo_setting', null, UtilityFacades::getsettings('seo_setting') == 'on' ? true : false, [
                                                'class' => 'custom-control custom-switch form-check-input input-primary',
                                                'id' => 'seoSettingEnableBtn',
                                                'data-onstyle' => 'primary',
                                                'data-toggle' => 'switchbutton',
                                            ]) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {{ Form::label('meta_title', __('Meta Title'), ['class' => 'form-label']) }}
                                            {{ Form::text('meta_title', Utility::getsettings('meta_title') ? Utility::getsettings('meta_title') : '', [
                                                'class' => 'form-control',
                                                'placeholder' => __('Enter meta title'),
                                            ]) }}
                                        </div>
                                        <div class="form-group">
                                            {{ Form::label('meta_keywords', __('Meta Keywords'), ['class' => 'form-label']) }}
                                            {{ Form::text(
                                                'meta_keywords',
                                                Utility::getsettings('meta_keywords') ? Utility::getsettings('meta_keywords') : '',
                                                [
                                                    'id' => 'choices-text-remove-button',
                                                    'class' => 'form-control ',
                                                    'data-placeholder' => __('Enter meta keywords'),
                                                ],
                                            ) }}
                                        </div>
                                        <div class="form-group">
                                            {{ Form::label('meta_description', __('Meta Description'), ['class' => 'form-label']) }}
                                            {{ Form::textarea(
                                                'meta_description',
                                                Utility::getsettings('meta_description') ? Utility::getsettings('meta_description') : '',
                                                ['class' => 'form-control ', 'rows' => 3, 'placeholder' => __('Enter meta description')],
                                            ) }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('Meta Image', __('Meta Image'), ['class' => 'form-label ms-4']) }}
                                            <div class="pt-0 card-body">
                                                <div class="setting_card">
                                                    <div class="logo-content ">
                                                        <a href="{{ Utility::getsettings('meta_image') ? Storage::url(Utility::getsettings('meta_image')) : Storage::url('seo/meta-image.jpg') }}"
                                                            target="_blank">
                                                            <img id="meta"
                                                                src="{{ Utility::getsettings('meta_image') ? Storage::url(Utility::getsettings('meta_image')) : Storage::url('seo/meta-image.jpg') }}"
                                                                width="250px">
                                                        </a>
                                                    </div>
                                                    <div class="mt-4 choose-files">
                                                        <label for="meta_image">
                                                            <div class=" bg-primary logo input-img-div"> <i
                                                                    class="px-1 ti ti-upload"></i>{{ __('Choose file here') }}
                                                                <input type="file"
                                                                    class="form-control file image-input"
                                                                    name="meta_image" id="meta_image"
                                                                    data-filename="meta_image"
                                                                    onchange="document.getElementById('meta').src = window.URL.createObjectURL(this.files[0])">
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="text-end">
                                    {!! Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) !!}
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                        <div id="pwa-setting" class="card">
                            <div class="col-md-12">
                                {{ Form::open(['route' => 'settings.pwa-setting.update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                <div class="card-header">
                                    <div class="row d-flex align-items-center">
                                        <div class="col-6">
                                            <h5 class="mb-2">{{ __('PWA Setting') }}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body google_map">
                                    <div class="row">
                                        <div class="col-lg-4 col-sm-6 col-md-6 form-group">
                                            <div class="card">
                                                <div class="card-header d-flex">
                                                    <h5 class="me-1">{{ __('PWA Icon') }}</h5>
                                                    <span>(128x128)</span>
                                                </div>
                                                <div class="pt-0 card-body">
                                                    <div class="inner-content">
                                                        <div class="py-2 mt-4 text-center logo-content dark-logo-content">
                                                            <a href="{{ Utility::getpath('pwa_icon_128') ? Storage::url(Utility::getsettings('pwa_icon_128')) : '' }}"
                                                                target="_blank">
                                                                <img src="{{ Utility::getpath('pwa_icon_128') ? Storage::url(Utility::getsettings('pwa_icon_128')) : '' }}"
                                                                    id="pwa_128">
                                                            </a>
                                                        </div>
                                                        <div class="mt-3 text-center choose-files">
                                                            <label for="pwa_icon_128">
                                                                <div class="bg-primary company_logo_update"> <i
                                                                        class="px-1 ti ti-upload"></i>{{ __('Choose file here') }}
                                                                </div>
                                                                {{ Form::file('pwa_icon_128', ['class' => 'form-control file', 'id' => 'pwa_icon_128', 'onchange' => "document.getElementById('pwa_128').src = window.URL.createObjectURL(this.files[0])", 'data-filename' => 'pwa_icon_128']) }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-6 col-md-6">
                                            <div class="card">
                                                <div class="card-header d-flex">
                                                    <h5 class="me-1">{{ __('PWA Icon') }}</h5>
                                                    <span>(144x144)</span>
                                                </div>
                                                <div class="pt-0 card-body">
                                                    <div class="inner-content">
                                                        <div class="py-2 mt-4 text-center logo-content dark-logo-content">
                                                            <a href="{{ Utility::getpath('pwa_icon_144') ? Storage::url(Utility::getsettings('pwa_icon_144')) : '' }}"
                                                                target="_blank">
                                                                <img src="{{ Utility::getpath('pwa_icon_144') ? Storage::url(Utility::getsettings('pwa_icon_144')) : '' }}"
                                                                    id="pwa_144">
                                                            </a>
                                                        </div>
                                                        <div class="mt-3 text-center choose-files">
                                                            <label for="pwa_icon_144">
                                                                <div class="bg-primary company_logo_update"> <i
                                                                        class="px-1 ti ti-upload"></i>{{ __('Choose file here') }}
                                                                </div>
                                                                {{ Form::file('pwa_icon_144', ['class' => 'form-control file', 'id' => 'pwa_icon_144', 'onchange' => "document.getElementById('pwa_144').src = window.URL.createObjectURL(this.files[0])", 'data-filename' => 'pwa_icon_144']) }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-6 col-md-6">
                                            <div class="card">
                                                <div class="card-header d-flex">
                                                    <h5 class="me-1">{{ __('PWA Icon') }}</h5>
                                                    <span>(152x152)</span>
                                                </div>
                                                <div class="pt-0 card-body">
                                                    <div class="inner-content">
                                                        <div class="py-2 mt-4 text-center logo-content dark-logo-content">
                                                            <a href="{{ Utility::getpath('pwa_icon_152') ? Storage::url(Utility::getsettings('pwa_icon_152')) : '' }}"
                                                                target="_blank">
                                                                <img src="{{ Utility::getpath('pwa_icon_152') ? Storage::url(Utility::getsettings('pwa_icon_152')) : '' }}"
                                                                    id="pwa_152">
                                                            </a>
                                                        </div>
                                                        <div class="mt-3 text-center choose-files">
                                                            <label for="pwa_icon_152">
                                                                <div class="bg-primary company_logo_update"> <i
                                                                        class="px-1 ti ti-upload"></i>{{ __('Choose file here') }}
                                                                </div>
                                                                {{ Form::file('pwa_icon_152', ['class' => 'form-control file', 'id' => 'pwa_icon_152', 'onchange' => "document.getElementById('pwa_152').src = window.URL.createObjectURL(this.files[0])", 'data-filename' => 'pwa_icon_152']) }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-6 col-md-6">
                                            <div class="card">
                                                <div class="card-header d-flex">
                                                    <h5 class="me-1">{{ __('PWA Icon') }}</h5>
                                                    <span>(192x192)</span>
                                                </div>
                                                <div class="pt-0 card-body">
                                                    <div class="inner-content">
                                                        <div class="py-2 mt-4 text-center logo-content dark-logo-content">
                                                            <a href="{{ Utility::getpath('pwa_icon_192') ? Storage::url(Utility::getsettings('pwa_icon_192')) : '' }}"
                                                                target="_blank">
                                                                <img src="{{ Utility::getpath('pwa_icon_192') ? Storage::url(Utility::getsettings('pwa_icon_192')) : '' }}"
                                                                    id="pwa_192">
                                                            </a>
                                                        </div>
                                                        <div class="mt-3 text-center choose-files">
                                                            <label for="pwa_icon_192">
                                                                <div class="bg-primary company_logo_update"> <i
                                                                        class="px-1 ti ti-upload"></i>{{ __('Choose file here') }}
                                                                </div>
                                                                {{ Form::file('pwa_icon_192', ['class' => 'form-control file', 'id' => 'pwa_icon_192', 'onchange' => "document.getElementById('pwa_192').src = window.URL.createObjectURL(this.files[0])", 'data-filename' => 'pwa_icon_192']) }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-6 col-md-6">
                                            <div class="card">
                                                <div class="card-header d-flex">
                                                    <h5 class="me-1">{{ __('PWA Icon') }}</h5>
                                                    <span>(256x256)</span>
                                                </div>
                                                <div class="pt-0 card-body">
                                                    <div class="inner-content">
                                                        <div class="py-2 mt-4 text-center logo-content dark-logo-content">
                                                            <a href="{{ Utility::getpath('pwa_icon_256') ? Storage::url(Utility::getsettings('pwa_icon_256')) : '' }}"
                                                                target="_blank">
                                                                <img src="{{ Utility::getpath('pwa_icon_256') ? Storage::url(Utility::getsettings('pwa_icon_256')) : '' }}"
                                                                    id="pwa_256">
                                                            </a>
                                                        </div>
                                                        <div class="mt-3 text-center choose-files">
                                                            <label for="pwa_icon_256">
                                                                <div class="bg-primary company_logo_update"> <i
                                                                        class="px-1 ti ti-upload"></i>{{ __('Choose file here') }}
                                                                </div>
                                                                {{ Form::file('pwa_icon_256', ['class' => 'form-control file', 'id' => 'pwa_icon_256', 'onchange' => "document.getElementById('pwa_256').src = window.URL.createObjectURL(this.files[0])", 'data-filename' => 'pwa_icon_256']) }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-6 col-md-6">
                                            <div class="card">
                                                <div class="card-header d-flex">
                                                    <h5 class="me-1">{{ __('PWA Icon') }}</h5>
                                                    <span>(512x512)</span>
                                                </div>
                                                <div class="pt-0 card-body">
                                                    <div class="inner-content">
                                                        <div class="py-2 mt-4 text-center logo-content dark-logo-content">
                                                            <a href="{{ Utility::getpath('pwa_icon_512') ? Storage::url(Utility::getsettings('pwa_icon_512')) : '' }}"
                                                                target="_blank">
                                                                <img src="{{ Utility::getpath('pwa_icon_512') ? Storage::url(Utility::getsettings('pwa_icon_512')) : '' }}"
                                                                    id="pwa_512">
                                                            </a>
                                                        </div>
                                                        <div class="mt-3 text-center choose-files">
                                                            <label for="pwa_icon_512">
                                                                <div class="bg-primary company_logo_update"> <i
                                                                        class="px-1 ti ti-upload"></i>{{ __('Choose file here') }}
                                                                </div>
                                                                {{ Form::file('pwa_icon_512', ['class' => 'form-control file', 'id' => 'pwa_icon_512', 'onchange' => "document.getElementById('pwa_512').src = window.URL.createObjectURL(this.files[0])", 'data-filename' => 'pwa_icon_512']) }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-end">
                                    <button class="btn-submit btn btn-primary" type="submit">
                                        {{ __('Save') }}
                                    </button>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@push('style')
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/bootstrap-switch-button.min.css') }}">
@endpush
@push('script')
    <script src="{{ asset('assets/js/plugins/bootstrap-switch-button.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>
    <script>
        function check_theme(color_val) {
            $('.theme-color').prop('checked', false);
            $('input[value="' + color_val + '"]').prop('checked', true);
        }
        $(document).on('click', "input[name$='storage_type']", function() {
            var test = $(this).val();
            if (test == 's3') {
                $("#s3").fadeIn(500);
                $("#s3").removeClass('d-none');
            } else {
                $("#s3").fadeOut(500);
            }
        });
        $(document).on('click', "input[name$='storage_type']", function() {
            var test = $(this).val();
            if (test == 'wasabi') {
                $("#wasabi").fadeIn(500);
                $("#wasabi").removeClass('d-none');
            } else {
                $("#wasabi").fadeOut(500);
            }
        });
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        });
        if ($('#choices-text-remove-button').length) {
            var textRemove = new Choices(
                document.getElementById('choices-text-remove-button'), {
                    delimiter: ',',
                    editItems: true,
                    removeItemButton: true,
                });
        }
        feather.replace();
        $(document).ready(function() {
            $(".socialsetting").trigger("select");
        });
        $(document).ready(function() {
            if ($("input[name$='captcha']").is(':checked')) {
                $("#recaptcha").fadeIn(500);
                $("#recaptcha").removeClass('d-none');
            } else {
                $("#recaptcha").fadeOut(500);
                $("#recaptcha").addClass('d-none');
            }
            $(".paymenttsetting").trigger("select");
        });
        $(document).on('change', ".socialsetting", function() {
            var test = $(this).val();
            if ($(this).is(':checked')) {
                if (test == 'google') {
                    $("#google").fadeIn(500);
                    $("#google").removeClass('d-none');
                } else if (test == 'facebook') {
                    $("#facebook").fadeIn(500);
                    $("#facebook").removeClass('d-none');
                } else if (test == 'github') {
                    $("#github").fadeIn(500);
                    $("#github").removeClass('d-none');
                } else if (test == 'linkedin') {
                    $("#linkedin").fadeIn(500);
                    $("#linkedin").removeClass('d-none');
                }
            } else {
                if (test == 'google') {
                    $("#google").fadeOut(500);
                    $("#google").addClass('d-none');
                } else if (test == 'facebook') {
                    $("#facebook").fadeOut(500);
                    $("#facebook").addClass('d-none');
                } else if (test == 'github') {
                    $("#github").fadeOut(500);
                    $("#github").addClass('d-none');
                } else if (test == 'linkedin') {
                    $("#linkedin").fadeOut(500);
                    $("#linkedin").addClass('d-none');
                }
            }
        });
        $(document).on('change', ".paymenttsetting", function() {
            var test = $(this).val();
            if ($(this).is(':checked')) {
                if (test == 'razorpay') {
                    $("#razorpay").fadeIn(500);
                    $("#razorpay").removeClass('d-none');
                } else if (test == 'stripe') {
                    $("#stripe").fadeIn(500);
                    $("#stripe").removeClass('d-none');
                } else if (test == 'paypal') {
                    $("#paypal").fadeIn(500);
                    $("#paypal").removeClass('d-none');
                } else if (test == 'offline') {
                    $("#offline").fadeIn(500);
                    $("#offline").removeClass('d-none');
                } else if (test == 'flutterwave') {
                    $("#flutterwave").fadeIn(500);
                    $("#flutterwave").removeClass('d-none');
                } else if (test == 'paystack') {
                    $("#paystack").fadeIn(500);
                    $("#paystack").removeClass('d-none');
                } else if (test == 'coingate') {
                    $("#coingate").fadeIn(500);
                    $("#coingate").removeClass('d-none');
                } else if (test == 'mercado') {
                    $("#mercado").fadeIn(500);
                    $("#mercado").removeClass('d-none');
                }
            } else {
                if (test == 'razorpay') {
                    $("#razorpay").fadeOut(500);
                    $("#razorpay").addClass('d-none');
                } else if (test == 'stripe') {
                    $("#stripe").fadeOut(500);
                    $("#stripe").addClass('d-none');
                } else if (test == 'paypal') {
                    $("#paypal").fadeOut(500);
                    $("#paypal").addClass('d-none');
                } else if (test == 'offline') {
                    $("#offline").fadeOut(500);
                    $("#offline").addClass('d-none');
                } else if (test == 'flutterwave') {
                    $("#flutterwave").fadeOut(500);
                    $("#flutterwave").addClass('d-none');
                } else if (test == 'paystack') {
                    $("#paystack").fadeIn(500);
                    $("#paystack").removeClass('d-none');
                } else if (test == 'coingate') {
                    $("#coingate").fadeIn(500);
                    $("#coingate").removeClass('d-none');
                } else if (test == 'mercado') {
                    $("#mercado").fadeIn(500);
                    $("#mercado").removeClass('d-none');
                }
            }
        });
        $(document).on('click', '.send_mail', function() {
            var action = $(this).data('url');
            var modal = $('#common_modal');
            $.get(action, function(response) {
                modal.find('.modal-title').html('{{ __('Test Mail') }}');
                modal.find('.body').html(response);
                modal.modal('show');
            })
        });
        document.addEventListener('DOMContentLoaded', function() {
            var genericExamples = document.querySelectorAll('[data-trigger]');
            for (i = 0; i < genericExamples.length; ++i) {
                var element = genericExamples[i];
                new Choices(element, {
                    placeholderValue: 'This is a placeholder set in the config',
                    searchPlaceholderValue: 'Select Option',
                });
            }
        });
        $(document).on('click', "input[name$='captchasetting']", function() {
            if (this.checked) {
                $('#captcha_setting').fadeIn(500);
                $("#captcha_setting").removeClass('d-none');
                $("#captcha_setting").addClass('d-block');
            } else {
                $('#captcha_setting').fadeOut(500);
                $("#captcha_setting").removeClass('d-block');
                $("#captcha_setting").addClass('d-none');
            }
        });
        $(document).on('click', "input[name$='captcha']", function() {
            var test = $(this).val();
            if (test == 'hcaptcha') {
                $("#hcaptcha").fadeIn(500);
                $("#hcaptcha").removeClass('d-none');
                $("#recaptcha").addClass('d-none');
            } else {
                $("#recaptcha").fadeIn(500);
                $("#recaptcha").removeClass('d-none');
                $("#hcaptcha").addClass('d-none');
            }
        });
        $(document).on('change', "#cookieSettingEnableBtn", function() {
            if ($(this).is(':checked')) {
                $(".cookieSettingEnableBtn").fadeIn(500);
                $('.cookieSettingEnableBtn').removeClass('d-none');
            } else {
                $(".cookieSettingEnableBtn").fadeOut(500);
                $(".cookieSettingEnableBtn").addClass('d-none');
            }
        });
        $(document).on('change', "#seoSettingEnableBtn", function() {
            var seo = $(this).is(':checked');
            if (seo) {
                $('.seoDiv').removeClass('d-none');
            } else {
                $('.seoDiv').addClass('d-none');
            }
        });
        $(document).on('change', "#multi_sms", function() {
            if ($(this).is(':checked')) {
                $(".multi_sms").fadeIn(500);
                $('.multi_sms').removeClass('d-none');
                $('#twilio').removeClass('d-none');
                $('#smssetting_twilio').attr('checked', true);
            } else {
                $(".multi_sms").fadeOut(500);
                $(".multi_sms").addClass('d-none');
                $('#smssetting_twilio').attr('checked', false);
                $('#smssetting_nexmo').attr('checked', false);
                $('#smssetting_fast2sms').attr('checked', false);
            }
        });
        $(document).on('click', "input[name$='smssetting']", function() {
            var test = $(this).val();
            $("#twilio").fadeOut(500);
            if (test == 'twilio') {
                $("#twilio").fadeIn(500);
                $("#twilio").removeClass('d-none');
                $("#nexmo").addClass('d-none');
                $("#fast2sms").addClass('d-none');
                $("#nexmo").fadeOut(500);
                $("#fast2sms").fadeOut(500);
            } else if (test == 'nexmo') {
                $("#nexmo").fadeIn(500);
                $("#twilio").addClass('d-none');
                $("#nexmo").removeClass('d-none');
                $("#fast2sms").addClass('d-none');
                $("#twilio").fadeOut(500);
                $("#fast2sms").fadeOut(500);
            } else if (test == 'fast2sms') {
                $("#fast2sms").fadeIn(500);
                $("#twilio").addClass('d-none');
                $("#nexmo").addClass('d-none');
                $("#fast2sms").removeClass('d-none');
                $("#nexmo").fadeOut(500);
                $("#twilio").fadeOut(500);
            }
        });
        $(document).on('change', "#google_calender", function() {
            if ($(this).is(':checked')) {
                $(".google_calender").fadeIn(500);
                $('.google_calender').removeClass('d-none');
            } else {
                $(".google_calender").fadeOut(500);
                $(".google_calender").addClass('d-none');
            }
        });

        $(document).on('change', "#google_map", function() {
            if ($(this).is(':checked')) {
                $(".google_map").fadeIn(500);
                $('.google_map').removeClass('d-none');
            } else {
                $(".google_map").fadeOut(500);
                $(".google_map").addClass('d-none');
            }
        });

        $(document).on('change', "#captchaEnableButton", function() {
            if (this.checked) {
                $('.captchaSetting').fadeIn(500);
                $(".captchaSetting").removeClass('d-none');
            } else {
                $('.captchaSetting').fadeOut(500);
                $(".captchaSetting").addClass('d-none');
            }
        });
        var custthemebg = document.querySelector("#cust-theme-bg");
        custthemebg.addEventListener("click", function() {
            if (custthemebg.checked) {
                document.querySelector(".dash-sidebar").classList.add("transprent-bg");
                document
                    .querySelector(".dash-header:not(.dash-mob-header)")
                    .classList.add("transprent-bg");
            } else {
                document.querySelector(".dash-sidebar").classList.remove("transprent-bg");
                document
                    .querySelector(".dash-header:not(.dash-mob-header)")
                    .classList.remove("transprent-bg");
            }
        });
        var custdarklayout = document.querySelector("#cust-darklayout");
        custdarklayout.addEventListener("click", function() {
            if (custdarklayout.checked) {
                document.querySelector(".m-header > .b-brand > img").setAttribute("src",
                    "{{ Storage::url(setting('app_logo')) ? Storage::url('app-logo/app-logo.png') : Storage::url('not-exists-data-images/78x78.png') }}"
                );
                document.querySelector("#main-style-link").setAttribute("href",
                    "{{ asset('assets/css/style-dark.css') }}");
            } else {
                document.querySelector(".m-header > .b-brand > img").setAttribute("src",
                    "{{ Storage::url(setting('app_dark_logo')) ? Storage::url('app-logo/app-dark-logo.png') : Storage::url('not-exists-data-images/78x78.png') }}"
                );
                document.querySelector("#main-style-link").setAttribute("href",
                    "{{ asset('assets/css/style.css') }}");
            }
        });
        $(document).on("change", ".chnageEmailNotifyStatus", function(e) {
            var csrf = $("meta[name=csrf-token]").attr("content");
            var email = $(this).parent().find("input[name=email_notification]").is(":checked");
            var action = $(this).attr("data-url");
            $.ajax({
                type: "POST",
                url: action,
                data: {
                    _token: csrf,
                    type: 'email',
                    email_notification: email,
                },
                success: function(response) {
                    console.log(response);
                    if (response.warning) {
                        showToStr("Warning!", response.warning, "warning");
                    }
                    if (response.is_success) {
                        showToStr("Success!", response.message, "success");
                    }
                },
            });
        });
        $(document).on("change", ".chnagesmsNotifyStatus", function(e) {
            var csrf = $("meta[name=csrf-token]").attr("content");
            var sms = $(this).parent().find("input[name=sms_notification]").is(":checked");
            var action = $(this).attr("data-url");
            $.ajax({
                type: "POST",
                url: action,
                data: {
                    _token: csrf,
                    type: 'sms',
                    sms_notification: sms,
                },
                success: function(response) {
                    if (response.warning) {
                        showToStr("Warning!", response.warning, "warning");
                    }
                    if (response.is_success) {
                        showToStr("Success!", response.message, "success");
                    }
                },
            });
        });
        $(document).on("change", ".chnageNotifyStatus", function(e) {
            var csrf = $("meta[name=csrf-token]").attr("content");
            var notify = $(this).parent().find("input[name=notify]").is(":checked");
            var action = $(this).attr("data-url");
            $.ajax({
                type: "POST",
                url: action,
                data: {
                    _token: csrf,
                    type: 'notify',
                    notify: notify,
                },
                success: function(response) {
                    if (response.warning) {
                        showToStr("Warning!", response.warning, "warning");
                    }
                    if (response.is_success) {
                        showToStr("Success!", response.message, "success");
                    }
                },
            });
        });
    </script>
@endpush
