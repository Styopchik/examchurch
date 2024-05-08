@extends('layouts.main')
@section('title', __('Submitted Booking'))
@section('breadcrumb')
    <div class="col-md-12">
        <div class="page-header-title">
            <h4 class="m-b-10">{{ __('Submitted Bookings of ' . ' ' . $booking->business_name) }}</h4>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">{!! Html::link(route('home'), __('Dashboard'), ['']) !!}</li>
            <li class="breadcrumb-item">{{ __('Submitted Bookings of ' . ' ' . $booking->business_name) }}</li>
        </ul>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="main-content">
            <section class="section">
                @if (!empty($bookingValue->Booking->business_logo))
                    <div class="text-center gallery gallery-md">
                        <img id="app-dark-logo" class="float-none gallery-item"
                            src="{{ Storage::exists($bookingValue->Booking->business_logo)
                                ? Storage::url($bookingValue->Booking->business_logo)
                                : Storage::url('not-exists-data-images/78x78.png') }}">
                    </div>
                @endif
                <h2 class="text-center">{{ $booking->title }}</h2>
                <div class="section-body filter">
                    <div class="row">
                        <div class="mt-4 col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-4 col-md-6 responsive-search">
                                            <div class="form-group d-flex justify-content-start">
                                                {{ Form::text('user', null, ['class' => 'form-control mr-1 ', 'placeholder' => __('Search here'), 'data-kt-ecommerce-category-filter' => 'search']) }}
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 responsive-search">
                                            <div class="form-group row d-flex justify-content-start">
                                                {{ Form::text('duration', null, ['class' => 'form-control mr-1 created_at', 'placeholder' => __('Select Date Range'), 'id' => 'pc-daterangepicker-1']) }}
                                                {!! Form::hidden('booking_id', $booking->id, ['id' => 'booking_id']) !!}
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 btn-responsive-search">
                                            {{ Form::button(__('Filter'), ['class' => 'btn btn-primary btn-lg add_filter button-left']) }}
                                            {{ Form::button(__('Clear Filter'), ['class' => 'btn btn-secondary btn-lg clear_filter']) }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mt-5 col-xl-12">
                                            <div class="table-responsive">
                                                {{ $dataTable->table(['width' => '100%']) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
@push('style')
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/flatpickr.min.css') }}">
    @include('layouts.includes.datatable-css')
@endpush
@push('script')
    <script src="{{ asset('assets/js/plugins/flatpickr.min.js') }}"></script>
    <script src="{{ asset('vendor/apex-chart/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>
    @include('layouts.includes.datatable-js')
    {{ $dataTable->scripts() }}
    <script>
        function copyToClipboard(element) {
            console.log(element);
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val($(element).data('url')).select();
            document.execCommand("copy");
            $temp.remove();
            showToStr('Great!', '{{ __('Copied.') }}', 'success');
        }
        document.querySelector("#pc-daterangepicker-1").flatpickr({
            mode: "range"
        });
    </script>
@endpush
