@php
    config([
        'captcha.sitekey' => Utility::getsettings('recaptcha_key'),
        'captcha.secret' => Utility::getsettings('recaptcha_secret'),
    ]);
@endphp
@extends('layouts.main-landing')
@section('title', __('Contact US'))
@section('content')
    <section class="blog-page-banner"
        style="background-image: url({{ Storage::url(Utility::keysettings('background_image', 1)) }});" width="100% "
        height="100%">
        <div class="container">
            <div class="common-banner-content">
                <div class="section-title">
                    <h2>{{ __('Contact US') }}</h2>
                </div>
                <ul class="back-cat-btn d-flex align-items-center justify-content-center">
                    <li><a href="{{ route('home') }}">{{ __('Home') }}</a>
                        <span>/</span>
                    </li>
                    <li><a href="#">{{ __('Contact US') }}</a></li>
                </ul>
            </div>
        </div>
    </section>
    <div class="container-fluid pt">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <article class="article article-detail article-noshadow">
                        <div class="p-0">
                            <iframe width="100%" height="450" frameborder="0" scrolling="no" marginheight="0"
                                marginwidth="0"
                                src="https://maps.google.com/maps?q={{ setting('latitude') }},{{ setting('longitude') }}&hl=en&z=14&amp;output=embed">
                            </iframe>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </div>
    <section class="blog-sidebar-sec pt">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <article class="article article-detail article-noshadow">
                        <div class="p-0 pb">
                            <section class="custom-section" id="sct-form-contact">
                                <div class="container position-relative zindex-100">
                                    <div class="mb-1 row justify-content-center">
                                        <div class="text-center section-title col-lg-6">
                                            <h2>
                                                <b>{{ __('Contact us') }}<p>
                                                        {{ __('If there is something we can help you with jut let us know. We will be more than happy to offer you our help') }}
                                                    </p>
                                                </b>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center">
                                        <div class="col-lg-6">
                                            <div class="card">
                                                {!! Form::open([
                                                    'route' => 'contact.mail',
                                                    'method' => 'Post',
                                                    'data-validate',
                                                    'enctype' => 'multipart/form-data',
                                                ]) !!}
                                                <div class="form-group">
                                                    {!! Form::text('name', null, [
                                                        'class' => 'form-control',
                                                        'placeholder' => __('Your name'),
                                                        'required',
                                                    ]) !!}
                                                </div>
                                                <div class="form-group">
                                                    {!! Form::email('email', null, [
                                                        'class' => 'form-control',
                                                        'placeholder' => __('email@example.com'),
                                                        'required',
                                                    ]) !!}
                                                </div>
                                                <div class="form-group">
                                                    {!! Form::text('contact_no', null, [
                                                        'class' => 'form-control',
                                                        'placeholder' => __('+40-745-234-567'),
                                                        'required',
                                                    ]) !!}
                                                </div>
                                                <div class="form-group">
                                                    {!! Form::textarea('message', null, [
                                                        'class' => 'form-control',
                                                        'data-toggle' => 'autosize',
                                                        'rows' => '3',
                                                        'placeholder' => __('Tell us a few words ...'),
                                                        'required',
                                                    ]) !!}
                                                </div>
                                                @if (Utility::getsettings('contact_us_recaptcha_status') == '1')
                                                    <div class="my-3 text-center">
                                                        {!! NoCaptcha::renderJs() !!}
                                                        {!! NoCaptcha::display() !!}
                                                    </div>
                                                @endif
                                                <div>
                                                    <button type="submit"
                                                        class="btn btn-primary">{{ __('Send your message') }}</button>
                                                </div>
                                                {!! Form::close() !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
@endsection
