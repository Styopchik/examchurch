@extends('layouts.main')
@section('title', __('Contact US Settings'))
@section('breadcrumb')
    <div class="col-md-12">
        <div class="page-header-title">
            <h4 class="m-b-10">{{ __('Contact US Settings') }}</h4>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">{!! Html::link(route('home'), __('Dashboard'), ['']) !!}</li>
            <li class="breadcrumb-item">{{ __('Contact US Settings') }}</li>
        </ul>
</div>@endsection
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
                                    'route' => ['landing.contactus.store'],
                                    'method' => 'Post',
                                    'id' => 'froentend-form',
                                    'enctype' => 'multipart/form-data',
                                    'data-validate',
                                    'novalidate',
                                ]) !!}
                                <div class="card-header">
                                    <div class="row align-items-center">
                                        <div class="col-lg-8">
                                            <h5 class="mb-0">{{ __('Contact Us Setting') }}</h5>
                                        </div>
                                        <div class="col-lg-4 d-flex justify-content-end">
                                            <div class="form-switch custom-switch-v1 d-inline-block">
                                                {!! Form::checkbox(
                                                    'contactus_setting_enable',
                                                    null,
                                                    Utility::keysettings('contactus_setting_enable', 1) == 'on' ? true : false,
                                                    [
                                                        'class' => 'custom-control custom-switch form-check-input input-primary',
                                                        'id' => 'startViewSettingEnableBtn',
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
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                {{ Form::label('contactus_name', __('Contact Us Name'), ['class' => 'form-label']) }}
                                                {!! Form::text('contactus_name', Utility::keysettings('contactus_name', 1), [
                                                    'class' => 'form-control',
                                                    'placeholder' => __('Enter contact us name'),
                                                ]) !!}
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                {{ Form::label('contactus_bold_name', __('Contact Us Bold Name'), ['class' => 'form-label']) }}
                                                {!! Form::text('contactus_bold_name', Utility::keysettings('contactus_bold_name', 1), [
                                                    'class' => 'form-control',
                                                    'placeholder' => __('Enter contact us bold name'),
                                                ]) !!}
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                {{ Form::label('contactus_detail', __('Contact Us Detail'), ['class' => 'form-label']) }}
                                                {!! Form::text('contactus_detail', Utility::keysettings('contactus_detail', 1), [
                                                    'class' => 'form-control',
                                                    'placeholder' => __('Enter contact us detail'),
                                                ]) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <h5>{{ __('Contact Us Details') }}</h5>
                                            <div class="form-group">
                                                {{ Form::label('contact_email', __('Contact Email'), ['class' => 'col-form-label']) }}
                                                <div class="custom-input-group">
                                                    {!! Form::text('contact_email', Utility::keysettings('contact_email', 1), [
                                                        'class' => 'form-control',
                                                        'placeholder' => __('Enter contact email'),
                                                    ]) !!}
                                                </div>
                                                <p class="text-sm">
                                                    {{ _('This email is for receive email when user submit contact us form.') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    {{ Form::label('latitude', __('Latitude'), ['class' => 'col-form-label']) }}
                                                    <div class="custom-input-group">
                                                        {!! Form::text('latitude', Utility::keysettings('latitude', 1), [
                                                            'class' => 'form-control',
                                                            'placeholder' => __('Enter latitude'),
                                                        ]) !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    {{ Form::label('longitude', __('Longitude'), ['class' => 'col-form-label']) }}
                                                    <div class="custom-input-group">
                                                        {!! Form::text('longitude', Utility::keysettings('longitude', 1), [
                                                            'class' => 'form-control',
                                                            'placeholder' => __('Enter longitude'),
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
@push('style')
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/bootstrap-switch-button.min.css') }}">
@endpush
@push('script')
    <script src="{{ asset('assets/js/plugins/bootstrap-switch-button.min.js') }}"></script>
@endpush
