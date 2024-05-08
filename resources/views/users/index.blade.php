@php
    $user = Auth::user();
@endphp
@extends('layouts.main')
@if ($user->type == 'Super Admin')
    @section('title', __('Admins'))
    @section('breadcrumb')
        <div class="col-md-12">
            <div class="page-header-title">
                <h4 class="m-b-10">{{ __('Admins') }}</h4>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">{!! Html::link(route('home'), __('Dashboard'), ['']) !!}</li>
                <li class="breadcrumb-item">{{ __('Admins') }}</li>
            </ul>
            <div class="float-end">
                <div class="d-flex align-items-center">
                    <a href="{{ route('grid.view', 'view') }}" data-bs-toggle="tooltip" title="{{ __('Grid View') }}"
                        class="btn btn-sm btn-primary" data-bs-placement="bottom">
                        <i class="ti ti-layout-grid"></i>
                    </a>
                </div>
            </div>
        </div>
    @endsection
@else
    @section('title', __('Users'))
    @section('breadcrumb')
        <div class="col-md-12">
            <div class="page-header-title">
                <h4 class="m-b-10">{{ __('Users') }}</h4>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">{!! Html::link(route('home'), __('Dashboard'), []) !!}</li>
                <li class="breadcrumb-item active">{{ __('Users') }}</li>
            </ul>
            <div class="float-end">
                <div class="d-flex align-items-center">
                    <a href="{{ route('grid.view', 'view') }}" data-bs-toggle="tooltip" title="{{ __('Grid View') }}"
                        class="btn btn-sm btn-primary" data-bs-placement="bottom">
                        <i class="ti ti-layout-grid"></i>
                    </a>
                </div>
            </div>
        </div>
    @endsection
@endif
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
@endpush
@push('script')
    @include('layouts.includes.datatable-js')
    {{ $dataTable->scripts() }}
    <script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>
    <script>
        $(function() {
            $(document).on('click', '.add_user', function() {
                var modal = $('#common_modal');
                $.ajax({
                    type: "GET",
                    url: '{{ route('users.create') }}',
                    data: {},
                    success: function(response) {
                        if ("{{ $user->type }}" == 'Super Admin') {
                            modal.find('.modal-title').html('{{ __('Create Admin') }}');
                        } else {
                            modal.find('.modal-title').html('{{ __('Create User') }}');
                        }
                        modal.find('.body').html(response.html);
                        modal.modal('show');
                        if ("{{ $user->type }}" != 'Super Admin') {
                            var multipleCancelButton = new Choices('#roles', {
                                removeItemButton: true,
                            });
                        }
                        var multipleCancelButton = new Choices('#country_code', {
                            removeItemButton: true,
                        });
                    },
                    error: function(error) {}
                });
            });
            
            $(document).on('click', '#user-plan', function() {
                var action = $(this).data('url');
                var modal = $('#common_modal');
                $.get(action, function(response) {
                    modal.find('.modal-dialog').addClass('modal-xl');
                    modal.find('.modal-title').html('{{ __('Upgrade Plan') }}');
                    modal.find('.body').html(response);
                    modal.modal('show');
                })
            });
            
            $(document).on('click', '#edit-user', function() {
                var action = $(this).data('url');
                var modal = $('#common_modal');
                $.get(action, function(response) {
                    if ("{{ $user->type }}" == 'Super Admin') {
                        modal.find('.modal-title').html('{{ __('Edit Admin') }}');
                    } else {
                        modal.find('.modal-title').html('{{ __('Edit User') }}');
                    }
                    modal.find('.body').html(response.html);
                    modal.modal('show');
                    if ("{{ $user->type }}" != 'Super Admin') {
                        var multipleCancelButton = new Choices('#roles', {
                            removeItemButton: true,
                        });
                    }
                    var multipleCancelButton = new Choices('#country_code', {
                        removeItemButton: true,
                    });
                })
            });
        });
    </script>
@endpush
