@extends('layouts.main')
@section('title', __('Coupons'))
@section('breadcrumb')
    <div class="col-md-12">
        <div class="page-header-title">
            <h4 class="m-b-10">{{ __('Coupons') }}</h4>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">{!! Html::link(route('home'), __('Dashboard'), ['']) !!}</li>
            <li class="breadcrumb-item">{{ __('Coupons') }}</li>
        </ul>
        <div class="float-end">
            <div class="d-flex align-items-center">
                <a href="#" data-url="{{ route('coupon.mass.create') }}" data-bs-toggle="tooltip"
                    title="{{ __('Mass Create') }}" class="btn btn-sm btn-primary coupon_mass_create"
                    data-bs-placement="bottom">
                    <i class="ti ti-plus"></i>
                </a>
                <a href="#" data-url="{{ route('coupon.upload') }}" data-bs-toggle="tooltip"
                    data-bs-placement="bottom" title="{{ __('Upload') }}" class="mx-1 btn btn-sm btn-primary upload_csv">
                    <i class="ti ti-upload"></i>
                </a>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-3 col-md-6 col-sm-12">
            <div class="card comp-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20 text-muted">{{ __('Total Coupon') }}</h6>
                            <h3 class="text-primary">{{ $totalCoupon }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="text-white ti ti-discount bg-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-12">
            <div class="card comp-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20 text-muted">{{ __('Expired Coupon') }}</h6>
                            <h3 class="text-danger">{{ $expieredCoupon }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="text-white ti ti-user-exclamation bg-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-12">
            <div class="card comp-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20 text-muted">{{ __('Total Used Coupon') }}</h6>
                            <h3 class="text-success">{{ $totalUsedCoupon }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="text-white ti ti-user-check bg-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-12">
            <div class="card comp-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20 text-muted">{{ __('Total Discounted Amount') }}</h6>
                            <h3 class="text-warning">{{ Utility::amountFormat($totalUseAmount) }}
                            </h3>
                        </div>
                        <div class="col-auto">
                            <i class="text-white ti ti-currency-dollar bg-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
            $(document).on('click', '.upload_csv', function() {
                var action = $(this).data('url');
                var modal = $('#common_modal');
                $.get(action, function(response) {
                    modal.find('.modal-title').html('{{ __('Upload Coupon') }}');
                    modal.find('.body').html(response);
                    modal.modal('show');
                })
            });
            $(document).on('click', '.coupon_add', function() {
                var action = '{{ route('coupon.create') }}';
                var modal = $('#common_modal');
                $.get(action, function(response) {
                    modal.find('.modal-title').html('{{ __('Create Coupon') }}');
                    modal.find('.body').html(response);
                    modal.modal('show');
                    modal.find('.code').click(function() {
                        var type = $(this).val();
                        if (type == 'manual') {
                            $('#manual').removeClass('d-none');
                            $('#manual').addClass('d-block');
                            $('#auto').removeClass('d-block');
                            $('#auto').addClass('d-none');
                        } else {
                            $('#auto').removeClass('d-none');
                            $('#auto').addClass('d-block');
                            $('#manual').removeClass('d-block');
                            $('#manual').addClass('d-none');
                        }
                    });
                    modal.find('#code-generate').click(function() {
                        var length = 10;
                        var result = '';
                        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                        var charactersLength = characters.length;
                        for (var i = 0; i < length; i++) {
                            result += characters.charAt(Math.floor(Math.random() *
                                charactersLength));
                        }
                        modal.find('#auto-code').val(result);
                    });
                    var multipleCancelButton = new Choices('[data-trigger]', {
                        removeItemButton: true,
                    });
                })
            });
            $(document).on('click', '.coupon_edit', function() {
                var action = $(this).data('url');
                var modal = $('#common_modal');
                $.get(action, function(response) {
                    modal.find('.modal-title').html('{{ __('Edit Coupon') }}');
                    modal.find('.body').html(response);
                    modal.modal('show');
                    var multipleCancelButton = new Choices('[data-trigger]', {
                        removeItemButton: true,
                    });
                })
            });
            $(document).on('click', '.coupon_mass_create', function() {
                var action = $(this).data('url');
                var modal = $('#common_modal');
                $.get(action, function(response) {
                    modal.find('.modal-title').html('{{ __('Create Mass Coupon') }}');
                    modal.find('.body').html(response);
                    modal.modal('show');
                    var multipleCancelButton = new Choices('[data-trigger]', {
                        removeItemButton: true,
                    });
                })
            });
        });
    </script>
@endpush
