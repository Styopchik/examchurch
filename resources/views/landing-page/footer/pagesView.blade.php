@extends('layouts.main-landing')
@section('title', isset($pageFooter->menu) ? $pageFooter->menu : null)
@section('content')
    <section class="blog-page-banner"
        style="background-image: url({{ Storage::url(Utility::keysettings('background_image', 1)) }});" width="100%"
        height="100%">
        <div class="container">
            <div class="common-banner-content">
                <div class="section-title">
                    <h2>{{ isset($pageFooter->menu) ? $pageFooter->menu : null }}</h2>
                </div>
                <ul class="back-cat-btn d-flex align-items-center justify-content-center">
                    <li><a href="{{ route('landingpage') }}">{{ __('Home') }}</a>
                        <span>/</span>
                    </li>
                    <li><a href="#">{{ isset($pageFooter->menu) ? $pageFooter->menu : null }}</a></li>
                </ul>
            </div>
        </div>
    </section>
    <section class="blog-sidebar-sec pt">
        <div class="container">
            <div class="row">
                <div class="mx-auto col-lg-12 col-md-12 col-12">
                    <div class="sidebar-widget-area">
                        <h3 class="title">{{ isset($pageFooter->menu) ? $pageFooter->menu : null }}</h3>
                        <div style="margin-top: 20px">
                            <p>{!! isset($pageSetting->description) ? $pageSetting->description : null !!}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection