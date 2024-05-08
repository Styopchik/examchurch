@extends('layouts.main')
@section('title', __('Edit Email Template'))
@section('breadcrumb')
    <div class="col-md-12">
        <div class="page-header-title">
            <h4 class="m-b-10">{{ __('Edit Email Template') }}</h4>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('email-template.index') }}">{{ __('Email Templates') }}</a></li>
            <li class="breadcrumb-item">{{ __('Edit Email Template') }}</li>
        </ul>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="main-content">
            <section class="section">
                <div class="m-auto col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5>{{ __('Edit Email Template') }}</h5>
                        </div>
                        <div class="card-body">
                            {!! Form::model($mailTemplate, ['method' => 'PATCH', 'route' => ['email-template.update', $mailTemplate->id]]) !!}
                            <div class="form-group col-md-6">
                                {{ Form::label('variables', __('Variables ;'), ['class' => 'form-label fw-bolder text-dark fs-6']) }}
                                @foreach ($mailTemplate->variables as $variables)
                                    <span class="fw-bolder text-dark fs-6">{{ $variables }}</span>
                                @endforeach
                            </div>
                            <div class="form-group fv-row mb-7">
                                {{ Form::label('mailable', __('Mailable'), ['class' => 'form-label fw-bolder text-dark fs-6']) }}
                                {!! Form::text('mailable', null, [
                                    'autofocus' => '',
                                    'required' => true,
                                    'autocomplete' => 'off',
                                    'class' => 'form-control form-control-lg form-control-solid',
                                    'readonly' . ($errors->has('mailable') ? ' is-invalid' : null),
                                ]) !!}
                                @error('mailable')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group fv-row mb-7">
                                {{ Form::label('subject', __('Subject'), ['class' => 'form-label fw-bolder text-dark fs-6']) }}
                                {!! Form::text('subject', null, [
                                    'autofocus' => '',
                                    'required' => true,
                                    'autocomplete' => 'off',
                                    'class' => 'form-control form-control-lg form-control-solid',
                                    'readonly' . ($errors->has('subject') ? ' is-invalid' : null),
                                ]) !!}
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group fv-row mb-7">
                                {{ Form::label('html_template', __('HTML Template'), ['class' => 'form-label fw-bolder text-dark fs-6']) }}
                                {!! Form::textarea('html_template', null, [
                                    'required' => true,
                                    'autocomplete' => 'off',
                                    'class' =>
                                        'form-control form-control-lg form-control-solid' . ($errors->has('html_template') ? ' is-invalid' : null),
                                ]) !!}
                                @error('html_template')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="float-end">
                                {!! Html::link(route('email-template.index'), __('Cancel'), ['class' => 'btn btn-secondary']) !!}
                                {{ Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) }}
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
@push('script')
    <script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
    <script>
        CKEDITOR.replace('html_template', {
            filebrowserUploadUrl: "{{ route('ckeditor.upload', ['_token' => csrf_token()]) }}",
            filebrowserUploadMethod: 'form'
        });
    </script>
@endpush
