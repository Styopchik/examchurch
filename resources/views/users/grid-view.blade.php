@php
    $user = auth::user();
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
                <li class="breadcrumb-item">{!! Html::link(route('home'), __('Dashboard'), []) !!}</li>
                <li class="breadcrumb-item active">{{ __('Admins') }}</li>
            </ul>
            <div class="float-end">
                <div class="d-flex align-items-center">
                    <a href="{{ route('grid.view') }}" data-bs-toggle="tooltip" title="{{ __('List View') }}"
                        class="btn btn-sm btn-primary" data-bs-placement="bottom">
                        <i class="ti ti-list"></i>
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
                    <a href="{{ route('grid.view') }}" data-bs-toggle="tooltip" title="{{ __('List View') }}"
                        class="btn btn-sm btn-primary" data-bs-placement="bottom">
                        <i class="ti ti-list"></i>
                    </a>
                </div>
            </div>
        </div>
    @endsection
@endif
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                @foreach ($users as $user)
                    <div class="mb-3 col-xl-3 d-flex">
                        <div class="text-center text-white card h-100 w-100">
                            <div class="pb-0 border-0 card-header">
                                <div class="d-flex align-items-center">
                                    <span class="p-2 px-3 rounded badge bg-primary">{{ $user->type }}</span>
                                </div>
                                <div class="card-header-right">
                                    <div class="btn-group card-option">
                                        <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            @can('edit-user')
                                                <a class="dropdown-item" href="javascript:void(0);" id="edit-user"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    data-bs-original-title="{{ __('Edit') }}"
                                                    data-url="{{ route('users.edit', $user->id) }}"><i class="ti ti-edit"></i>
                                                    <span>{{ __('Edit') }}</span></a>
                                            @endcan
                                            @if ($user->email_verified_at != '')
                                                <a class="dropdown-item" href="{{ route('user.verified', $user->id) }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    data-bs-original-title="{{ __('Email Verified') }}">
                                                    <i class="ti ti-mail"></i><span>{{ __('Email Verified') }}</span></a>
                                            @else
                                                <a class="dropdown-item" href="{{ route('user.verified', $user->id) }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    data-bs-original-title="{{ __('Email Unverified') }}">
                                                    <i
                                                        class="ti ti-mail-forward"></i><span>{{ __('Email Unverified') }}</span></a>
                                            @endif
                                            @if ($user->phone_verified_at != '')
                                                <a class="dropdown-item"
                                                    href="{{ route('user.phoneverified', $user->id) }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    data-bs-original-title="{{ __('Phone Verified') }}">
                                                    <i
                                                        class="ti ti-message-circle"></i><span>{{ __('Phone Verified') }}</span></a>
                                            @else
                                                <a class="dropdown-item"
                                                    href="{{ route('user.phoneverified', $user->id) }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    data-bs-original-title="{{ __('Phone Unverified') }}">
                                                    <i
                                                        class="ti ti-message-circle"></i><span>{{ __('Phone Unverified') }}</span></a>
                                            @endif
                                            @can('impersonate-user')
                                                <a class="dropdown-item" target="_new"
                                                    href="{{ route('users.impersonate', $user->id) }}" data-bs-toggle="tooltip"
                                                    data-bs-placement="bottom" data-bs-original-title="{{ __('Impersonate') }}"
                                                    aria-label="{{ __('Impersonate') }}">
                                                    <i class="ti ti-new-section"><span
                                                            class="font-fmaily">{{ __('Impersonate') }}</span></i>
                                                </a>
                                            @endcan
                                            @can('delete-user')
                                                {!! Form::open([
                                                    'method' => 'DELETE',
                                                    'route' => ['users.destroy', $user->id],
                                                    'id' => 'delete-form-' . $user->id,
                                                    'class' => 'd-inline',
                                                ]) !!}
                                                <a href="#" class="dropdown-item show_confirm"
                                                    id="delete-form-{{ $user->id }}" data-bs-toggle="tooltip"
                                                    data-bs-placement="bottom" data-bs-original-title="{{ __('Delete') }}"><i
                                                        class="mr-0 ti ti-trash"></i><span>{{ __('Delete') }}</span></a>
                                                {!! Form::close() !!}
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <img src="{{ isset($user->avatar) ? Storage::url($user->avatar) : $user->avatar_image }}"
                                    alt="user-image" width="100px" class="rounded-circle">
                                <h4 class="mt-2 text-dark">{{ $user->name }}</h4>
                                <small class="text-dark">{{ $user->email }}</small>
                                <div class="mt-4">
                                    <div class="row justify-content-between align-items-center">
                                        <div class="text-center col-6">
                                            <span class="mb-0 font-bold d-block">{{ $user->plan_name }}</span>
                                        </div>
                                        <div class="mb-2 text-center col-6 Id ">
                                            @can('plan-upgrade-user')
                                                <a href="#" id="user-plan"
                                                    data-url="{{ route('user.plan', $user->id) }}" data-size="lg"
                                                    data-ajax-popup="true" class="text-sm btn small--btn btn-outline-primary"
                                                    data-title="{{ __('Upgrade Plan') }}">{{ __('Upgrade Plan') }}</a>
                                            @endcan
                                        </div>
                                        <div class="col-12">
                                            <hr class="my-3">
                                        </div>
                                        <div class="pb-2 text-center col-12">
                                            @if (!$user->plan_id)
                                                <span class="text-xs text-dark">{{ __('Plan Expired : Lifetime') }}</span>
                                            @else
                                                <span class="text-xs text-dark">{{ __('Plan Expired :') }}
                                                    {{ Utility::dateFormat($user->plan_expired_date) }}</span>
                                            @endif
                                        </div>
                                        <div class="my-2 text-center col-4">
                                            <span
                                                class="mb-0 text-sm font-bold text-dark d-block">{{ $user->max_users }}</span>
                                            <span class="text-sm d-block text-muted">{{ __('Users') }}</span>
                                        </div>
                                        <div class="my-2 text-center col-4">
                                            <span
                                                class="mb-0 text-sm d-block text-dark font-weight-bold">{{ $user->max_roles }}</span>
                                            <span class="text-sm d-block text-muted">{{ __('Roles') }}</span>
                                        </div>
                                        <div class="my-2 text-center col-4">
                                            <span
                                                class="mb-0 text-sm d-block font-weight-bold text-dark">{{ $user->max_form }}</span>
                                            <span class="text-sm d-block text-muted">{{ __('Forms') }}</span>
                                        </div>
                                        <div class="my-2 text-center col-4">
                                            <span
                                                class="mb-0 text-sm d-block font-weight-bold text-dark">{{ $user->max_booking }}</span>
                                            <span class="text-sm d-block text-muted">{{ __('Booking') }}</span>
                                        </div>
                                        <div class="my-2 text-center col-4">
                                            <span
                                                class="mb-0 text-sm d-block font-weight-bold text-dark">{{ $user->max_documents }}</span>
                                            <span class="text-sm d-block text-muted">{{ __('Documents') }}</span>
                                        </div>
                                        <div class="my-2 text-center col-4">
                                            <span
                                                class="mb-0 text-sm d-block font-weight-bold text-dark">{{ $user->max_polls }}</span>
                                            <span class="text-sm d-block text-muted">{{ __('Polls') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="mb-3 col-md-3 d-flex">
                    <a href="#" class="btn-addnew-project add_user h-100 w-100">
                        <div class="bg-primary proj-add-icon">
                            <i class="ti ti-plus"></i>
                        </div>
                        <h6 class="mt-4 mb-2">{{ __('New User') }}</h6>
                        <p class="text-center text-muted">{{ __('Click here to add new User') }}</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
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
