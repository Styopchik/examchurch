@extends('layouts.main')
@section('title', __('Create Page Settings'))
@section('breadcrumb')
<div class="col-md-12">
    <div class="page-header-title">
        <h4 class="m-b-10">{{ __('Create Page Settings') }}</h4>
    </div>
    <ul class="breadcrumb">
        <li class="breadcrumb-item">{!! Html::link(route('home'), __('Dashboard'), ['']) !!}</li>
        <li class="breadcrumb-item">{!! Html::link(route('page-setting.index'), __('Page Settings'), []) !!}</li>
        <li class="breadcrumb-item">{{ __('Create Page Settings') }}</li>
    </ul>
</div>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="mx-auto col-xl-7 col-lg-7">
                    <div class="tab-content" id="pills-tabContent">
                        <div class="card">
                            <div class="tab-pane fade show active" id="apps-setting" role="tabpanel"
                                aria-labelledby="landing-apps-setting">
                                {!! Form::open([
                                    'route' => ['page-setting.store'],
                                    'method' => 'POST',
                                    'id' => 'froentend-form',
                                    'enctype' => 'multipart/form-data',
                                ]) !!}
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-lg-8 d-flex align-items-center">
                                            <h5 class="mb-0">{{ __('Create Page Setting') }}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                {{ Form::label('title', __('Title'), ['class' => 'form-label']) }}
                                                *
                                                {!! Form::text('title', null, ['class' => 'form-control', 'placeholder'=>"Enter Page Title",  'id' => 'title']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label" for="exampleFormControlSelect1"> {{ __('Select Type') }}
                                                </label>
                                                <select class="form-select" id="type" name="type" data-trigger>
                                                    <option selected disabled value="all" class="link"> {{ __('Select type') }}
                                                    </option>
                                                    <option value="link" class="link"> {{ __('Link') }} </option>
                                                    <option value="desc" class="description"> {{ __('Descrtiption') }} </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12 d-none" id="link">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    {{ Form::label('url_type', __('Link Type'), ['class' => 'form-label']) }}
                                                    <select name="url_type" class="form-control" data-trigger>
                                                        <option value="" selected disabled>{{ __('Select Page Type') }}</option>
                                                        <option value="ifream">{{ __('Ifream') }}</option>
                                                        <option value="internal link">{{ __('Internal Link') }}</option>
                                                        <option value="external link">{{ __('External Link')}}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    {{ Form::label('page_url', __('Page URL'), ['class' => 'form-label']) }}
                                                    {!! Form::text('page_url', null, [
                                                        'class' => 'form-control',
                                                        'placeholder' => __('Enter Link URL'),
                                                    ]) !!}
                                                    <small class="text-muted"><b>{{ __('Simple Page') }}</b> {{ __(':- Leave it Blank') }} </small><br>
                                                    <small class="text-muted"><b>{{ __('Internal Link') }}</b> {{ __(':- http://fulltenancy.vhost/') }} </small><br>
                                                    <small class="text-muted"><b>{{ __('External Link') }}</b> :- {{ __('http://google.com/') }} </small>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    {{ Form::label('friendly_url', __('Search Friendly URL'), ['class' => 'form-label']) }}
                                                    {!! Form::text('friendly_url', null, [
                                                        'class' => 'form-control',
                                                        'placeholder' => __('Enter Search Friendly URL'),
                                                    ]) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 d-none" id="description">
                                            <div class="form-group">
                                                {{ Form::label('description', __('Page Detail'), ['class' => 'form-label']) }}
                                                {!! Form::textarea('descriptions', null, [
                                                    'class' => 'form-control',
                                                    'rows' => '1',
                                                    'placeholder' => __('Enter Page detail'),
                                                ]) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="text-end">
                                        {!! Html::link(route('page-setting.index'), __('Cancel'), ['class'=>'btn btn-secondary']) !!}
                                        {{ Form::button(__('Save'), ['type' => 'submit', 'id' => 'save-btn', 'class' => 'btn btn-primary']) }}
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
@push('script')
    <script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>
    <script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
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
        CKEDITOR.replace('descriptions', {
            filebrowserUploadUrl: "{{ route('ckeditor.upload', ['_token' => csrf_token()]) }}",
            filebrowserUploadMethod: 'form'
        });
        $(document).on('change', 'select[name="type"]', function() {
            $('#link').hide();
            $('#description').hide();
            var test = $(this).val();
            if (test == 'link') {
                $('#description').hide();
                $('#link').show();
                $("#link").fadeIn(500);
                $("#link").removeClass('d-none');
                $('#description').fadeOut(500);
            } else {
                $('#link').hide();
                $('#description').show();
                $("#link").fadeOut(500);
                $("#description").fadeIn(500);
                $("#description").removeClass('d-none');
            }
        });
    </script>
@endpush
