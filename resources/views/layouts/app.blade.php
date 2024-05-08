@php
    $lang = Utility::getActiveLanguage();
    \App::setLocale($lang);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ Utility::getsettings('rtl') == '1' || $lang == 'ar' ? 'rtl' : '' }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="title"
    content="{{ !empty(Utility::getsettings('meta_title'))
        ? Utility::getsettings('meta_title') :  Utility::getsettings('app_name')  }}">
<meta name="keywords"
    content="{{ !empty(Utility::getsettings('meta_keywords'))
        ? Utility::getsettings('meta_keywords')
        : 'Multi Users,Role & permission , Form & poll management , document Genrator , Booking system' }}">
<meta name="description"
    content="{{ !empty(Utility::getsettings('meta_description'))
        ? Utility::getsettings('meta_description')
        : 'Discover the efficiency of prime laravel Saas, a user-friendly web application by Quebix Apps.' }}">
<meta name="meta_image_logo" property="og:image"
    content="{{ !empty(Utility::getsettings('meta_image_logo'))
        ? Storage::url(Utility::getsettings('meta_image_logo'))
        : Storage::url('seeder-image/meta-image-logo.jpg') }}">
    @if (Utility::getsettings('seo_setting') == 'on')
        {!! app('seotools')->generate() !!}
    @endif
    <title>@yield('title') - {{ setting('app_name') }}</title>

    <link rel="manifest" href="{{ asset('/public/manifest.json') }}">
    <link rel="icon"
        href="{{ setting('favicon_logo') ? Storage::url('app-logo/app-favicon-logo.png') : asset('assets/images/app-favicon-logo.png') }}"
        type="image/png">
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/landing-page2/css/landingpage-2.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/landing-page2/css/landingpage2-responsive.css') }}">
</head>

