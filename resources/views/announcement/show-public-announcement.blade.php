@php
    $languages = \App\Facades\UtilityFacades::languages();
@endphp
@extends('layouts.main-landing')
@section('title', __('Show Announcement'))
@section('content')
    <section class="blog-page-banner"
        style="background-image: url({{ Utility::getsettings('background_image') ? Storage::url(Utility::getsettings('background_image')) : asset('vendor/landing-page2/image/blog-banner-image.png') }} );"
        width="100% " height="100%">
        <div class="container">
            <div class="common-banner-content">
                <div class="section-title">
                    <h2>{{ __('Show Announcement') }}</h2>
                </div>
                <ul class="back-cat-btn d-flex align-items-center justify-content-center">
                    <li><a href="{{ route('landingpage') }}">{{ __('Home') }}</a>
                        <span>/</span>
                    </li>
                    <li><a href="javascript:void(0);">{{ __('Show Announcement') }}</a></li>
                </ul>
            </div>
        </div>
    </section>
    <section class="home-faqs-sec pt pb ">
        <div class="container text-center">
            <h5 class="card-title mb-4">{{ $announcement->title }}</h5>
            <img class="announcement-img" src="{{ Storage::url($announcement->image) }}" alt="Card image cap">

            <hr>
            <p>{!! $announcement->description !!}</p>
        </div>
    </section>
@endsection
