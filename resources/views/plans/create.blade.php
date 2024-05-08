@extends('layouts.main')
@section('title', __('Create Plan'))
@section('breadcrumb')
    <div class="col-md-12">
        <div class="page-header-title">
            <h4 class="m-b-10">{{ __('Create Plan') }}</h4>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">{!! Html::link(route('home'), __('Dashboard'), ['']) !!}</li>
            <li class="breadcrumb-item">{!! Html::link(route('plans.index'), __('Plans'), ['']) !!}</li>
            <li class="breadcrumb-item">{{ __('Create Plan') }}</li>
        </ul>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="section-body">
            <div class="m-auto col-lg-6 col-md-6 col-sm-8">
                <div class="card ">
                    <div class="card-header">
                        <h5> {{ __('Create Plan') }}</h5>
                    </div>
                    {!! Form::open([
                        'route' => 'plans.store',
                        'method' => 'Post',
                        'enctype' => 'multipart/form-data',
                        'data-validate',
                        'novalidate',
                    ]) !!}
                    <div class="card-body">
                        <div class="form-group">
                            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}
                            {!! Form::text('name', null, ['placeholder' => __('Enter name'), 'class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="form-group">
                            {{ Form::label('price', __('Price'), ['class' => 'form-label']) }}
                            {!! Form::number('price', null, ['placeholder' => __('Enter price'), 'class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="form-group">
                            {{ Form::label('duration', __('Duration'), ['class' => 'form-label']) }}
                            <div class="row">
                                <div class="col-md-6">
                                    {!! Form::number('duration', null, [
                                        'placeholder' => __('Enter duration'),
                                        'class' => 'form-control',
                                        'required',
                                    ]) !!}
                                </div>
                                <div class="col-md-6">
                                    <div class=" d-block">
                                        <select class="form-control" name="durationtype" data-trigger
                                            id="choices-single-default">
                                            <option selected value="Month">{{ __('Month') }}</option>
                                            <option value="Year">{{ __('Year') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('max_users', __('Maximum users'), ['class' => 'form-label']) }}
                            {!! Form::number('max_users', null, [
                                'placeholder' => __('Enter maximum users'),
                                'class' => 'form-control',
                                'required',
                            ]) !!}
                        </div>
                        <div class="form-group">
                            {{ Form::label('max_roles', __('Maximum Roles'), ['class' => 'form-label']) }}
                            {!! Form::number('max_roles', null, [
                                'placeholder' => __('Enter maximum roles'),
                                'class' => 'form-control',
                                'required',
                            ]) !!}
                        </div>
                        <div class="form-group">
                            {{ Form::label('max_form', __('Maximum forms'), ['class' => 'form-label']) }}
                            {!! Form::number('max_form', null, [
                                'placeholder' => __('Enter maximum forms'),
                                'class' => 'form-control',
                                'required',
                            ]) !!}
                        </div>
                        <div class="form-group">
                            {{ Form::label('max_booking', __('Maximum Bookings'), ['class' => 'form-label']) }}
                            {!! Form::number('max_booking', null, [
                                'placeholder' => __('Enter maximum bookings'),
                                'class' => 'form-control',
                                'required',
                            ]) !!}
                        </div>
                        <div class="form-group">
                            {{ Form::label('max_documents', __('Maximum Documents'), ['class' => 'form-label']) }}
                            {!! Form::number('max_documents', null, [
                                'placeholder' => __('Enter maximum documents'),
                                'class' => 'form-control',
                                'required',
                            ]) !!}
                        </div>
                        <div class="form-group">
                            {{ Form::label('max_polls', __('Maximum Polls'), ['class' => 'form-label']) }}
                            {!! Form::number('max_polls', null, [
                                'placeholder' => __('Enter maximum polls'),
                                'class' => 'form-control',
                                'required',
                            ]) !!}
                        </div>
                        <div class="form-group">
                            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
                            {!! Form::textarea('description', null, [
                                'class' => 'form-control',
                                'rows' => '2',
                                'placeholder' => __('Enter description'),
                            ]) !!}
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="mb-3 btn-flt float-end">
                            {!! Html::link(route('plans.index'), __('Cancel'), ['class' => 'btn btn-secondary']) !!}
                            {{ Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) }}
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>
    <script>
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
    </script>
@endpush
