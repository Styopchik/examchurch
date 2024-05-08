@extends('layouts.main')
@section('title', __('Header Setting'))
@section('breadcrumb')
    <div class="col-md-12">
        <div class="page-header-title">
            <h4 class="m-b-10">{{ __('Header Setting') }}</h4>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">{!! Html::link(route('home'), __('Dashboard'), ['']) !!}</li>
            <li class="breadcrumb-item">{{ __('Header Setting') }}</li>
        </ul>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="card sticky-top">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            @include('landing-page.landingpage-sidebar')
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-lg-9 col-md-9 col-sm-9">
                                    <h5>{{ __('Header Sub Menu') }}</h5>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 justify-content-end d-flex">
                                    <a href="{{ route('header.sub.menu.create') }}" data-ajax-popup="true"
                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                        class="mx-1 btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
                                        <i class="ti ti-plus text-light"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('No') }}</th>
                                            <th>{{ __('Page Name') }}</th>
                                            <th>{{ __('Slug') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (is_array($headerSubMenus) || is_object($headerSubMenus))
                                            @php
                                                $ffNo = 1;
                                            @endphp
                                            @foreach ($headerSubMenus as $key => $headerSubMenu)
                                                @php
                                                    $pageName = App\Models\PageSetting::select('title')
                                                        ->where('id', $headerSubMenu->page_id)
                                                        ->first();
                                                    $parentName = App\Models\HeaderSetting::select('menu', 'slug')->first();
                                                @endphp
                                                <tr>
                                                    <td>{{ $ffNo++ }}</td>
                                                    <td>{{ $pageName->title }}</td>
                                                    <td>{{ $parentName['slug'] }}</td>
                                                    <td>
                                                        <span>
                                                            <a href="{{ route('header.sub.menu.edit', $headerSubMenu->id) }}"
                                                                data-ajax-popup="true" data-bs-toggle="tooltip"
                                                                data-bs-placement="bottom"
                                                                class="mx-1 btn btn-sm btn-primary"
                                                                data-bs-original-title="{{ __('Edit') }}">
                                                                <i class="ti ti-pencil text-light"></i>
                                                            </a>
                                                            {!! Form::open([
                                                                'method' => 'DELETE',
                                                                'class' => 'd-inline',
                                                                'route' => ['header.sub.menu.delete', $headerSubMenu->id],
                                                                'id' => 'delete-form-' . $headerSubMenu->id,
                                                            ]) !!}
                                                            <a href="javascript:void(0);"
                                                                class="btn btn-sm small btn-danger show_confirm"
                                                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                                id="delete-form-{{ $headerSubMenu->id }}"
                                                                data-bs-original-title="{{ __('Delete') }}">
                                                                <i class="text-white ti ti-trash"></i>
                                                            </a>
                                                            {!! Form::close() !!}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('style')
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/bootstrap-switch-button.min.css') }}">
@endpush
@push('script')
    <script src="{{ asset('assets/js/plugins/bootstrap-switch-button.min.js') }}"></script>
@endpush
