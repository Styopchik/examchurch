<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ \App\Facades\UtilityFacades::getsettings('rtl') == '1' ? 'rtl' : '' }}">

<head>
    @php
        $primaryColor = \App\Facades\UtilityFacades::getsettings('color');
        if (isset($primaryColor)) {
            $color = $primaryColor;
        } else {
            $color = 'theme-2';
        }
    @endphp
    <title>@yield('title') | {{ setting('app_name') }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="manifest" href="{{ asset('/public/manifest.json') }}">
    <link rel="icon" href="{{ setting('favicon_logo') ? Storage::url('app-logo/app-favicon-logo.png') : '' }}"
        type="image/png">
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/customizer.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/jqueryform/css/jquery.rateyo.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/css/custom.css') }}">
    @if (Utility::getsettings('rtl') == '1')
        <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}" id="main-style-link">
    @else
        @if (Utility::getsettings('dark_mode') == 'on')
            <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css') }}">
        @else
            <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
        @endif
    @endif
    @stack('style')
</head>

<body class="{{ $color }}">
    <div class="loading">Loading…</div>
    <div class="dash-content">
        @yield('content')
    </div>
    <div class="modal fade" role="dialog" id="common_modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="body">

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" role="dialog" id="common_modal1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <p></p>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" role="dialog" id="common_modal2">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <p></p>
                </div>
            </div>
        </div>
    </div>
    <div class="top-0 p-3 position-fixed end-0" style="z-index: 99999">
        <div id="liveToast" class="toast fade" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body"> </div>
                <button type="button" class="m-auto btn-close btn-close-white me-2" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>
</body>

<script src="{{ asset('vendor/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
<script src="{{ asset('vendor/modules/tooltip.js') }}"></script>
<script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('assets/js/plugins/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bouncer.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/form-validation.js') }}"></script>
<script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap-notify.min.js') }}"></script>
<script src="{{ asset('assets/js/custom.js') }}"></script>

@include('layouts.includes.alerts')

<script>
    var toster_pos = 'right';
    window.addEventListener("load", function() {
        // Hide the loader when the page is fully loaded
        var loader = document.querySelector(".loading");
        $(loader).addClass('d-none');
    });
</script>
@if (!empty(setting('gtag')))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ setting('gtag') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', '{{ setting('gtag') }}');
    </script>
@endif
<script>
    if ($('#sschoices-multiple-remove-button').length) {
        var multipleCancelButton = new Choices('#sschoices-multiple-remove-button', {
            removeItemButton: true,
        });
    }
    feather.replace();
    var pctoggle = document.querySelector("#pct-toggler");
    if (pctoggle) {
        pctoggle.addEventListener("click", function() {
            if (
                !document.querySelector(".pct-customizer").classList.contains("active")
            ) {
                document.querySelector(".pct-customizer").classList.add("active");
            } else {
                document.querySelector(".pct-customizer").classList.remove("active");
            }
        });
    }
    var themescolors = document.querySelectorAll(".themes-color > a");
    for (var h = 0; h < themescolors.length; h++) {
        var c = themescolors[h];
        c.addEventListener("click", function(event) {
            var targetElement = event.target;
            if (targetElement.tagName == "SPAN") {
                targetElement = targetElement.parentNode;
            }
            var temp = targetElement.getAttribute("data-value");
            removeClassByPrefix(document.querySelector("body"), "theme-");
            document.querySelector("body").classList.add(temp);
        });
    }

    function removeClassByPrefix(node, prefix) {
        for (let i = 0; i < node.classList.length; i++) {
            let value = node.classList[i];
            if (value.startsWith(prefix)) {
                node.classList.remove(value);
            }
        }
    }
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
</body>

</html>
