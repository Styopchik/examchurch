@extends('layouts.main')
@section('title', __('Edit Admin Request'))
@section('breadcrumb')
    <div class="col-md-12">
        <div class="page-header-title">
            <h4 class="m-b-10">{{ __('Edit Admin Request') }}</h4>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">{!! Html::link(route('home'),__('Dashboard'),['']) !!}</li>
            <li class="breadcrumb-item">{!! Html::link(route('requestuser.index'),__('Admin Requests'),['']) !!}</li>
            <li class="breadcrumb-item">{{ __('Edit Admin Request') }}</li>
        </ul>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="section-body">
            <div class="row">
                <div class="m-auto col-md-4">
                    <div class="card ">
                        <div class="card-header">
                            <h5> {{ __('Edit Admin Request') }}</h5>
                        </div>
                        {!! Form::model($requestUser, [
                            'route' => ['requestuser.update', $requestUser->id],
                            'method' => 'POST',
                            'enctype' => 'multipart/form-data',
                        ]) !!}
                        <div class="card-body">
                            <div class="form-group ">
                                {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}
                                <div class="input-group ">
                                    {!! Form::text('name', $requestUser->name, [
                                        'class' => 'form-control',
                                        'id' => 'name',
                                        'placeholder' => __('Enter name'),
                                        'required',
                                        'autofocus',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {{ Form::label('Email Address', __('Email Address'), ['class' => 'form-label']) }}
                                <div class="input-group">
                                    {!! Form::email('email', $requestUser->email, [
                                        'class' => 'form-control',
                                        'id' => 'email',
                                        'placeholder' => __('Enter email'),
                                        'required',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {{ Form::label('Password', __('Password'), ['class' => 'form-label']) }}
                                <div class="input-group ">
                                    {!! Form::password('password', [
                                        'class' => 'form-control pwstrength',
                                        'id' => 'password',
                                        'placeholder' => __('Enter password'),
                                        'data-indicator' => 'pwindicator',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {{ Form::label('Confirm Password', __('Confirm Password'), ['class' => 'form-label']) }}
                                <div class="input-group ">
                                    {!! Form::password('password_confirmation', [
                                        'class' => 'form-control',
                                        'id' => 'password-confirm',
                                        'placeholder' => __('Enter confirm password'),
                                    ]) !!}
                                </div>
                            </div>
                            <div class="mb-3 form-group">
                                {{ Form::label('country_code', __('Country Code'), ['class' => 'd-block form-label']) }}
                                <select id="country_code" name="country_code"class="form-control" data-trigger>
                                    @foreach (\App\Core\Data::getCountriesList() as $key => $value)
                                        <option data-kt-flag="{{ $value['flag'] }}"
                                            {{ $value['phone_code'] == $requestUser->country_code ? 'selected' : '' }} value="{{ $key }}">
                                            +{{ $value['phone_code'] }} {{ $value['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 form-group">
                                {{ Form::label('phone', __('Phone Number'), ['class' => 'form-label']) }}
                                {!! Form::number('phone', null, [
                                    'autofocus' => '',
                                    'required' => true,
                                    'autocomplete' => 'off',
                                    'placeholder' => 'Enter phone Number',
                                    'class' => 'form-control',
                                ]) !!}
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="mb-3 btn-flt float-end">
                                {!! Html::link(route('requestuser.index'),__('Cancel'),['class'=>'btn btn-secondary']) !!}
                                {{ Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) }}
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
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
