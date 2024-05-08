@php
    use Carbon\Carbon;
    use App\Models\Setting;
    use App\Facades\UtilityFacades;
    $payment_type = [];
    $currency_symbol = env('CURRENCY_SYMBOL');
@endphp
@extends('layouts.main')
@section('title', __('Plans'))
@section('breadcrumb')
    <div class="col-md-12">
        <div class="page-header-title">
            <h4 class="m-b-10">{{ __('Plans') }}</h4>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">{!! Html::link(route('home'), __('Dashboard'), ['']) !!}</li>
            <li class="breadcrumb-item">{{ __('Plans') }}</li>
        </ul>
    </div>
@endsection
@section('content')
    @hasrole('Super Admin')
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
    @endhasrole
    @hasrole('Admin')
        @section('breadcrumb')
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item">{{ __('Plans') }}</li>
            </ul>
        @endsection
        <div class="card-body">
            <section id="price" class="row">
                <div class="container">
                    <div class="row">
                        @foreach ($plans as $plan)
                            @if ($plan->active_status == 1)
                                @if ($plan->id == 1)
                                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                        <div class="card custom-price-card price-2 bg-primary wow animate__fadeInUp plan-style"
                                            data-wow-delay="0.4s">
                                            <div class="custom-card-body">
                                                <span class="custom-price-badge">{{ Str::upper($plan->name) }}</span>
                                                <span class="mb-4 text-white f-w-600 custom-p-price">
                                                    {{ $currency_symbol . '' . $plan->price }}
                                                    <small
                                                        class="text-sm">/{{ $plan->duration . ' ' . $plan->durationtype }}</small>
                                                </span>
                                                <p class="mb-0 text-white">
                                                    {{ $plan->description }}
                                                </p>
                                                <ul class="custom-list-unstyled">
                                                    <li>
                                                        <span class="custom-theme-avtar">
                                                            <i class="text-primary ti ti-circle-plus"></i>
                                                        </span>
                                                        {{ $plan->max_users . ' ' . __('Users') }}
                                                    </li>
                                                    <li>
                                                        <span class="custom-theme-avtar">
                                                            <i class="text-primary ti ti-circle-plus"></i>
                                                        </span>
                                                        {{ $plan->max_roles . ' ' . __('Roles') }}
                                                    </li>
                                                    <li>
                                                        <span class="custom-theme-avtar">
                                                            <i class="text-primary ti ti-circle-plus"></i>
                                                        </span>
                                                        {{ $plan->max_form . ' ' . __('Forms') }}
                                                    </li>
                                                    <li>
                                                        <span class="custom-theme-avtar">
                                                            <i class="text-primary ti ti-circle-plus"></i>
                                                        </span>
                                                        {{ $plan->max_booking . ' ' . __('Bookings') }}
                                                    </li>
                                                    <li>
                                                        <span class="custom-theme-avtar">
                                                            <i class="text-primary ti ti-circle-plus"></i>
                                                        </span>
                                                        {{ $plan->max_documents . ' ' . __('Documents') }}
                                                    </li>
                                                    <li>
                                                        <span class="custom-theme-avtar">
                                                            <i class="text-primary ti ti-circle-plus"></i>
                                                        </span>
                                                        {{ $plan->max_polls . ' ' . __('Polls') }}
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if ($plan->id !== 1)
                                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                        <div class="card custom-price-card price-1 wow animate__fadeInUp plan-style"
                                            data-wow-delay="0.4s">
                                            <div class="custom-card-body">
                                                <span
                                                    class="custom-price-badge bg-primary">{{ Str::upper($plan->name) }}</span>
                                                <span class="mb-4 f-w-600 custom-p-price ">
                                                    {{ $currency_symbol . '' . $plan->price }}
                                                    <small
                                                        class="text-sm">/{{ $plan->duration . ' ' . $plan->durationtype }}</small>
                                                </span>
                                                <p class="mb-0 ">
                                                    {{ $plan->description }}
                                                </p>
                                                <ul class="custom-list-unstyled-price-1">
                                                    <li>
                                                        <span class="custom-theme-avtar">
                                                            <i class="text-primary ti ti-circle-plus"></i>
                                                        </span>
                                                        {{ $plan->max_users . ' ' . __('Users') }}
                                                    </li>
                                                    <li>
                                                        <span class="custom-theme-avtar">
                                                            <i class="text-primary ti ti-circle-plus"></i>
                                                        </span>
                                                        {{ $plan->max_roles . ' ' . __('Roles') }}
                                                    </li>
                                                    <li>
                                                        <span class="custom-theme-avtar">
                                                            <i class="text-primary ti ti-circle-plus"></i>
                                                        </span>
                                                        {{ $plan->max_form . ' ' . __('Forms') }}
                                                    </li>
                                                    <li>
                                                        <span class="custom-theme-avtar">
                                                            <i class="text-primary ti ti-circle-plus"></i>
                                                        </span>
                                                        {{ $plan->max_booking . ' ' . __('Bookings') }}
                                                    </li>
                                                    <li>
                                                        <span class="custom-theme-avtar">
                                                            <i class="text-primary ti ti-circle-plus"></i>
                                                        </span>
                                                        {{ $plan->max_documents . ' ' . __('Documents') }}
                                                    </li>
                                                    <li>
                                                        <span class="custom-theme-avtar">
                                                            <i class="text-primary ti ti-circle-plus"></i>
                                                        </span>
                                                        {{ $plan->max_polls . ' ' . __('Polls') }}
                                                    </li>
                                                </ul>
                                            </div>
                                            <br>
                                            <div class="text-center d-grid">
                                                @if ($plan->id != 1)
                                                    <div class="pricing-cta">
                                                        @if ($plan->id == $user->plan_id && !empty($user->plan_expired_date))
                                                            <a href="javascript:void(0)" data-id="{{ $plan->id }}"
                                                                class="mb-3 btn btn-primary d-flex justify-content-center align-items-center mx-sm-5"
                                                                data-amount="{{ $plan->price }}">{{ __('Expire at') }}
                                                                {{ Carbon::parse($user->plan_expired_date)->format('d/m/Y') }}</a>
                                                        @else
                                                            <a href="{{ route('payment', \Illuminate\Support\Facades\Crypt::encrypt($plan->id)) }}"
                                                                class="mb-3 btn btn-primary d-flex justify-content-center align-items-center mx-sm-5">{{ __('Buy Plan') }}
                                                                <i class="ti ti-chevron-right ms-2"></i></a>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    </div>
            </section>
        </div>
    @endhasrole
@endsection
@hasrole('Super Admin')
    @push('style')
        @include('layouts.includes.datatable-css')
    @endpush
    @push('script')
        @include('layouts.includes.datatable-js')
        {{ $dataTable->scripts() }}
    @endpush
@endhasrole