<body>
    <!-- [ auth-signup ] start -->
    <div class="auth-wrapper auth-v3">
        <!--header start here-->
        <header class="site-header header-style-one">
            <div class="main-navigationbar">
                <div class="container">
                    <div class="navigation-row d-flex align-items-center ">
                        <nav class="menu-items-col d-flex align-items-center justify-content-between ">
                            <div class="logo-col">
                                <h1>
                                    <a href="#" tabindex="0">
                                        <img src="{{ Storage::url(setting('app_dark_logo')) ? Storage::url('app-logo/app-dark-logo.png') : asset('assets/images/app-dark-logo.png') }}"
                                            width="100%" height="100%" loading="lazy" />
                                    </a>
                                </h1>
                            </div>
                            <div class="menu-item-right-col d-flex align-items-center justify-content-between">
                                <div class="menu-left-col">
                                    <ul class="main-nav d-flex align-items-center">
                                        <li class="menus-lnk">
                                            <a href="{{ route('landingpage') }}"
                                                tabindex="0">{{ __('Home') }}</a>
                                        </li>
                                        @php
                                            $headerMainMenus = App\Models\HeaderSetting::get();
                                        @endphp
                                        @if (!empty($headerMainMenus))
                                            @foreach ($headerMainMenus as $headerMainMenu)
                                                <li class="menu-has-items">
                                                    @php
                                                        $page = App\Models\PageSetting::find($headerMainMenu->page_id);
                                                    @endphp
                                                    <a @if ($page->type == 'link') ?  href="{{ $page->page_url }}"  @else  href="{{ route('description.page', $headerMainMenu->slug) }}" @endif
                                                        tabindex="0">
                                                        {{ $page->title }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                                <div class="menu-right-col">
                                    <ul class=" d-flex align-items-center">
                                        <li class="switch-toggle" onclick="myFunction()">
                                            <a class="switch-sun d-none">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26"
                                                    viewBox="0 0 26 26" fill="none">
                                                    <path
                                                        d="M13 18C15.7614 18 18 15.7614 18 13C18 10.2386 15.7614 8 13 8C10.2386 8 8 10.2386 8 13C8 15.7614 10.2386 18 13 18Z"
                                                        stroke="black" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path d="M13 3V1" stroke="black" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M13 25V23" stroke="black" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M3 13H1" stroke="black" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M25 13H23" stroke="black" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M5.92977 20.07L4.50977 21.49" stroke="black"
                                                        stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path d="M21.4903 4.51001L20.0703 5.93001" stroke="black"
                                                        stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path d="M20.0703 20.07L21.4903 21.49" stroke="black"
                                                        stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path d="M4.50977 4.51001L5.92977 5.93001" stroke="black"
                                                        stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            </a>
                                            <a class="switch-moon">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="512"
                                                    viewBox="0 0 512 512" width="512">
                                                    <title />
                                                    <path
                                                        d="M152.62,126.77c0-33,4.85-66.35,17.23-94.77C87.54,67.83,32,151.89,32,247.38,32,375.85,136.15,480,264.62,480c95.49,0,179.55-55.54,215.38-137.85-28.42,12.38-61.8,17.23-94.77,17.23C256.76,359.38,152.62,255.24,152.62,126.77Z" />
                                                </svg>
                                            </a>
                                        </li>
                                        <li class="language-btn">
                                            <select class="nice-select"
                                                onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);"
                                                id="language">
                                                @foreach (Utility::languages() as $language)
                                                    <option class=""
                                                        @if ($lang == $language) selected @endif
                                                        value="{{ route('change.lang', $language) }}">
                                                        {{ Str::upper($language) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </li>
                                        @yield('auth-topbar')
                                        <li class="mobile-menu">
                                            <button class="mobile-menu-button" id="menu">
                                                <div class="one"></div>
                                                <div class="two"></div>
                                                <div class="three"></div>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
            <!-- Mobile menu start here -->
            <div class="container">
                <div class="mobile-menu-wrapper">
                    <div class="mobile-menu-bar">
                        <ul>
                            <li><a href="{{ route('landingpage') }}" tabindex="0"> {{ __('Home') }} </a></li>
                            @if (!empty($headerMainMenus))
                                @foreach ($headerMainMenus as $headerMainMenu)
                                    <li class="menu-has-items">
                                        @php
                                            $page = App\Models\PageSetting::find($headerMainMenu->page_id);
                                        @endphp
                                        <a @if ($page->type == 'link') ?  href="{{ $page->page_url }}"  @else  href="{{ route('description.page', $headerMainMenu->slug) }}" @endif
                                            tabindex="0">
                                            {{ $page->title }}
                                        </a>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Mobile menu end here -->
        </header>
        <!--header end here-->
        <div class="login-page-wrapper">
            <div class="login-container">
                <div class="login-row d-flex">
                    <div class="login-col-6">
                        @yield('content')
                    </div>
                    <div class="login-media-col">
                        <div class="login-media-inner">
                            <img
                                src="{{ Utility::getsettings('login_image')
                                    ? Storage::url(Utility::getsettings('login_image'))
                                    : asset('assets/images/auth/img-auth-3.svg') }}" />
                            <h3>
                                {{ Utility::getsettings('login_name') ? Utility::getsettings('login_name') : __('“Attention is the new currency”') }}
                            </h3>
                            <p>
                                {{ Utility::getsettings('login_detail') ? Utility::getsettings('login_detail') : __('The more effortless the writing looks, the more effort the writer actually put into the process.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ auth-signup ] end -->
    <!--footer start here-->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-row">
                <div class="footer-col footer-link">
                    <div class="footer-widget">
                        <div class="footer-logo">
                            <img src="{{ Storage::url(setting('app_logo')) ? Storage::url('app-logo/app-logo.png') : asset('assets/images/app-logo.png') }}"
                                alt="footer-logo" class="footer-light-logo">
                            <img src="{{ Utility::logoSetting('app_dark_logo') ? Storage::url('app-logo/app-dark-logo.png') : asset('assets/images/app-dark-logo.png') }}"
                                alt="footer-logo" class="footer-dark-logo">
                        </div>
                        <p>{{ Utility::getsettings('footer_description')
                            ? Utility::getsettings('footer_description')
                            : 'A feature is a unique quality or characteristic that something has. Real-life examples: Elaborately colored tail feathers are peacocks most well-known feature.' }}
                        </p>
                    </div>
                </div>
                @php
                    $footerMainMenus = App\Models\FooterSetting::where('parent_id', 0)->get();
                @endphp
                @if (!empty($footerMainMenus))
                    @foreach ($footerMainMenus as $footerMainMenu)
                        <div class="footer-col">
                            <div class="footer-widget">
                                <h3>{{ $footerMainMenu->menu }}</h3>
                                @php
                                    $subMenus = App\Models\FooterSetting::where('parent_id', $footerMainMenu->id)->get();
                                @endphp
                                <ul>
                                    @foreach ($subMenus as $subMenu)
                                        @php
                                            $page = App\Models\PageSetting::find($subMenu->page_id);
                                        @endphp
                                        @if (isset($page))
                                            <li>
                                                <a @if ($page->type == 'link') ?  href="{{ $page->page_url }}"  @else  href="{{ route('description.page', $subMenu->slug) }}" @endif
                                                    tabindex="0">{{ $page->title }}
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            <div class="footer-bottom">
                <div class="row align-items-center">
                    <div class="col-12">
                        <p>© {{ date('Y') }} {{ config('app.name') }}.</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!--footer end here-->
</body>
<!--scripts start here-->
<script src="{{ asset('vendor/landing-page2/js/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/landing-page2/js/slick.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bouncer.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/form-validation.js') }}"></script>
<script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap-notify.min.js') }}"></script>
<script src="{{ asset('vendor/landing-page2/js/custom.js') }}"></script>
<!--scripts end here-->
<script>
    function myFunction() {
        const element = document.body;
        element.classList.toggle("dark-mode");
        const isDarkMode = element.classList.contains("dark-mode");
        const expirationDate = new Date();
        expirationDate.setDate(expirationDate.getDate() + 30);
        document.cookie = `mode=${isDarkMode ? "dark" : "light"}; expires=${expirationDate.toUTCString()}; path=/`;
        if (isDarkMode) {
            $('.switch-toggle').find('.switch-moon').addClass('d-none');
            $('.switch-toggle').find('.switch-sun').removeClass('d-none');
        } else {
            $('.switch-toggle').find('.switch-sun').addClass('d-none');
            $('.switch-toggle').find('.switch-moon').removeClass('d-none');
        }
    }
    window.addEventListener("DOMContentLoaded", () => {
        const modeCookie = document.cookie.split(";").find(cookie => cookie.includes("mode="));
        if (modeCookie) {
            const mode = modeCookie.split("=")[1];
            if (mode === "dark") {
                $('.switch-toggle').find('.switch-moon').addClass('d-none');
                $('.switch-toggle').find('.switch-sun').removeClass('d-none');
                document.body.classList.add("dark-mode");
            } else {
                $('.switch-toggle').find('.switch-sun').addClass('d-none');
                $('.switch-toggle').find('.switch-moon').removeClass('d-none');
            }
        }
    });

    @if (session('failed'))
        showToStr('Failed!', '{{ session('failed') }}', 'danger');
    @endif
    @if ($errors = session('errors'))
        @if (is_object($errors))
            @foreach ($errors->all() as $error)
                showToStr('Error!', '{{ $error }}', 'danger');
            @endforeach
        @else
            showToStr('Error!', '{{ session('errors') }}', 'danger');
        @endif
    @endif
    @if (session('successful'))
        showToStr('Successfully!', '{{ session('successful') }}', 'success');
    @endif
    @if (session('success'))
        showToStr('Done!', '{{ session('success') }}', 'success');
    @endif
    @if (session('warning'))
        showToStr('Warning!', '{{ session('warning') }}', 'warning');
    @endif
    @if (session('status'))
        showToStr('Great!', '{{ session('status') }}', 'info');
    @endif
    $(document).on('click', '.delete-action', function() {
        var form_id = $(this).attr('data-form-id')
        $.confirm({
            title: '{{ __('Alert !') }}',
            content: '{{ __('Are You sure ?') }}',
            buttons: {
                confirm: function() {
                    $("#" + form_id).submit();
                },
                cancel: function() {}
            }
        });
    });
</script>
<script>
    // Fetch the manifest.json file
    url = '{{ config('app.url') }}';
    var appUrl = url.replace(/\/$/, '');
    file = appUrl + '/public/manifest.json';

    fetch(file)
        .then(response => response.json())
        .then(data => {
            if (data.icons[0].sizes === '128x128') {
                    data.icons[0].src = '{{ Utility::getpath("pwa_icon_128") ? Storage::url(setting("pwa_icon_128")) : "" }}';
            }
            if (data.icons[1].sizes === '144x144') {
                data.icons[1].src = '{{ Utility::getpath("pwa_icon_144") ? Storage::url(setting("pwa_icon_144")) : "" }}';
            }
            if (data.icons[2].sizes === '152x152') {
                data.icons[2].src = '{{ Utility::getpath("pwa_icon_152") ? Storage::url(setting("pwa_icon_152")) : "" }}';
            }
            if (data.icons[3].sizes === '192x192') {
                data.icons[3].src = '{{ Utility::getpath("pwa_icon_192") ? Storage::url(setting("pwa_icon_192")) : "" }}';
            }
            if (data.icons[4].sizes === '256x256') {
                data.icons[4].src = '{{ Utility::getpath("pwa_icon_256") ? Storage::url(setting("pwa_icon_256")) : "" }}';
            }
            if (data.icons[5].sizes === '512x512') {
                data.icons[5].src = '{{ Utility::getpath("pwa_icon_512") ? Storage::url(setting("pwa_icon_512")) : "" }}';
            }

            data.name = "{{ setting('app_name') }}";
            data.short_name = "{{ setting('app_name') }}";
            data.start_url = appUrl;

            const updatedManifest = JSON.stringify(data);
            const blob = new Blob([updatedManifest], {
                type: 'application/json'
            });
            const url = URL.createObjectURL(blob);
            document.querySelector('link[rel="manifest"]').href = url;
        })
        .catch(error => console.error('Error fetching manifest.json:', error));
</script>

@stack('script')
<script>
    feather.replace();
</script>
@if (Utility::getsettings('cookie_setting_enable') == 'on')
    @include('layouts.cookie-consent')
@endif

</html>
