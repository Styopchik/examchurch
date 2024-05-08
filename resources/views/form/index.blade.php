@extends('layouts.main')
@section('title', __('Forms'))
@section('breadcrumb')
    <div class="col-md-12">
        <div class="page-header-title">
            <h4 class="m-b-10">{{ __('Forms') }}</h4>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">{!! Html::link(route('home'), __('Dashboard'), []) !!}</li>
            <li class="breadcrumb-item active"> {{ __('Forms') }} </li>
        </ul>
        <div class="float-end d-flex align-items-center">
            <div class="me-2">
                <button class="btn btn-primary btn-sm" id="filter_btn" data-bs-toggle="tooltip" title="{{ __('Filter') }}"
                    data-bs-placement="bottom"><i class="fas fa-filter"></i></button>
            </div>
            <div class="d-flex align-items-center">
                <a href="{{ route('grid.form.view', 'view') }}" data-bs-toggle="tooltip" title="{{ __('Grid View') }}"
                    class="btn btn-sm btn-primary" data-bs-placement="bottom">
                    <i class="ti ti-layout-grid"></i>
                </a>
            </div>
        </div>
    </div>
    <div id="filter" style="display:none">
        <div class="card mt-3 mb-0">
            <div class="card-header">
                <h5>Filter</h5>
            </div>
            <div class="card-body">
                <div class="row align-items-end filters">
                    <div class="col-md-4 col-sm-12 form-group">
                        {{ Form::label('created_at', __('date'), ['class' => 'form-label']) }}
                        {!! Form::text('filterdate', null, [
                            'id' => 'filterdate',
                            'class' => 'form-control created_at',
                            'placeholder' => 'select date',
                        ]) !!}
                    </div>
                    <div class="col-md-4 col-sm-12 form-group">
                        {{ Form::label('category', __('Category'), ['class' => 'form-label']) }}
                        {!! Form::select('category[]', $categories, null, [
                            'class' => 'form-control category',
                            'id' => 'choices-multiple-remove-button',
                            'multiple' => 'multiple',
                            'data-trigger',
                        ]) !!}
                    </div>
                    <div class="col-md-4 col-sm-12 form-group">
                        {{ Form::label('roles', __('Roles'), ['class' => 'form-label']) }}
                        <select name="roles" id="role" class='form-control roles' data-trigger>
                            <option value="" selected>Select Role</option>
                            @foreach ($roles as $key => $role)
                                <option value="{{ $key }}">{{ $role }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-footer ms-auto">
                {!! Form::button(__('Apply'), ['id' => 'applyfilter', 'class' => 'btn btn-primary']) !!}
                {!! Form::button(__('Clear'), ['id' => 'clearfilter', 'class' => 'btn btn-secondary']) !!}
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        {{ $dataTable->table(['width' => '100%']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('style')
    @include('layouts.includes.datatable-css')
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/flatpickr.min.css') }}">
@endpush
@push('script')
    @include('layouts.includes.datatable-js')
    {{ $dataTable->scripts() }}
    <script src="{{ asset('assets/js/plugins/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>

    <script>
            function copyToClipboard(element) {
            console.log(element);
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val($(element).data('url')).select();
            document.execCommand("copy");
            $temp.remove();
            showToStr('Great!', '{{ __('Copied.') }}', 'success',
                '{{ asset('assets/images/notification/ok-48.png') }}', 4000);
        }
        $(function() {
            $(document).on('click', '#share-qr-code', function() {
                var action = $(this).data('share');
                var modal = $('#common_modal2');
                $.get(action, function(response) {
                    modal.find('.modal-title').html('{{ __('QR Code') }}');
                    modal.find('.modal-body').html(response.html);
                    feather.replace();
                    modal.modal('show');
                })
            });
        });

        $(document).on('click', "#filter_btn", function() {
            $("#filter").toggle("slow")
        });

        document.querySelector("#filterdate").flatpickr({
            mode: "range"
        });

        var multipleCancelButton = new Choices(
            '#choices-multiple-remove-button', {
                removeItemButton: true,
            }
        );
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
