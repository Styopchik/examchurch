@php
    $users = \Auth::user();
    $languages = Utility::languages();
@endphp
<header class="site-header header-style-one">
    @if (isset($announcementBars) && $announcementBars->count() > 0)
        <div class="announcebar">
            @foreach ($announcementBars as $announcementBar)
                @if ($announcementBar->cont < 1)
                    <p class="text-center text-capitalize">
                        <b>{{ __('Announcement :') }}</b>
                        <a href="{{ route('show.public.announcement', ['slug' => $announcementBar->slug]) }}"
                            class="announcement-title">
                            {{ $announcementBar->title }}
                        </a>
                    </p>
                @endif
            @endforeach
        </div>
    @endif
    <div class="main-navigationbar">
        <div class="container">
            <div class="navigation-row d-flex align-items-center ">
                <nav class="menu-items-col d-flex align-items-center justify-content-between ">
                    <div class="logo-col">
                        <h1>
                            <a tabindex="0">
                                <img src="{{ Storage::url(setting('app_dark_logo')) ? Storage::url('app-logo/app-dark-logo.png') : asset('assets/images/app-dark-logo.png') }}"
                                    width="100%" height="100%" loading="lazy" />
                            </a>
                        </h1>
                    </div>
                    <div class="menu-item-right-col d-flex align-items-center justify-content-between">
                        <div class="menu-left-col">
                            <ul class="main-nav d-flex align-items-center">
                                <li class="menus-lnk">
                                    <a href="{{ route('landingpage') }}" tabindex="0">{{ __('Home') }}</a>
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
                                            <path d="M13 3V1" stroke="black" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path d="M13 25V23" stroke="black" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path d="M3 13H1" stroke="black" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path d="M25 13H23" stroke="black" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path d="M5.92977 20.07L4.50977 21.49" stroke="black" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M21.4903 4.51001L20.0703 5.93001" stroke="black" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M20.0703 20.07L21.4903 21.49" stroke="black" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M4.50977 4.51001L5.92977 5.93001" stroke="black" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </a>
                                    <a class="switch-moon">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="512" viewBox="0 0 512 512"
                                            width="512">
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
                                            <option class="" @if ($lang == $language) selected @endif
                                                value="{{ route('change.lang', $language) }}">
                                                {{ Str::upper($language) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </li>
                                @yield('auth-topbar')
                                <li class="login-btn">
                                    @if ($users)
                                        <a href="{{ route('home') }}">{{ __('Dashboard') }}</a>
                                    @else
                                        <a href="{{ route('home') }}">{{ __('Login') }}</a>
                                    @endif
                                </li>
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
                    <li>
                        <div class="mobile-login-btn">
                            @if ($users)
                                <a href="{{ route('home') }}">{{ __('Dashboard') }}</a>
                            @else
                                <a href="{{ route('home') }}">{{ __('Login') }}</a>
                            @endif
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- Mobile menu end here -->
</header>
