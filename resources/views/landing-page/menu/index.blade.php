@extends('layouts.main')
@section('title', __('Landing Page'))
@section('breadcrumb')
    <div class="col-md-12">
        <div class="page-header-title">
            <h4 class="m-b-10">{{ __('Menu Settings') }}</h4>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">{!! Html::link(route('home'), __('Dashboard'), ['']) !!}</li>
            <li class="breadcrumb-item">{{ __('Menu Settings') }}</li>
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
                                    'route' => ['landing.menusection1.store'],
                                    'method' => 'Post',
                                    'id' => 'froentend-form',
                                    'enctype' => 'multipart/form-data',
                                    'data-validate',
                                    'novalidate',
                                ]) !!}
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <h5 class="mb-0">{{ __('Menu Setting Section 1') }}</h5>
                                        </div>
                                        <div class="col-lg-4 d-flex justify-content-end">
                                            <div class="form-switch custom-switch-v1 d-inline-block">
                                                {!! Form::checkbox(
                                                    'menu_setting_section1_enable',
                                                    null,
                                                    Utility::getsettings('menu_setting_section1_enable') == 'on' ? true : false,
                                                    [
                                                        'class' => 'custom-control custom-switch form-check-input input-primary',
                                                        'id' => 'appsSettingEnableBtn',
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
                                                {{ Form::label('menu_image_section1', __('Menu Image'), ['class' => 'form-label']) }}
                                                *
                                                {!! Form::file('menu_image_section1', ['class' => 'form-control', 'id' => 'menu_image_section1']) !!}
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                {{ Form::label('menu_name_section1', __('Menu Name'), ['class' => 'form-label']) }}
                                                {!! Form::text('menu_name_section1', Utility::getsettings('menu_name_section1'), [
                                                    'class' => 'form-control',
                                                    'placeholder' => __('Enter menu name'),
                                                ]) !!}
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                {{ Form::label('menu_bold_name_section1', __('Menu Bold Name'), ['class' => 'form-label']) }}
                                                {!! Form::text('menu_bold_name_section1', Utility::getsettings('menu_bold_name_section1'), [
                                                    'class' => 'form-control',
                                                    'placeholder' => __('Enter menu bold name'),
                                                ]) !!}
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                {{ Form::label('menu_detail_section1', __('Menu Detail'), ['class' => 'form-label']) }}
                                                {!! Form::textarea('menu_detail_section1', Utility::getsettings('menu_detail_section1'), [
                                                    'class' => 'form-control',
                                                    'rows' => '3',
                                                    'placeholder' => __('Enter menu detail'),
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
                    <div class="card">
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="apps-setting" role="tabpanel"
                                aria-labelledby="landing-apps-setting">
                                {!! Form::open([
                                    'route' => ['landing.menusection2.store'],
                                    'method' => 'Post',
                                    'id' => 'froentend-form',
                                    'enctype' => 'multipart/form-data',
                                    'data-validate',
                                    'novalidate',
                                ]) !!}
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <h5 class="mb-0">{{ __('Menu Setting Section 2') }}</h5>
                                        </div>
                                        <div class="col-lg-4 d-flex justify-content-end">
                                            <div class="form-switch custom-switch-v1 d-inline-block">
                                                {!! Form::checkbox(
                                                    'menu_setting_section2_enable',
                                                    null,
                                                    Utility::getsettings('menu_setting_section2_enable') == 'on' ? true : false,
                                                    [
                                                        'class' => 'custom-control custom-switch form-check-input input-primary',
                                                        'id' => 'appsSettingEnableBtn',
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
                                                {{ Form::label('menu_image_section2', __('Menu Image'), ['class' => 'form-label']) }}
                                                *
                                                {!! Form::file('menu_image_section2', ['class' => 'form-control', 'id' => 'menu_image_section2']) !!}
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                {{ Form::label('menu_name_section2', __('Menu Name'), ['class' => 'form-label']) }}
                                                {!! Form::text('menu_name_section2', Utility::getsettings('menu_name_section2'), [
                                                    'class' => 'form-control',
                                                    'placeholder' => __('Enter menu name'),
                                                ]) !!}
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                {{ Form::label('menu_bold_name_section2', __('Menu Bold Name'), ['class' => 'form-label']) }}
                                                {!! Form::text('menu_bold_name_section2', Utility::getsettings('menu_bold_name_section2'), [
                                                    'class' => 'form-control',
                                                    'placeholder' => __('Enter menu bold name'),
                                                ]) !!}
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                {{ Form::label('menu_detail_section2', __('Menu Detail'), ['class' => 'form-label']) }}
                                                {!! Form::textarea('menu_detail_section2', Utility::getsettings('menu_detail_section2'), [
                                                    'class' => 'form-control',
                                                    'rows' => '3',
                                                    'placeholder' => __('Enter menu detail'),
                                                ]) !!}
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
                        </div>
                    </div>
                    <div class="card">
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="apps-setting" role="tabpanel"
                                aria-labelledby="landing-apps-setting">
                                {!! Form::open([
                                    'route' => ['landing.menusection3.store'],
                                    'method' => 'Post',
                                    'id' => 'froentend-form',
                                    'enctype' => 'multipart/form-data',
                                    'data-validate',
                                    'novalidate',
                                ]) !!}
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <h5 class="mb-0">{{ __('Menu Setting Section3') }}</h5>
                                        </div>
                                        <div class="col-lg-4 d-flex justify-content-end">
                                            <div class="form-switch custom-switch-v1 d-inline-block">
                                                {!! Form::checkbox(
                                                    'menu_setting_section3_enable',
                                                    null,
                                                    Utility::getsettings('menu_setting_section3_enable') == 'on' ? true : false,
                                                    [
                                                        'class' => 'custom-control custom-switch form-check-input input-primary',
                                                        'id' => 'appsSettingEnableBtn',
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
                                                {{ Form::label('menu_image_section3', __('Menu Image'), ['class' => 'form-label']) }}
                                                *
                                                {!! Form::file('menu_image_section3', ['class' => 'form-control', 'id' => 'menu_image_section3']) !!}
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                {{ Form::label('menu_name_section3', __('Menu Name'), ['class' => 'form-label']) }}
                                                {!! Form::text('menu_name_section3', Utility::getsettings('menu_name_section3'), [
                                                    'class' => 'form-control',
                                                    'placeholder' => __('Enter menu name'),
                                                ]) !!}
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                {{ Form::label('menu_bold_name_section3', __('Menu Bold Name'), ['class' => 'form-label']) }}
                                                {!! Form::text('menu_bold_name_section3', Utility::getsettings('menu_bold_name_section3'), [
                                                    'class' => 'form-control',
                                                    'placeholder' => __('Enter menu bold name'),
                                                ]) !!}
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                {{ Form::label('menu_detail_section3', __('Menu Detail'), ['class' => 'form-label']) }}
                                                {!! Form::textarea('menu_detail_section3', Utility::getsettings('menu_detail_section3'), [
                                                    'class' => 'form-control',
                                                    'rows' => '3',
                                                    'placeholder' => __('Enter menu detail'),
                                                ]) !!}
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
    <script>
        $(document).ready(function() {
            $(document).on('click', '.menu_create', function() {
                var action = $(this).data('url');
                var modal = $('#common_modal');
                $.get(action, function(response) {
                    modal.find('.modal-title').html('{{ __('Create Menu') }}');
                    modal.find('.body').html(response);
                    modal.modal('show');
                })
            });
        });
        $(document).ready(function() {
            $(document).on('click', '.menu_edit', function() {
                var action = $(this).data('url');
                var modal = $('#common_modal');
                $.get(action, function(response) {
                    modal.find('.modal-title').html('{{ __('Edit Menu') }}');
                    modal.find('.body').html(response);
                    modal.modal('show');
                })
            });
        });
    </script>
@endpush
