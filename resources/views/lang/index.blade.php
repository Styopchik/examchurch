@extends('layouts.main')
@section('title', __('Languages'))
@section('breadcrumb')
    <div class="col-md-12">
        <div class="page-header-title">
            <h4 class="m-b-10">{{ __('Languages') }}</h4>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Languages') }}</li>
        </ul>
        <div class="float-end">
            <div class="d-flex align-items-center">
                @can('create-language')
                    <button data-url="{{ route('create.language', [$currantLang]) }}" type="button" data-bs-toggle="tooltip"
                        data-bs-original-title="{{ __('Create') }}" class="btn btn-sm btn-primary create_lang"
                        data-bs-placement="bottom">
                        <i class="ti ti-plus"></i>
                    </button>
                @endcan
                @can('delete-language')
                    {!! Form::open([
                        'method' => 'DELETE',
                        'route' => ['lang.destroy', $currantLang],
                        'id' => 'delete-form-' . $currantLang,
                    ]) !!}
                    <a href="#" data-bs-toggle="tooltip" data-bs-placement="bottom" title=""
                        data-bs-original-title="{{ __('Delete') }}"
                        class="btn btn-sm btn-danger float-end btn-lg text-light ms-1 show_confirm">
                        <i class="ti ti-trash"></i>
                    </a>
                    {!! Form::close() !!}
                @endcan
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="card sticky-top">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            @foreach ($languages as $lang)
                                <a href="{{ route('manage.language', [$lang]) }}"
                                    class="list-group-item list-group-item-action border-0 {{ $currantLang == $lang ? 'active' : '' }}">{{ Str::upper($lang) }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    <div id="useradd-1" class="card">
                        <div class="card-header">
                            <ul class="mb-3 nav nav-pills" id="pills-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="account-details-tab" data-bs-toggle="pill"
                                        href="#account-details" role="tab" aria-controls="account-details"
                                        aria-selected="true">{{ __('Labels') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="login-details-tab" data-bs-toggle="pill" href="#login-details"
                                        role="tab" aria-controls="login-details"
                                        aria-selected="false">{{ __('Message') }}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="pt-0 card-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="account-details" role="tabpanel"
                                    aria-labelledby="account-details-tab">
                                    {!! Form::open([
                                        'route' => ['store.language.data', $currantLang],
                                        'method' => 'POST',
                                        'class' => 'form-horizontal',
                                    ]) !!}
                                    <div class="row">
                                        @foreach ($arrLabel as $label => $value)
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="label[{{ $label }}]">{{ $label }}
                                                    </label>
                                                    <input type="text" class="form-control"
                                                        name="label[{{ $label }}]" id="label[{{ $label }}]" value="{{ $value }}">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="tab-pane" id="login-details" role="tabpanel"
                                    aria-labelledby="login-details-tab">
                                    <div class="row">
                                        @foreach ($arrMessage as $fileName => $fileValue)
                                            <div class="col-lg-12">
                                                <h3>{{ ucfirst($fileName) }}</h3>
                                            </div>
                                            @foreach ($fileValue as $label => $value)
                                                @if (is_array($value))
                                                    @foreach ($value as $label2 => $value2)
                                                        @if (is_array($value2))
                                                            @foreach ($value2 as $label3 => $value3)
                                                                @if (is_array($value3))
                                                                    @foreach ($value3 as $label4 => $value4)
                                                                        @if (is_array($value4))
                                                                            @foreach ($value4 as $label5 => $value5)
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label
                                                                                            for="message[{{ $fileName }}][{{ $label }}][{{ $label2 }}][{{ $label3 }}][{{ $label4 }}][{{ $label5 }}]"
                                                                                            class="form-label">{{ $fileName }}.{{ $label }}.{{ $label2 }}.{{ $label3 }}.{{ $label4 }}.{{ $label5 }}</label>
                                                                                        <input type="text"
                                                                                            class="form-control"
                                                                                            name="message[{{ $fileName }}][{{ $label }}][{{ $label2 }}][{{ $label3 }}][{{ $label4 }}][{{ $label5 }}]"
                                                                                            id="message[{{ $fileName }}][{{ $label }}][{{ $label2 }}][{{ $label3 }}][{{ $label4 }}][{{ $label5 }}]"
                                                                                            value="{{ $value5 }}">
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        @else
                                                                            <div class="col-lg-6">
                                                                                <div class="form-group">
                                                                                    <label
                                                                                        for="message[{{ $fileName }}][{{ $label }}][{{ $label2 }}][{{ $label3 }}][{{ $label4 }}]"
                                                                                        class="form-label">{{ $fileName }}.{{ $label }}.{{ $label2 }}.{{ $label3 }}.{{ $label4 }}</label>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        name="message[{{ $fileName }}][{{ $label }}][{{ $label2 }}][{{ $label3 }}][{{ $label4 }}]"
                                                                                        id="message[{{ $fileName }}][{{ $label }}][{{ $label2 }}][{{ $label3 }}][{{ $label4 }}]"
                                                                                        value="{{ $value4 }}">
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    @endforeach
                                                                @else
                                                                    <div class="col-lg-6">
                                                                        <div class="form-group">
                                                                            <label
                                                                                for="message[{{ $fileName }}][{{ $label }}][{{ $label2 }}][{{ $label3 }}]"
                                                                                class="form-label">{{ $fileName }}.{{ $label }}.{{ $label2 }}.{{ $label3 }}</label>
                                                                            <input type="text" class="form-control"
                                                                                name="message[{{ $fileName }}][{{ $label }}][{{ $label2 }}][{{ $label3 }}]"
                                                                                id="message[{{ $fileName }}][{{ $label }}][{{ $label2 }}][{{ $label3 }}]"
                                                                                value="{{ $value3 }}">
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            <div class="col-lg-6">
                                                                <div class="form-group">
                                                                    <label
                                                                        for="message[{{ $fileName }}][{{ $label }}][{{ $label2 }}]"
                                                                        class="form-label">{{ $fileName }}.{{ $label }}.{{ $label2 }}</label>
                                                                    <input type="text" class="form-control"
                                                                        name="message[{{ $fileName }}][{{ $label }}][{{ $label2 }}]"
                                                                        id="message[{{ $fileName }}][{{ $label }}][{{ $label2 }}]"
                                                                        value="{{ $value2 }}">
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <label
                                                                for="message[{{ $fileName }}][{{ $label }}]"
                                                                class="form-label">{{ $fileName }}.{{ $label }}</label>
                                                            <input type="text" class="form-control"
                                                                name="message[{{ $fileName }}][{{ $label }}]"
                                                                id="message[{{ $fileName }}][{{ $label }}]"
                                                                value="{{ $value }}">
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="mb-3 col-lg-12 float-end">
                                {{ Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary float-end']) }}
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
        $(document).on('click', '.create_lang', function() {
            var url = $(this).attr('data-url');
            $.ajax({
                type: 'GET',
                url: url,
                data: {},
                success: function(response) {
                    $("#common_modal .modal-title").html('{{ __('Create Language') }}');
                    $("#common_modal .body").html(response);
                    $("#common_modal").modal('show');
                },
                error: function(response) {}
            });
        });
    </script>
@endpush
