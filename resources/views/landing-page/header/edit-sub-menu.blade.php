@extends('layouts.main')
@section('title', __('Landing Page'))
@section('breadcrumb')
    <div class="col-md-12">
        <div class="page-header-title">
            <h4 class="m-b-10">{{ __('Landing Page') }}</h4>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">{!! Html::link(route('home'), __('Dashboard'), ['']) !!}</li>
            <li class="breadcrumb-item">{{ __('Landing Page') }}</li>
        </ul>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="mx-auto col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>{{ __('Edit Footer Sub Menu') }}</h5>
                        </div>
                        {!! Form::open([
                            'route' => ['header.sub.menu.update', $headerPage->id],
                            'method' => 'Post',
                            'class' => 'form-horizontal',
                            'enctype' => 'multipart/form-data',
                            'data-validate',
                            'novalidate',
                        ]) !!}
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group">
                                    {{ Form::label('page_id', __('Select Page'), ['class' => 'form-label']) }}
                                    {!! Form::select('page_id', $pages, $headerPage->page_id, [
                                        'class' => 'form-select',
                                        'required',
                                        'data-trigger',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="text-end">
                                <a href="{{ route('landing.header.index') }}"
                                    class="btn btn-secondary">{{ __('Cancel') }}</a>
                                {{ Form::button(__('Save'), ['type' => 'submit',  'class' => 'btn btn-primary']) }}
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
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
                    searchPlaceholderValue: 'This is a search placeholder',
                });
            }
        });
    </script>
@endpush
