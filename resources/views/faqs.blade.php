@extends('layouts.main-landing')
@section('title', __('FAQs'))
@section('content')
    <section class="blog-page-banner"
        style="background-image: url({{ Storage::url(Utility::keysettings('background_image',1)) }});" width="100% "
        height="100%">
        <div class="container">
            <div class="common-banner-content">
                <div class="section-title">
                    <h2>{{ __('Faqs') }}</h2>
                </div>
                <ul class="back-cat-btn d-flex align-items-center justify-content-center">
                    <li><a href="{{ route('landingpage') }}">{{ __('Home') }}</a>
                        <span>/</span>
                    </li>
                    <li><a href="javascript:void(0)">{{ __('Faqs') }}</a></li>
                </ul>
            </div>
        </div>
    </section>
    <section class="home-faqs-sec pt pb">
        <div class="container">
            <div class="faqs-right-div">
                @foreach ($faqs as $faq)
                    <div class="set has-children">
                        <a href="javascript:;" class="nav-label">
                            <span>{{ $faq->question }}</span>
                        </a>
                        <div class="nav-list">
                            <p>{!! $faq->answer !!}</p>
                        </div>
                    </div>
                @endforeach
                <img src="{{ asset('vendor/landing-page2/image/test-bg.png') }}" alt="bacground-image"
                    class="faqs-bg">
            </div>
        </div>
    </section>
@endsection
