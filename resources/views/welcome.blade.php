@extends('layouts.main-landing')
@section('title', __('Home'))
@section('content')
    @if (Utility::keysettings('apps_setting_enable', 1) == 'on')
        <section class="home-banner-sec">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-12">
                        <div class="banner-image">
                            <img src="{{ Utility::keysettings('apps_image', 1) ? Storage::url(Utility::keysettings('apps_image', 1)) : asset('vendor/landing-page2/image/slider-main-image.png') }}"
                                alt="home-banner-image" width="100% " height="100%">
                        </div>
                    </div>
                </div>
            </div>
            <img src="{{ asset('vendor/landing-page2/image/slider-image.png') }}" alt="background-image"
                class="home-bg-image">
            <img src="{{ asset('vendor/landing-page2/image/bacground-image.png') }}" alt="background-image"
                class="bg-fir-img">
            <img src="{{ asset('vendor/landing-page2/image/bacground-image-2.png') }}" alt="bacground-image"
                class="bg-sec-img">
            <img src="{{ asset('vendor/landing-page2/image/slider-sec-image.png') }}" alt="bacground-image"
                class="bg-the-img">
        </section>
        <section class="admin-saas-sec pt pb">
            <div class="container">
                <div class="text-center section-title">
                    <h2>{{ Utility::keysettings('apps_name', 1) ? Utility::keysettings('apps_name', 1) : __('Prime Laravel') }}<b>{{ Utility::keysettings('apps_bold_name', 1) ? Utility::keysettings('apps_bold_name', 1) : __('Form Builder') }}</b>
                    </h2>
                </div>
                <div class="section-content">
                    <p>{{ Utility::keysettings('app_detail', 1) ? Utility::keysettings('app_detail', 1) : __('Prime Laravel Form Builder is software for creating automated systems, you can create your own forms without writing a line of code. you have only to use the Drag & Drop to build your form and start using it.') }}
                    </p>
                </div>
            </div>
        </section>
        @if (isset($appsMultipleImageSettings))
            <section class="client-logo-section ">
                <img src="{{ asset('vendor/landing-page2/image/client-logo-bg1.png') }}" alt="client-bg" class="client-bg"
                    loading="lazy">
                <img src="{{ asset('vendor/landing-page2/image/client-logo-bg2.png') }}" alt="client-bg" class="client-bg2"
                    loading="lazy">
                <div class="container">
                    <div class="client-logo-wrap">
                        <div class="client-logo-slider slick-slider">
                            @foreach ($appsMultipleImageSettings as $appsMultipleImageSetting)
                                <div class="client-logo-iteam">
                                    <a href="javascript:void(0);">
                                        @if (Storage::exists(Storage::url($appsMultipleImageSetting->apps_multiple_image)))
                                            <img src="{{ Storage::url($appsMultipleImageSetting->apps_multiple_image) }}"
                                                alt="client-logo" width="100%" height="100%">
                                        @else
                                            <img src="{{ Storage::url('seeder-images/1.png') }}" alt="">
                                        @endif
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @endif
    @endif

    @if (Utility::keysettings('feature_setting_enable', 1) == 'on')
        @if (isset($features))
            <section class="features-sec pt pb" id="features">
                <div class="container">
                    <div class="text-center section-title">
                        <h2>{{ Utility::keysettings('feature_name', 1) ? Utility::keysettings('feature_name', 1) : 'Stunning with' }}
                            <b>{{ Utility::keysettings('feature_bold_name', 1) ? Utility::keysettings('feature_bold_name', 1) : 'lots of features' }}</b>
                        </h2>
                    </div>
                    <div class="text-center feature-sec-content">
                        <p>{{ Utility::keysettings('feature_detail', 1)
                            ? Utility::keysettings('feature_detail', 1)
                            : "Optimize your manufacturing business with Digitize, offering a seamless user interface for
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    streamlined operations, one convenient platform." }}
                        </p>
                    </div>
                    <div class="features-card-slide">
                        @foreach ($features as $key => $feature_value)
                            <div class="features-card">
                                <div class="features-card-inner">
                                    <div class="features-card-image">
                                        <a href="javascript:void(0);">
                                            <img src="{{ Storage::url($feature_value->feature_image) }}"
                                                alt="home-banner-image" width="60" height="60">
                                        </a>
                                    </div>
                                    <div class="features-card-content">
                                        <div class="features-top-content">
                                            <h3>
                                                <a href="javascript:void(0);">{{ isset($feature_value) ? $feature_value->feature_name : 'Warehouse Powerful' }}<br><b>
                                                        {{ isset($feature_value) ? $feature_value->feature_bold_name : 'Reporting Tools' }}</b></a>
                                            </h3>
                                        </div>
                                        <div class="features-bottom-content">
                                            <p>{{ isset($feature_value) ? $feature_value->feature_detail : 'The capability to clean, transform, and manipulate data to make it suitable for reporting and analysis.' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <img src="{{ asset('vendor/landing-page2/image/features-bg-image.png') }}" alt="background-image"
                        class="features-bg">
                </div>
            </section>
        @endif
    @endif

    @if (Utility::keysettings('menu_setting_section1_enable', 1) == 'on')
        <section class="apex-chart-sec" id="menu_section_1">
            <img src="{{ asset('vendor/landing-page2/image/features-bg-2.png') }}" alt="background-image"
                class="features-sec-bg">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 col-12">
                        <div class="chart-left-side">
                            <img src="{{ asset('vendor/landing-page2/image/blue.png') }}" alt=""
                                class="blue-bg left-blue">
                            <img src="{{ asset('vendor/landing-page2/image/purple.png') }}" alt=""
                                class="purple-bg left-purple">
                            <img src="{{ asset('vendor/landing-page2/image/yellow-squre.png') }}" alt=""
                                class="yellow-bg left-yellow">
                            <img src="{{ Utility::keysettings('menu_image_section1', 1)
                                ? Storage::url(Utility::keysettings('menu_image_section1', 1))
                                : asset('vendor/landing-page2/image/apex-chart-img.png') }}"
                                alt="chart-image" width="100% " height="100%">
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="chart-right-side">
                            <h2>
                                @if (Utility::keysettings('menu_name_section1', 1))
                                    {{ Utility::keysettings('menu_name_section1', 1) }}
                                @else
                                    {{ __('All in one place') }} <b> {{ __('CRM system') }} </b>
                                    {{ __('with') }}
                                @endif
                                <b> {{ Utility::keysettings('menu_bold_name_section1', 1) }} </b>
                            </h2>
                            <p>
                                {{ Utility::keysettings('menu_detail_section1', 1)
                                    ? Utility::keysettings('menu_detail_section1', 1)
                                    : __('ApexCharts is a modern charting library that helps developers to create beautiful and
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        interactive visualizations for web pages with a simple API, while React-ApexCharts is
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ApexChart’s React integration that allows us to use ApexCharts in our applications.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if (Utility::keysettings('menu_setting_section2_enable', 1) == 'on')
        <section class="support-system-sec pt pb" id="menu_section_2">
            <div class="container">
                <div class="row align-items-center ">
                    <div class="col-md-6 col-12">
                        <div class="chart-left-side">
                            <img src="{{ asset('vendor/landing-page2/image/blue.png') }}" alt=""
                                class="blue-small-round blue-bg">
                            <img src="{{ asset('vendor/landing-page2/image/purple.png') }}" alt=""
                                class="purple-bg left-purple">
                            <img src="{{ asset('vendor/landing-page2/image/yellow-squre.png') }}" alt=""
                                class="yellow-bg section2-yellow">
                            <img src="{{ Utility::keysettings('menu_image_section2', 1)
                                ? Storage::url(Utility::keysettings('menu_image_section2', 1))
                                : asset('vendor/landing-page2/image/apex-chart-img.png') }}"
                                alt="chart-image" width="100% " height="100%">
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="chart-right-side">
                            <h2>
                                @if (Utility::keysettings('menu_name_section2', 1))
                                    {{ Utility::keysettings('menu_name_section2', 1) }}
                                @else
                                    {{ __('All in one place CRM system with') }} <b> {{ __('Support System') }}
                                    </b>
                                @endif
                                <b> {{ Utility::keysettings('menu_bold_name_section2', 1) }} </b>
                            </h2>
                            <p>
                                {{ Utility::keysettings('menu_detail_section2', 1)
                                    ? Utility::keysettings('menu_detail_section2', 1)
                                    : __('A decision support system (DSS) is a computer program application used to improve a companys decision-making capabilities. It analyzes large amounts of data and presents an
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           organization with the best possible options available.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if (Utility::keysettings('menu_setting_section3_enable', 1) == 'on')
        <section class="apex-chart-sec" id="menu_section_3">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 col-12">
                        <div class="chart-left-side">
                            <img src="{{ asset('vendor/landing-page2/image/blue.png') }}" alt=""
                                class="blue-bg section3-blue">
                            <img src="{{ asset('vendor/landing-page2/image/purple.png') }}" alt=""
                                class="purple-bg section3-purple">
                            <img src="{{ asset('vendor/landing-page2/image/yellow-squre.png') }}" alt=""
                                class="yellow-bg section3-yellow">
                            <img src="{{ Utility::keysettings('menu_image_section3', 1)
                                ? Storage::url(Utility::keysettings('menu_image_section3', 1))
                                : asset('vendor/landing-page2/image/apex-chart-img.png') }}"
                                alt="chart-image" width="100% " height="100%">
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="chart-right-side">
                            <h2>
                                @if (Utility::keysettings('menu_name_section3', 1))
                                    {{ Utility::keysettings('menu_name_section3', 1) }}
                                @else
                                    {{ __('Empowering with Streamlined') }} <b> {{ __('Manufacturers') }} </b>
                                @endif
                                <b> {{ Utility::keysettings('menu_bold_name_section3', 1) }} </b>
                            </h2>
                            <p>
                                {{ Utility::keysettings('menu_detail_section3', 1)
                                    ? Utility::keysettings('menu_detail_section3', 1)
                                    : __('Digitize SAAS software is a game-changing solution designed exclusively for manufacturers, revolutionizing their operations and driving digital transformation. With
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              its advanced features and cutting-edge technology,') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if (Utility::keysettings('business_growth_setting_enable', 1) == 'on')
        <section class="video-play-sec pt pb" id="business_growth">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-12">
                        <div class="video-wrapper-div">
                            <div class="video-image">
                                @if (!empty(Utility::keysettings('business_growth_video', 1)))
                                    <video id="videoPlayer" controls width="100%" height="100%"
                                        poster="{{ Storage::url(Utility::keysettings('business_growth_front_image', 1)) }}"
                                        data-setup="{}">
                                        <source src="{{ Storage::url(Utility::keysettings('business_growth_video', 1)) }}"
                                            type='video/mp4' />
                                        <source src="{{ Storage::url(Utility::keysettings('business_growth_video', 1)) }}"
                                            type="video/ogg">
                                    </video>
                                @else
                                    <img src="{{ asset('vendor/landing-page2/image/video-image.png') }}" alt="video"
                                        width="100%" height="100%">
                                @endif
                            </div>
                            <a href="javascript:void(0);" class="play-btn" id="playButton">
                                <svg xmlns="http://www.w3.org/2000/svg" width="123" height="123"
                                    viewBox="0 0 123 123" fill="none">
                                    <path
                                        d="M90.3519 110.096C81.8393 115.252 71.8538 118.221 61.1745 118.221C30.0286 118.221 4.7793 92.9717 4.7793 61.8255C4.7793 30.6791 30.0286 5.43027 61.1745 5.43027C92.3207 5.43027 117.57 30.6791 117.57 61.8255C117.57 73.4073 113.999 84.1735 108.011 93.1296"
                                        stroke="#645BE1" stroke-width="9.55851" stroke-miterlimit="10"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path
                                        d="M53.5816 80.2225L77.4064 66.4197C80.9328 64.3768 80.9328 59.2738 77.4064 57.2309L53.5816 43.4282C50.0528 41.3841 45.6406 43.9369 45.6406 48.0225V75.6282C45.6406 79.7139 50.0528 82.2668 53.5816 80.2225Z"
                                        stroke="#645BE1" stroke-width="9.55851" stroke-miterlimit="10"
                                        stroke-linejoin="round" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <img src="{{ asset('vendor/landing-page2/image/video-bg.png') }}" alt="background-image" class="video-bg">
            <img src="{{ asset('vendor/landing-page2/image/video-bg-2.png') }}" alt="background-image"
                class="video-bg-sec">
        </section>
        <section class="counter-sec pb">
            <div class="container">
                <div class="section-title">
                    <h2> {{ Utility::keysettings('business_growth_name', 1)
                        ? Utility::keysettings('business_growth_name', 1)
                        : __('Makes Quick') }}
                        <b>
                            {{ Utility::keysettings('business_growth_bold_name', 1)
                                ? Utility::keysettings('business_growth_bold_name', 1)
                                : __('Business Growth') }}
                        </b>
                    </h2>
                    <p>
                        {{ Utility::keysettings('business_growth_detail', 1)
                            ? Utility::keysettings('business_growth_detail', 1)
                            : __('Offer unique products, services, or solutions that stand out in the market. Innovation and
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    differentiation can attract customers and give you a competitive edge.') }}
                    </p>
                </div>
                <div class="main-counter-div">
                    <div class="row">
                        @if (isset($businessGrowthsViewSettings))
                            @foreach ($businessGrowthsViewSettings as $businessGrowthsViewSetting)
                                <div class="col-sm-4 col-12 ">
                                    <div class="text-center counter-iteam counter">
                                        <h3>
                                            <span class="count" data-target="2">
                                                {{ $businessGrowthsViewSetting->business_growth_view_amount }}
                                            </span>
                                        </h3>

                                        <span class="counter-content">
                                            {{ $businessGrowthsViewSetting->business_growth_view_name }}</span>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="advance-feature">
                <div class="advance-feature-slider">
                    @if (isset($businessGrowthsSettings))
                        @foreach ($businessGrowthsSettings as $businessGrowthsSetting)
                            <div>
                                <div class="advance-feature-card">
                                    <div class="advance-card-inner d-flex align-items-center">
                                        <div class="advance-card-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25"
                                                viewBox="0 0 25 25" fill="none">
                                                <path
                                                    d="M12.5 0C5.59642 0 0 5.59642 0 12.5C0 19.4036 5.59642 25 12.5 25C19.4036 25 25 19.4036 25 12.5C25 5.59642 19.4036 0 12.5 0ZM18.6178 10.737L12.2264 16.5232C12.0697 16.6652 11.8871 16.7607 11.6958 16.8108C11.5309 16.8843 11.3539 16.9223 11.1763 16.9223C10.8601 16.9223 10.5434 16.8058 10.2958 16.5709L6.36058 12.8354C5.84833 12.3491 5.82742 11.5397 6.31367 11.0274C6.7995 10.5152 7.60917 10.494 8.12167 10.9803L11.2539 13.9535L16.9009 8.84058C17.4244 8.36658 18.2332 8.40658 18.7072 8.93025C19.1812 9.454 19.1412 10.2627 18.6178 10.737Z"
                                                    fill="#645BE1" />
                                            </svg>
                                        </div>
                                        <div class="advance-card-content">
                                            <p> {{ $businessGrowthsSetting->business_growth_title }} </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </section>
    @endif

    @if (Utility::keysettings('testimonial_setting_enable', 1) == 'on')
        <section class="testimonials-sec" id="testimonials">
            <div class="container">
                <div class="section-title">
                    <h2> {{ Utility::keysettings('testimonial_name', 1) ? Utility::keysettings('testimonial_name', 1) : __('Simple, Flexible') }}
                        <b> {{ Utility::keysettings('testimonial_bold_name', 1) ? Utility::keysettings('testimonial_bold_name', 1) : __('Our customer say') }}
                        </b>
                    </h2>
                    <p>
                        {{ Utility::keysettings('testimonial_detail', 1)
                            ? Utility::keysettings('testimonial_detail', 1)
                            : __(
                                'Its similar to case studies, but this format allows the company to tell the customers story from their own perspective.',
                            ) }}
                    </p>
                </div>
                <div class="testimonial-slider">
                    @if (isset($testimonials))
                        @foreach ($testimonials as $testimonial)
                            <div class="testimonial-card">
                                <div class="testimonial-card-inner">
                                    <div class="testimonial-card-content">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="42" height="37"
                                            viewBox="0 0 42 37" fill="none">
                                            <path
                                                d="M2.10973 -2.722e-06L14.9411 -4.78488e-07C16.1061 -2.74801e-07 17.0515 0.945469 17.0515 2.11042L17.0515 14.9418C17.0515 16.1068 16.1061 17.0522 14.9411 17.0522L8.79977 17.0522C8.87997 20.412 9.66082 23.1007 11.1381 25.1225C12.3031 26.7179 14.0673 28.0391 16.4268 29.0816C17.5116 29.5586 17.9801 30.8417 17.4736 31.9139L15.9541 35.1217C15.4645 36.1515 14.2531 36.6032 13.2063 36.1515C10.4121 34.9444 8.05264 33.4164 6.12797 31.5593C3.78114 29.2927 2.17304 26.7348 1.30355 23.8815C0.434012 21.0282 -0.000693456 17.1366 -0.000692593 12.1982L-0.000690829 2.11042C-0.000690626 0.945508 0.944822 -2.92568e-06 2.10973 -2.722e-06Z"
                                                fill="black" />
                                            <path
                                                d="M36.6786 36.1431C33.9182 34.9402 31.5714 33.4123 29.634 31.5593C27.2661 29.2927 25.6496 26.7433 24.7801 23.9111C23.9106 21.0789 23.4759 17.1746 23.4759 12.1982L23.4759 2.11042C23.4759 0.945466 24.4213 -2.92569e-06 25.5863 -2.722e-06L38.4177 -4.78488e-07C39.5826 -2.74801e-07 40.5281 0.945469 40.5281 2.11042L40.5281 14.9418C40.5281 16.1068 39.5826 17.0522 38.4177 17.0522L32.2763 17.0522C32.3565 20.4121 33.1374 23.1007 34.6147 25.1225C35.7796 26.7179 37.5439 28.0391 39.9034 29.0816C40.9882 29.5586 41.4567 30.8417 40.9502 31.9139L39.4349 35.1133C38.9452 36.1431 37.7254 36.599 36.6786 36.1431Z"
                                                fill="black" />
                                        </svg>
                                        <p>{{ $testimonial->desc }}</p>
                                        <div class="client-info">
                                            <div class="client-img">
                                                <img src="{{ Storage::url($testimonial->image) }}" alt="client-image"
                                                    width="100%" height="100%">
                                            </div>
                                            <div class="client-name">
                                                <a href="javascript:void(0);">{{ $testimonial->name }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <img src="{{ asset('vendor/landing-page2/image/test-bg.png') }}" alt="testimonial-bg"
                    class="testimonial-bg">
                <img src="{{ asset('vendor/landing-page2/image/test-bg-2.png') }}" alt="testimonial-bg"
                    class="testimonial-bg-2">
                <img src="{{ asset('vendor/landing-page2/image/test-bg-3.png') }}" alt="testimonial-bg"
                    class="testimonial-bg-3">
            </div>
        </section>
    @endif

    @if (Utility::keysettings('plan_setting_enable', 1) == 'on')
        <section class="pricing-plans-sec pt pb" id="plans">
            <div class="container">
                <div class="section-title">
                    <h2> {{ Utility::keysettings('plan_name', 1) ? Utility::keysettings('plan_name', 1) : __('Simple, Flexible') }}
                        <b> {{ Utility::keysettings('plan_bold_name', 1) ? Utility::keysettings('plan_bold_name', 1) : __('Pricing') }}
                        </b>
                    </h2>
                    <p>
                        {{ Utility::keysettings('plan_detail', 1)
                            ? Utility::keysettings('plan_detail', 1)
                            : __('The pricing structure is easy to comprehend, and all costs and fees are explicitly stated. There
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                are no hidden charges or surprise costs for customers.') }}
                    </p>
                </div>
                <div class="row">
                    @foreach ($plans as $key => $plan)
                        <div class="col-lg-4 col-md-6 col-12" style="padding-top: 10px !important;">
                            <div class="basic-plans">
                                <div class="basic-plans-top">
                                    <h3>
                                        {{ $plan->name }}
                                    </h3>
                                    <ul>
                                        <li class="d-flex align-items-center">
                                            <div class="plan-card-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25"
                                                    viewBox="0 0 25 25" fill="none">
                                                    <path
                                                        d="M12.5 0C5.59642 0 0 5.59642 0 12.5C0 19.4036 5.59642 25 12.5 25C19.4036 25 25 19.4036 25 12.5C25 5.59642 19.4036 0 12.5 0ZM18.6178 10.737L12.2264 16.5232C12.0697 16.6652 11.8871 16.7607 11.6958 16.8108C11.5309 16.8843 11.3539 16.9223 11.1763 16.9223C10.8601 16.9223 10.5434 16.8058 10.2958 16.5709L6.36058 12.8354C5.84833 12.3491 5.82742 11.5397 6.31367 11.0274C6.7995 10.5152 7.60917 10.494 8.12167 10.9803L11.2539 13.9535L16.9009 8.84058C17.4244 8.36658 18.2332 8.40658 18.7072 8.93025C19.1812 9.454 19.1412 10.2627 18.6178 10.737Z"
                                                        fill="#645BE1" />
                                                </svg>
                                            </div>
                                            <div class="plan-card-content">
                                                <p>{{ $plan->duration . ' ' . $plan->durationtype }}
                                                    {{ __('Duration') }}</p>
                                            </div>
                                        </li>
                                        <li class="d-flex align-items-center">
                                            <div class="plan-card-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25"
                                                    viewBox="0 0 25 25" fill="none">
                                                    <path
                                                        d="M12.5 0C5.59642 0 0 5.59642 0 12.5C0 19.4036 5.59642 25 12.5 25C19.4036 25 25 19.4036 25 12.5C25 5.59642 19.4036 0 12.5 0ZM18.6178 10.737L12.2264 16.5232C12.0697 16.6652 11.8871 16.7607 11.6958 16.8108C11.5309 16.8843 11.3539 16.9223 11.1763 16.9223C10.8601 16.9223 10.5434 16.8058 10.2958 16.5709L6.36058 12.8354C5.84833 12.3491 5.82742 11.5397 6.31367 11.0274C6.7995 10.5152 7.60917 10.494 8.12167 10.9803L11.2539 13.9535L16.9009 8.84058C17.4244 8.36658 18.2332 8.40658 18.7072 8.93025C19.1812 9.454 19.1412 10.2627 18.6178 10.737Z"
                                                        fill="#645BE1" />
                                                </svg>
                                            </div>
                                            <div class="plan-card-content">
                                                <p>{{ $plan->max_users . ' ' . __('Users') }}</p>
                                            </div>
                                        </li>
                                        <li class="d-flex align-items-center">
                                            <div class="plan-card-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25"
                                                    viewBox="0 0 25 25" fill="none">
                                                    <path
                                                        d="M12.5 0C5.59642 0 0 5.59642 0 12.5C0 19.4036 5.59642 25 12.5 25C19.4036 25 25 19.4036 25 12.5C25 5.59642 19.4036 0 12.5 0ZM18.6178 10.737L12.2264 16.5232C12.0697 16.6652 11.8871 16.7607 11.6958 16.8108C11.5309 16.8843 11.3539 16.9223 11.1763 16.9223C10.8601 16.9223 10.5434 16.8058 10.2958 16.5709L6.36058 12.8354C5.84833 12.3491 5.82742 11.5397 6.31367 11.0274C6.7995 10.5152 7.60917 10.494 8.12167 10.9803L11.2539 13.9535L16.9009 8.84058C17.4244 8.36658 18.2332 8.40658 18.7072 8.93025C19.1812 9.454 19.1412 10.2627 18.6178 10.737Z"
                                                        fill="#645BE1" />
                                                </svg>
                                            </div>
                                            <div class="plan-card-content">
                                                <p>{{ $plan->max_roles . ' ' . __('Roles') }}</p>
                                            </div>
                                        </li>
                                        <li class="d-flex align-items-center">
                                            <div class="plan-card-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25"
                                                    viewBox="0 0 25 25" fill="none">
                                                    <path
                                                        d="M12.5 0C5.59642 0 0 5.59642 0 12.5C0 19.4036 5.59642 25 12.5 25C19.4036 25 25 19.4036 25 12.5C25 5.59642 19.4036 0 12.5 0ZM18.6178 10.737L12.2264 16.5232C12.0697 16.6652 11.8871 16.7607 11.6958 16.8108C11.5309 16.8843 11.3539 16.9223 11.1763 16.9223C10.8601 16.9223 10.5434 16.8058 10.2958 16.5709L6.36058 12.8354C5.84833 12.3491 5.82742 11.5397 6.31367 11.0274C6.7995 10.5152 7.60917 10.494 8.12167 10.9803L11.2539 13.9535L16.9009 8.84058C17.4244 8.36658 18.2332 8.40658 18.7072 8.93025C19.1812 9.454 19.1412 10.2627 18.6178 10.737Z"
                                                        fill="#645BE1" />
                                                </svg>
                                            </div>
                                            <div class="plan-card-content">
                                                <p>{{ $plan->max_form . ' ' . __('Forms') }}</p>
                                            </div>
                                        </li>
                                        <li class="d-flex align-items-center">
                                            <div class="plan-card-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25"
                                                    viewBox="0 0 25 25" fill="none">
                                                    <path
                                                        d="M12.5 0C5.59642 0 0 5.59642 0 12.5C0 19.4036 5.59642 25 12.5 25C19.4036 25 25 19.4036 25 12.5C25 5.59642 19.4036 0 12.5 0ZM18.6178 10.737L12.2264 16.5232C12.0697 16.6652 11.8871 16.7607 11.6958 16.8108C11.5309 16.8843 11.3539 16.9223 11.1763 16.9223C10.8601 16.9223 10.5434 16.8058 10.2958 16.5709L6.36058 12.8354C5.84833 12.3491 5.82742 11.5397 6.31367 11.0274C6.7995 10.5152 7.60917 10.494 8.12167 10.9803L11.2539 13.9535L16.9009 8.84058C17.4244 8.36658 18.2332 8.40658 18.7072 8.93025C19.1812 9.454 19.1412 10.2627 18.6178 10.737Z"
                                                        fill="#645BE1" />
                                                </svg>
                                            </div>
                                            <div class="plan-card-content">
                                                <p>{{ $plan->max_booking . ' ' . __('Bookings') }}</p>
                                            </div>
                                        </li>
                                        <li class="d-flex align-items-center">
                                            <div class="plan-card-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25"
                                                    viewBox="0 0 25 25" fill="none">
                                                    <path
                                                        d="M12.5 0C5.59642 0 0 5.59642 0 12.5C0 19.4036 5.59642 25 12.5 25C19.4036 25 25 19.4036 25 12.5C25 5.59642 19.4036 0 12.5 0ZM18.6178 10.737L12.2264 16.5232C12.0697 16.6652 11.8871 16.7607 11.6958 16.8108C11.5309 16.8843 11.3539 16.9223 11.1763 16.9223C10.8601 16.9223 10.5434 16.8058 10.2958 16.5709L6.36058 12.8354C5.84833 12.3491 5.82742 11.5397 6.31367 11.0274C6.7995 10.5152 7.60917 10.494 8.12167 10.9803L11.2539 13.9535L16.9009 8.84058C17.4244 8.36658 18.2332 8.40658 18.7072 8.93025C19.1812 9.454 19.1412 10.2627 18.6178 10.737Z"
                                                        fill="#645BE1" />
                                                </svg>
                                            </div>
                                            <div class="plan-card-content">
                                                <p>{{ $plan->max_documents . ' ' . __('Documents') }}</p>
                                            </div>
                                        </li>
                                        <li class="d-flex align-items-center">
                                            <div class="plan-card-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25"
                                                    viewBox="0 0 25 25" fill="none">
                                                    <path
                                                        d="M12.5 0C5.59642 0 0 5.59642 0 12.5C0 19.4036 5.59642 25 12.5 25C19.4036 25 25 19.4036 25 12.5C25 5.59642 19.4036 0 12.5 0ZM18.6178 10.737L12.2264 16.5232C12.0697 16.6652 11.8871 16.7607 11.6958 16.8108C11.5309 16.8843 11.3539 16.9223 11.1763 16.9223C10.8601 16.9223 10.5434 16.8058 10.2958 16.5709L6.36058 12.8354C5.84833 12.3491 5.82742 11.5397 6.31367 11.0274C6.7995 10.5152 7.60917 10.494 8.12167 10.9803L11.2539 13.9535L16.9009 8.84058C17.4244 8.36658 18.2332 8.40658 18.7072 8.93025C19.1812 9.454 19.1412 10.2627 18.6178 10.737Z"
                                                        fill="#645BE1" />
                                                </svg>
                                            </div>
                                            <div class="plan-card-content">
                                                <p>{{ $plan->max_polls . ' ' . __('Polls') }}</p>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="basic-plans-bottom justify-content-between d-flex align-items-center">
                                    <div class="basic-plans-price d-flex">
                                        <div class="basic-price-left">
                                            <p>{{ __('Billed') }}</p>
                                            <ins>{{ Utility::amountFormat($plan->price) }} </ins>
                                        </div>
                                        <div class="basic-price-right">

                                        </div>
                                    </div>
                                    <div class="basic-plans-button d-flex">
                                        @if ($plan->id == 1)
                                            <a href="{{ route('requestuser.create', Crypt::encrypt(['plan_id' => $plan->id])) }}"
                                                class="mt-2 subscribe_plan btn btn-primary btn-block"
                                                data-id="{{ $plan->id }}"
                                                data-amount="{{ $plan->price }}">{{ __('Free') }}
                                                <i class="ti ti-chevron-right ms-2"></i></a>
                                        @elseif ($plan->id != 1)
                                            <a href="{{ route('requestuser.create', Crypt::encrypt(['plan_id' => $plan->id])) }}"
                                                class="mt-2 subscribe_plan btn btn-primary btn-block"
                                                data-id="{{ $plan->id }}"
                                                data-amount="{{ $plan->price }}">{{ __('Subscribe') }}
                                                <i class="ti ti-chevron-right ms-2"></i></a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if (Utility::keysettings('contactus_setting_enable', 1) == 'on')
                    <div class="custom-pricing-div d-flex align-items-center justify-content-between">
                        <div class="custom-pricing-left">
                            <h4>
                                <b>{{ Utility::keysettings('contactus_name', 1) ? Utility::keysettings('contactus_name', 1) : __('Enterprise') }}</b>
                                {{ Utility::keysettings('contactus_bold_name', 1) ? Utility::keysettings('contactus_bold_name', 1) : __('Custom pricing') }}
                            </h4>
                        </div>
                        <div class="custom-pricing-center">
                            <p>
                                {{ Utility::keysettings('contactus_detail', 1)
                                    ? Utility::keysettings('contactus_detail', 1)
                                    : __('Offering tiered pricing options based on different levels of features or services allows
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    customers.') }}
                            </p>
                        </div>
                        <div class="custom-pricing-right">
                            <a href="{{ route('contactus') }}" class="btn">{{ __('Contact us') }}</a>
                        </div>
                    </div>
                @endif
                <img src="{{ asset('vendor/landing-page2/image/features-bg-image.png') }}" alt="bacground-image"
                    class="pricing-bg">
            </div>
        </section>
    @endif

    @if (Utility::keysettings('faq_setting_enable', 1) == 'on')
        <section class="home-faqs-sec" id="faqs">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="faqs-left-div">
                            <h2>
                                {{ Utility::keysettings('faq_name', 1) ? Utility::keysettings('faq_name', 1) : 'Frequently asked questions' }}
                            </h2>
                            <a href="{{ route('faqs.pages') }}" class="btn">View All FAQs
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="11"
                                    viewBox="0 0 17 11" fill="none">
                                    <path
                                        d="M15.8434 6.63069L12.4502 10.6141C12.2632 10.8337 12.0181 10.9434 11.773 10.9434C11.5279 10.9434 11.2828 10.8337 11.0958 10.6141C10.7218 10.175 10.7218 9.46319 11.0958 9.02412L12.8541 6.96L1.75847 6.96C1.22956 6.96 0.800781 6.45664 0.800781 5.83572C0.800781 5.21479 1.22956 4.71143 1.75847 4.71143L12.8541 4.71143L11.0958 2.64731C10.7218 2.20825 10.7218 1.4964 11.0958 1.05733C11.4698 0.61826 12.0762 0.61826 12.4502 1.05733L15.8434 5.04074C16.2174 5.47977 16.2174 6.19166 15.8434 6.63069Z"
                                        fill="white" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
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
                </div>
            </div>
        </section>
    @endif

    @if (Utility::keysettings('blog_setting_enable', 1) == 'on')
        <section class="home-article-sec pt pb" id="blogs">
            <div class="container">
                <div class="section-title">
                    <h2> {{ Utility::keysettings('blog_name', 1) ? Utility::keysettings('blog_name', 1) : 'What’s New?' }}
                    </h2>
                    <p> {{ Utility::keysettings('blog_detail', 1)
                        ? Utility::keysettings('blog_detail', 1)
                        : 'Optimize your manufacturing business with Digitize, offering a seamless user interface for
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    streamlined operations, one convenient platform.' }}
                    </p>
                </div>
                <div class="article-slider">
                    @foreach ($blogs as $blog)
                        <div class="article-card">
                            <div class="article-card-inner">
                                <div class="article-card-image">
                                    <a href="{{ route('view.blog', $blog->slug) }}">
                                        <img src="{{ isset($blog->images) ? Storage::url($blog->images) : asset('vendor/landing-page2/image/blog-card-img.png') }}"
                                            alt="blog-card-image">
                                    </a>
                                </div>
                                <div class="article-card-content">
                                    <div class="author-info d-flex align-items-center justify-content-between">
                                        <div class="date d-flex align-items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23"
                                                viewBox="0 0 23 23" fill="none">
                                                <path
                                                    d="M18.0527 1.86077H16.6306V1.00753C16.6306 0.536546 16.2484 0.154297 15.7774 0.154297C15.3064 0.154297 14.9242 0.536546 14.9242 1.00753V1.86077H7.52946V1.00753C7.52946 0.536546 7.14721 0.154297 6.67623 0.154297C6.20524 0.154297 5.82299 0.536546 5.82299 1.00753V1.86077H4.40094C1.65011 1.86077 0.134766 3.37611 0.134766 6.12694V18.0722C0.134766 20.823 1.65011 22.3384 4.40094 22.3384H18.0527C20.8035 22.3384 22.3189 20.823 22.3189 18.0722V6.12694C22.3189 3.37611 20.8035 1.86077 18.0527 1.86077ZM4.40094 3.56723H5.82299V4.42047C5.82299 4.89145 6.20524 5.2737 6.67623 5.2737C7.14721 5.2737 7.52946 4.89145 7.52946 4.42047V3.56723H14.9242V4.42047C14.9242 4.89145 15.3064 5.2737 15.7774 5.2737C16.2484 5.2737 16.6306 4.89145 16.6306 4.42047V3.56723H18.0527C19.8468 3.56723 20.6124 4.33287 20.6124 6.12694V6.98017H1.84123V6.12694C1.84123 4.33287 2.60687 3.56723 4.40094 3.56723ZM18.0527 20.6319H4.40094C2.60687 20.6319 1.84123 19.8663 1.84123 18.0722V8.68664H20.6124V18.0722C20.6124 19.8663 19.8468 20.6319 18.0527 20.6319Z"
                                                    fill="black" />
                                            </svg>
                                            <span>{{ App\Facades\UtilityFacades::dateTimeFormat($blog->created_at) }}</span>
                                        </div>
                                    </div>
                                    <h3>
                                        <a
                                            href="{{ route('view.blog', $blog->slug) }}">{{ isset($blog->title) ? $blog->title : __('Benefits of Multi-Tenancy in Laravel Dashboard') }}</a>
                                    </h3>
                                    <p>{{ isset($blog->short_description)
                                        ? html_entity_decode($blog->short_description)
                                        : __(
                                            'Exploring the advantages of implementing multi-tenancy, such as cost savings,scalability, easier maintenance, and improved security.',
                                        ) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <img src="{{ asset('vendor/landing-page2/image/features-bg-image.png') }}" alt="bacground-image"
                    class="article-bg">
            </div>
        </section>
    @endif

    @if (Utility::keysettings('start_view_setting_enable', 1) == 'on')
        <section class="contact-banner-sec pt pb" id="start_view">
            <div class="container">
                <div class="row contact-banner-row align-items-center">
                    <div class="col-md-6">
                        <div class="contact-banner-leftside">
                            <h2>
                                {{ Utility::keysettings('start_view_name', 1)
                                    ? Utility::keysettings('start_view_name', 1)
                                    : __('Start Using Prime Laravel Admin') }}
                            </h2>
                            <p>
                                {{ Utility::keysettings('start_view_detail', 1)
                                    ? Utility::keysettings('start_view_detail', 1)
                                    : __('Instead of forcing you to change how you write your code, the package by default
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            bootstraps tenancy automatically, in the background.') }}
                            </p>
                            <div class="contact-btn-wrapper d-flex align-items-center">
                                <a href="javascript:void(0);" class="white-btn"> {{ __('Get Started') }}
                                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="11"
                                        viewBox="0 0 17 11" fill="none">
                                        <path
                                            d="M15.728 6.42841L12.219 10.5478C12.0256 10.7749 11.7722 10.8884 11.5187 10.8884C11.2652 10.8884 11.0118 10.7749 10.8184 10.5478C10.4316 10.0938 10.4316 9.35761 10.8184 8.90355L12.6367 6.76896L1.16226 6.76896C0.615291 6.76896 0.171875 6.24841 0.171875 5.60629C0.171875 4.96417 0.615291 4.44362 1.16226 4.44362L12.6367 4.44362L10.8184 2.30903C10.4316 1.85497 10.4316 1.11882 10.8184 0.664763C11.2052 0.210704 11.8322 0.210704 12.219 0.664763L15.728 4.78417C16.1148 5.2382 16.1148 5.97438 15.728 6.42841Z"
                                            fill="#645BE1" />
                                    </svg>
                                </a>
                                <a href="{{ route('contactus') }}" class=" btn contact-btn">
                                    {{ Utility::keysettings('start_view_link_name', 1)
                                        ? Utility::keysettings('start_view_link_name', 1)
                                        : __('Contact us') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="demo-bannerimg">
                            <img src="{{ asset('vendor/landing-page2/image/bg-round.png') }}" alt=""
                                class="demo-bg-img">
                            <img src="{{ Utility::keysettings('start_view_image', 1) ? Storage::url(Utility::keysettings('start_view_image', 1)) : asset('vendor/landing-page2/image/contact-us-banner.png') }}"
                                alt="home-banner-image" width="100%" height="100%">
                        </div>
                    </div>
                </div>
                <img src="{{ asset('vendor/landing-page2/image/test-bg.png') }}" alt="bacground-image"
                    class="contact-bg">
            </div>
        </section>
    @endif
@endsection
