@extends('layouts.main')
@section('title', 'Create Category')
@section('breadcrumb')
    <div class="col-md-12">
        <div class="page-header-title">
            <h4 class="m-b-10">{{ __('Create Category') }}</h4>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">{!! Html::link(route('home'), __('Dashboard'), []) !!}</li>
            <li class="breadcrumb-item"><a href="{{ route('blogs-category.index') }}">{{ __('Blog Category') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Create Category') }}</li>
        </ul>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="m-auto col-xl-6 col-lg-6 col-md-6 col-sm-12">
            {!! Form::open([
                'route' => 'blogs-category.store',
                'method' => 'Post',
                'enctype' => 'multipart/form-data',
                'data-validate',
            ]) !!}
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Create Category') }}</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}
                        {!! Form::text('name', null, ['placeholder' => __('Enter name'), 'class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}
                        {!! Form::select('status', ['1' => 'Active', '0' => 'Deactive'], 'Active', [
                            'class' => 'form-select',
                            'data-trigger',
                        ]) !!}
                    </div>
                </div>
                <div class="card-footer">
                    <div class="text-end">
                        {!! Html::link(route('blogs-category.index'), __('Cancel'), ['class' => 'btn btn-secondary']) !!}
                        {{ Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) }}
                    </div>
                </div>
                {!! Form::close() !!}
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
                    searchPlaceholderValue: 'This is a search placeholder',
                });
            }
        });
    </script>
@endpush
