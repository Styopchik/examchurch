<title>{{ config('chatify.name') }}</title>

{{-- Meta tags --}}
{{-- <meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="route" content="{{ $route }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="url" content="{{ url('').'/'.config('chatify.routes.prefix') }}" data-user="{{ Auth::user()->id }}">
<link rel="icon" href="{{ setting('favicon_logo') ? Storage::url('app-logo/app-favicon-logo.png') : '' }}" type="image/png"> --}}

<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="id" content="{{ $id }}">
<meta name="messenger-color" content="{{ $messengerColor }}">
<meta name="messenger-theme" content="{{ $dark_mode }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="url" content="{{ url('').'/'.config('chatify.routes.prefix') }}" data-user="{{ Auth::user()->id }}">

{{-- scripts --}}
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/js/chatify/font.awesome.min.js') }}"></script>
<script src="{{ asset('vendor/js/chatify/autosize.js') }}"></script>
<script src="{{ asset('vendor/js/app.js') }}"></script>
<script src='{{ asset('assets/js/nprogress.js') }}'></script>

{{-- styles --}}
<link rel='stylesheet' href='{{ asset('assets/css/nprogress.css') }}'/>
<link href="{{ asset('vendor/css/chatify/style.css') }}" rel="stylesheet" />
<link href="{{ asset('vendor/css/chatify/'.$dark_mode.'.mode.css') }}" rel="stylesheet" />
{{-- <link href="{{ asset('vendor/css/app.css') }}" rel="stylesheet" /> --}}

{{-- Messenger Color Style--}}
@include('Chatify::layouts.messengerColor')
