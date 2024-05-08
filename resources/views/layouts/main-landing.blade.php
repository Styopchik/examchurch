<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>@yield('title') | {{ setting('app_name') }}</title>
    <meta name="title"
        content="{{ !empty(Utility::getsettings('meta_title'))
            ? Utility::getsettings('meta_title')
            : Utility::getsettings('app_name') }}">
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

    {{-- manifest.json --}}
    <link rel="manifest" href="{{ asset('/public/manifest.json') }}">
    <link rel="icon"
        href="{{ setting('favicon_logo') ? Storage::url('app-logo/app-favicon-logo.png') : asset('assets/images/app-favicon-logo.png') }}"
        type="image/png">
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/landing-page2/css/landingpage-2.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/landing-page2/css/landingpage2-responsive.css') }}">
    @stack('style')
</head>

<body class="light">
    <!--header start here-->
    @include('layouts.front-header')
    <!--header end here-->
    {{-- <main class="home-wrapper"> --}}
    @yield('content')
    {{-- </main> --}}
    <!--footer start here-->
    @include('layouts.front-footer')
    <!--footer end here-->
    <!--scripts start here-->
    <script src="{{ asset('vendor/landing-page2/js/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/landing-page2/js/slick.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bouncer.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-validation.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('vendor/landing-page2/js/custom.js') }}"></script>
    <!--scripts end here-->

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const playButton = document.getElementById('playButton');
            const videoPlayer = document.getElementById('videoPlayer');
            if (playButton) {
                playButton.addEventListener('click', () => {
                    videoPlayer.style.display = 'block';
                    videoPlayer.play();
                    playButton.style.display = 'none';
                });
            }
        });

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
    </script>
    <script>
        var headerHright = $('header').outerHeight();
        $('header').next('.home-banner-sec').css('padding-top', headerHright + 'px');
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
                    data.icons[0].src =
                        '{{ Utility::getpath('pwa_icon_128') ? Storage::url(setting('pwa_icon_128')) : '' }}';
                }
                if (data.icons[1].sizes === '144x144') {
                    data.icons[1].src =
                        '{{ Utility::getpath('pwa_icon_144') ? Storage::url(setting('pwa_icon_144')) : '' }}';
                }
                if (data.icons[2].sizes === '152x152') {
                    data.icons[2].src =
                        '{{ Utility::getpath('pwa_icon_152') ? Storage::url(setting('pwa_icon_152')) : '' }}';
                }
                if (data.icons[3].sizes === '192x192') {
                    data.icons[3].src =
                        '{{ Utility::getpath('pwa_icon_192') ? Storage::url(setting('pwa_icon_192')) : '' }}';
                }
                if (data.icons[4].sizes === '256x256') {
                    data.icons[4].src =
                        '{{ Utility::getpath('pwa_icon_256') ? Storage::url(setting('pwa_icon_256')) : '' }}';
                }
                if (data.icons[5].sizes === '512x512') {
                    data.icons[5].src =
                        '{{ Utility::getpath('pwa_icon_512') ? Storage::url(setting('pwa_icon_512')) : '' }}';
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
</body>
@if (Utility::keysettings('cookie_setting_enable', 1) == 'on')
    @include('layouts.cookie-consent')
@endif

</html>
