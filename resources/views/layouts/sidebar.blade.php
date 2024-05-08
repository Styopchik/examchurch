@php
    use App\Models\Booking;
    use App\Models\Form;
    $user = \Auth::user();
    $currantLang = $user->currentLanguage();
    $languages = Utility::languages();
    $role_id = $user->roles->first()->id;
    $user_id = $user->id;
    if (Auth::user()->type == 'Admin') {
        $forms = Form::where('created_by', $user->id)->get();
        $bookings = Booking::where('created_by', $user->id)->get();
    } elseif (Auth::user()->type == 'Super Admin') {
        $forms = Form::select(['forms.*'])
            ->where(function ($query) use ($role_id, $user_id) {
                $query
                    ->whereIn('forms.id', function ($query1) use ($role_id) {
                        $query1->select('form_id')->from('assign_forms_roles')->where('role_id', $role_id);
                    })
                    ->OrWhereIn('forms.id', function ($query1) use ($user_id) {
                        $query1->select('form_id')->from('assign_forms_users')->where('user_id', $user_id);
                    });
            })
            ->get();
        $bookings = Booking::where('created_by', $user->id)->get();
    } else {
        if (\Auth::user()->can('access-all-submitted-form')) {
            $forms = Form::get();

            $bookings = Booking::get();
        } else {
            $forms = Form::select(['forms.*'])
                ->where(function ($query) use ($role_id, $user_id) {
                    $query
                        ->whereIn('forms.id', function ($query1) use ($role_id) {
                            $query1->select('form_id')->from('assign_forms_roles')->where('role_id', $role_id);
                        })
                        ->OrWhereIn('forms.id', function ($query1) use ($user_id) {
                            $query1->select('form_id')->from('assign_forms_users')->where('user_id', $user_id);
                        });
                })
                ->get();
            $bookings = Booking::where('created_by', $user->id)->get();
        }
    }
    // $forms = $forms->get();
    // $bookings = $bookings->get();
@endphp
<nav class="dash-sidebar light-sidebar  {{ $user->transprent_layout == 1 ? 'transprent-bg' : '' }}">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('home') }}" class="text-center b-brand">
                @if ($user->dark_layout == 1)
                    <img src="{{ Storage::url(setting('app_logo')) ? Storage::url('app-logo/app-logo.png') : Storage::url('not-exists-data-images/78x78.png') }}"
                        class="app-logo">
                @else
                    <img src="{{ Storage::url(setting('app_dark_logo')) ? Storage::url('app-logo/app-dark-logo.png') : Storage::url('not-exists-data-images/78x78.png') }}"
                        class="app-logo">
                @endif
            </a>
        </div>
        <div class="navbar-content">
            <ul class="dash-navbar">
                <li class="dash-item dash-hasmenu {{ request()->is('home*') ? 'active' : '' }}">
                    <a href="{{ route('home') }}" class="dash-link"><span class="dash-micon"><i
                                class="ti ti-home"></i></span>
                        <span class="dash-mtext custom-weight">{{ __('Dashboard') }}</span></a>
                </li>
                @if ($user->type == 'Super Admin')
                    @canany(['manage-user', 'manage-role'])
                        <li
                            class="dash-item dash-hasmenu {{ request()->is('users*') || request()->is('roles*') ? 'active dash-trigger' : 'collapsed' }}">
                            <a class="dash-link"><span class="dash-micon"><i class="ti ti-layout-2"></i></span><span
                                    class="dash-mtext">{{ __('User Management') }}</span><span class="dash-arrow"><i
                                        data-feather="chevron-right"></i></span></a>
                            <ul class="dash-submenu">
                                @can('manage-user')
                                    <li class="dash-item {{ request()->is('users*') ? 'active' : '' }}">
                                        <a class="dash-link" href="{{ route('users.index') }}">{{ __('Admins') }}</a>
                                    </li>
                                @endcan
                                @can('manage-role')
                                    <li class="dash-item {{ request()->is('roles*') ? 'active' : '' }}">
                                        <a class="dash-link" href="{{ route('roles.index') }}">{{ __('Roles') }}</a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany
                    @hasrole('Super Admin')
                        <li class=" dash-item dash-hasmenu {{ request()->is('request-user*') ? 'active' : '' }}">
                            <a href="{{ route('requestuser.index') }}" class="dash-link"><span class="dash-micon"><i
                                        class="ti ti-shield-check"></i></span>
                                <span class="dash-mtext custom-weight">{{ __('Admin Requests') }}</span>
                            </a>
                        </li>
                    @endhasrole
                    @canany(['manage-coupon', 'manage-plan'])
                        <li
                            class="dash-item dash-hasmenu {{ request()->is('coupon*') || request()->is('plans*') || request()->is('payment*') ? 'active dash-trigger' : 'collapsed' }}">
                            <a class="dash-link"><span class="dash-micon"><i class="ti ti-gift"></i></span><span
                                    class="dash-mtext">{{ __('Subscription') }}</span><span class="dash-arrow"><i
                                        data-feather="chevron-right"></i></span></a>
                            <ul class="dash-submenu">
                                @can('manage-coupon')
                                    <li class="dash-item {{ request()->is('coupon*') ? 'active' : '' }}">
                                        <a class="dash-link" href="{{ route('coupon.index') }}">{{ __('Coupons') }}</a>
                                    </li>
                                @endcan
                                @can('manage-plan')
                                    <li class="dash-item {{ request()->is('plans*') ? 'active' : '' }}">
                                        <a class="dash-link" href="{{ route('plans.index') }}">{{ __('Plans') }}</a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany
                    @canany(['manage-blog', 'manage-category'])
                        <li
                            class="dash-item dash-hasmenu {{ request()->is('blogs*') || request()->is('blogs-category*') ? 'active dash-trigger' : 'collapsed' }}">
                            <a href="#!" class="dash-link">
                                <span class="dash-micon">
                                    <i class="ti ti-forms"></i>
                                </span>
                                <span class="dash-mtext">{{ __('Blog') }}</span>
                                <span class="dash-arrow"><i data-feather="chevron-right"></i></span>
                            </a>
                            <ul class="dash-submenu">
                                @can('manage-category')
                                    <li class="dash-item {{ request()->is('blogs-category*') ? 'active' : '' }}">
                                        <a class="dash-link" href="{{ route('blogs-category.index') }}">
                                            {{ __('Categories') }}
                                        </a>
                                    </li>
                                @endcan
                                @can('manage-blog')
                                    <li
                                        class="dash-item {{ request()->is('blogs*') && !request()->is('blogs-category*') ? 'active' : '' }}">
                                        <a class="dash-link" href="{{ route('blogs.index') }}">
                                            {{ __('Blogs') }}
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany
                    @can('manage-announcement')
                        <li class="dash-item dash-hasmenu {{ request()->is('announcement*') ? 'active' : '' }}">
                            <a href="{{ route('announcement.index') }}" class="dash-link">
                                <span class="dash-micon">
                                    <i class="ti ti-confetti">
                                    </i>
                                </span>
                                <span class="dash-mtext">{{ __('Announcement') }}
                                </span>
                            </a>
                        </li>
                    @endcan
                    @canany(['manage-offline-payment-transactions', 'manage-transactions'])
                        <li
                            class="dash-item dash-hasmenu {{ request()->is('offline*') || request()->is('sales*') ? 'active dash-trigger' : 'collapsed' }}">
                            <a class="dash-link"><span class="dash-micon"><i class="ti ti-clipboard-check"></i></span><span
                                    class="dash-mtext">{{ __('Payment') }}</span><span class="dash-arrow"><i
                                        data-feather="chevron-right"></i></span></a>
                            <ul class="dash-submenu">
                                @can('manage-offline-payment-transactions')
                                    <li class="dash-item {{ request()->is('offline*') ? 'active' : '' }}">
                                        <a class="dash-link"
                                            href="{{ route('offline.index') }}">{{ __('Offline Payments') }}</a>
                                    </li>
                                @endcan
                                @can('manage-transactions')
                                    <li class="dash-item {{ request()->is('sales*') ? 'active' : '' }}">
                                        <a class="dash-link" href="{{ route('sales.index') }}">{{ __('Transactions') }}</a>
                                    </li>
                                @endcan

                            </ul>
                        </li>
                    @endcanany
                    @canany(['manage-email-template', 'manage-sms-template', 'manage-language', 'manage-setting'])
                        <li
                            class="dash-item dash-hasmenu {{ request()->is('email-template*') || request()->is('sms-template*') || request()->is('manage-language*') || request()->is('create-language*') || request()->is('settings*') || request()->is('create-language*') || request()->is('settings*') ? 'active dash-trigger' : 'collapsed' }}">
                            <a class="dash-link"><span class="dash-micon"><i class="ti ti-apps"></i></span><span
                                    class="dash-mtext">{{ __('Account Setting') }}</span><span class="dash-arrow"><i
                                        data-feather="chevron-right"></i></span></a>
                            <ul class="dash-submenu">
                                @can('manage-email-template')
                                    <li class="dash-item {{ request()->is('email-template*') ? 'active' : '' }}">
                                        <a class="dash-link"
                                            href="{{ route('email-template.index') }}">{{ __('Email Templates') }}</a>
                                    </li>
                                @endcan
                                @can('manage-sms-template')
                                    <li class="dash-item {{ request()->is('sms-template*') ? 'active' : '' }}">
                                        <a class="dash-link"
                                            href="{{ route('sms-template.index') }}">{{ __('Sms Templates') }}</a>
                                    </li>
                                @endcan
                                @can('manage-language')
                                    <li
                                        class="dash-item {{ request()->is('manage-language*') || request()->is('create-language*') ? 'active' : '' }}">
                                        <a class="dash-link"
                                            href="{{ route('manage.language', [$currantLang]) }}">{{ __('Manage Languages') }}</a>
                                    </li>
                                @endcan
                                @can('manage-setting')
                                    <li class="dash-item {{ request()->is('settings*') ? 'active' : '' }}">
                                        <a class="dash-link" href="{{ route('settings') }}">{{ __('Settings') }}</a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany
                    @canany(['manage-landingpage', 'manage-testimonial', 'manage-faqs', 'manage-page-setting'])
                        <li
                            class="dash-item dash-hasmenu {{ request()->is('landingpage-setting*') || request()->is('page-setting*') || request()->is('faqs*') || request()->is('testimonials*') ? 'active dash-trigger' : 'collapsed' }}">
                            <a class="dash-link"><span class="dash-micon"><i class="ti ti-table"></i></span><span
                                    class="dash-mtext">{{ __('Frontend Setting') }}</span><span class="dash-arrow"><i
                                        data-feather="chevron-right"></i></span></a>
                            <ul class="dash-submenu">
                                @can('manage-landingpage')
                                    <li class="dash-item {{ request()->is('landingpage-setting*') ? 'active' : '' }}">
                                        <a class="dash-link"
                                            href="{{ route('landingpage.setting') }}">{{ __('Landing Page') }}</a>
                                    </li>
                                @endcan
                                @can('manage-testimonial')
                                    <li class="dash-item {{ request()->is('testimonials*') ? 'active' : '' }}">
                                        <a class="dash-link"
                                            href="{{ route('testimonials.index') }}">{{ __('Testimonials') }}</a>
                                    </li>
                                @endcan
                                @can('manage-faqs')
                                    <li class="dash-item {{ request()->is('faqs*') ? 'active' : '' }}">
                                        <a class="dash-link" href="{{ route('faqs.index') }}">{{ __('Faqs') }}</a>
                                    </li>
                                @endcan
                                @can('manage-page-setting')
                                    <li class="dash-item {{ request()->is('page-setting*') ? 'active' : '' }}">
                                        <a class="dash-link"
                                            href="{{ route('page-setting.index') }}">{{ __('Page Settings') }}</a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany
                    @if (Auth::user()->type == 'Super Admin')
                        <li
                            class="dash-item dash-hasmenu {{ request()->is('telescope*') ? 'active dash-trigger' : 'collapsed' }}">
                            <a class="dash-link"><span class="dash-micon"><i
                                        class="ti ti-device-desktop-analytics"></i></span><span
                                    class="dash-mtext">{{ __('System Analytics') }}</span><span class="dash-arrow"><i
                                        data-feather="chevron-right"></i></span></a>
                            <ul class="dash-submenu">
                                <li class="dash-item {{ request()->is('telescope*') ? 'active' : '' }}">
                                    <a class="dash-link"
                                        href="{{ route('telescope') }}">{{ __('Telescope Dashboard') }}</a>
                                </li>
                            </ul>
                        </li>
                    @endif
                @endif
                @if ($user->type != 'Super Admin')
                    @if (Auth::user()->type == 'Admin')
                        @can('manage-dashboard-widget')
                            <li class="dash-item dash-hasmenu {{ request()->is('index-dashboard*') ? 'active' : '' }}">
                                <a href="{{ route('dashboard.index') }}" class="dash-link"><span class="dash-micon"><i
                                            class="ti ti-dashboard"></i></span>
                                    <span class="dash-mtext custom-weight">{{ __('Dashboard Widgets') }}</span></a>
                            </li>
                        @endcan
                    @endif
                    @canany(['manage-user', 'manage-role'])
                        <li
                            class="dash-item dash-hasmenu {{ request()->is('users*') || request()->is('roles*') ? 'active dash-trigger' : 'collapsed' }}">
                            <a class="dash-link"><span class="dash-micon"><i class="ti ti-layout-2"></i></span><span
                                    class="dash-mtext">{{ __('User Management') }}</span><span class="dash-arrow"><i
                                        data-feather="chevron-right"></i></span></a>
                            <ul class="dash-submenu">
                                @can('manage-user')
                                    <li class="dash-item {{ request()->is('users*') ? 'active' : '' }}">
                                        <a class="dash-link" href="{{ route('users.index') }}">{{ __('Users') }}</a>
                                    </li>
                                @endcan
                                @can('manage-role')
                                    <li class="dash-item {{ request()->is('roles*') ? 'active' : '' }}">
                                        <a class="dash-link" href="{{ route('roles.index') }}">{{ __('Roles') }}</a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany
                    @canany(['manage-plan'])
                        <li
                            class="dash-item dash-hasmenu {{ request()->is('plans*') || request()->is('payment*') ? 'active dash-trigger' : 'collapsed' }}">
                            <a class="dash-link"><span class="dash-micon"><i class="ti ti-gift"></i></span><span
                                    class="dash-mtext">{{ __('Subscription') }}</span><span class="dash-arrow"><i
                                        data-feather="chevron-right"></i></span></a>
                            <ul class="dash-submenu">
                                @can('manage-plan')
                                    <li class="dash-item {{ request()->is('plans*') ? 'active' : '' }}">
                                        <a class="dash-link" href="{{ route('plans.index') }}">{{ __('Plans') }}</a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany

                    @canany(['manage-form', 'manage-form-template', 'manage-submitted-form', 'manage-form-category'])
                        <li
                            class="dash-item dash-hasmenu {{ request()->is('forms*', 'design*') || request()->is('formvalues*') || request()->is('form-template*') || request()->is('form-template/design*') ? 'active dash-trigger' : 'collapsed' }}">
                            <a class="dash-link"><span class="dash-micon"><i class="ti ti-table"></i></span><span
                                    class="dash-mtext">{{ __('Form') }}</span><span class="dash-arrow"><i
                                        data-feather="chevron-right"></i></span></a>
                            <ul class="dash-submenu">
                                @can('manage-form-category')
                                    <li class="dash-item {{ request()->is('form-category*') ? 'active' : '' }}">
                                        <a class="dash-link"
                                            href="{{ route('form-category.index') }}">{{ __('Category') }}</a>
                                    </li>
                                @endcan
                                @can('manage-form-template')
                                    <li
                                        class="dash-item {{ request()->is('form-template*') || request()->is('form-template/design*') ? 'active' : '' }}">
                                        <a class="dash-link"
                                            href="{{ route('form-template.index') }}">{{ __('Template') }}</a>
                                    </li>
                                @endcan
                                @can('manage-form')
                                    <li class="dash-item {{ request()->is('forms*', 'design*') ? 'active' : '' }}">
                                        <a class="dash-link" href="{{ route('forms.index') }}">{{ __('Forms') }}</a>
                                    </li>
                                @endcan
                                @can('manage-submitted-form')
                                    <li class="dash-item">
                                        <a class="dash-link"><span
                                                class="dash-mtext custom-weight">{{ __('Submitted Forms') }}</span><span
                                                class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                                        <ul
                                            class="dash-submenu {{ Request::route()->getName() == 'view.form.values' ? 'd-block' : '' }}">
                                            @foreach ($forms as $form)
                                                <li class="dash-item {{ request()->is('formvalues*') ? 'active' : '' }}">
                                                    <a class="dash-link {{ Request::route()->getName() == 'view.form.values' ? 'show' : '' }}"
                                                        href="{{ route('view.form.values', $form->id) }}">{{ $form->title }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany
                    @canany('manage-booking', 'booking-calendar', 'submitted-booking')
                        <li
                            class="dash-item dash-hasmenu {{ request()->is('bookings*') || request()->is('booking-values*') ? 'active dash-trigger' : 'collapsed' }}">
                            <a class="dash-link"><span class="dash-micon"><i class="ti ti-box-model-2"></i></span><span
                                    class="dash-mtext">{{ __('Booking') }}</span><span class="dash-arrow"><i
                                        data-feather="chevron-right"></i></span></a>
                            <ul class="dash-submenu">
                                @can('manage-booking')
                                    <li
                                        class="dash-item {{ request()->is('bookings*', 'bookings/design*') ? 'active' : '' }}">
                                        <a class="dash-link" href="{{ route('bookings.index') }}">{{ __('Booking') }}</a>
                                    </li>
                                @endcan
                                @can('manage-booking-calendar')
                                    <li class="dash-item selection:{{ request()->is('calendar/booking*') ? 'active' : '' }}">
                                        <a href="{{ route('booking.calendar') }}" class="dash-link">
                                            {{ __('Booking Calendar') }}</a>
                                    </li>
                                @endcan
                                @can('manage-submitted-booking')
                                    <li class="dash-item">
                                        <a class="dash-link"><span
                                                class="dash-mtext custom-weight">{{ __('Submitted Booking') }}</span><span
                                                class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                                        <ul
                                            class="dash-submenu {{ Request::route()->getName() == 'view.booking.values' || Request::route()->getName() == 'timing.bookingvalues.show' || Request::route()->getName() == 'seats.bookingvalues.show' || Request::route()->getName() == 'timing.bookingvalues.edit' || Request::route()->getName() == 'seats.bookingvalues.edit' ? 'd-block' : '' }}">
                                            @foreach ($bookings as $book)
                                                <li class="dash-item {{ request()->is('bookingvalues*') ? 'active' : '' }}">
                                                    <a class="dash-link {{ Request::route()->getName() == 'view.booking.values' || Request::route()->getName() == 'timing.bookingvalues.show' || Request::route()->getName() == 'seats.bookingvalues.show' || Request::route()->getName() == 'timing.bookingvalues.edit' || Request::route()->getName() == 'seats.bookingvalues.edit' ? 'show' : '' }}"
                                                        href="{{ route('view.booking.values', $book->id) }}">{{ $book->business_name }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany
                    @canany(['manage-poll'])
                        @can('manage-poll')
                            <li class="dash-item dash-hasmenu {{ request()->is('poll*') ? 'active' : '' }}">
                                <a class="dash-link" href="{{ route('poll.index') }}"><span class="dash-micon">
                                        <i class="ti ti-accessible"></i></span>
                                    <span class="dash-mtext">{{ __('Polls') }}</span>
                                </a>
                            </li>
                        @endcan
                    @endcanany
                    @can('manage-announcement')
                        <li class="dash-item dash-hasmenu {{ request()->is('announcement*') ? 'active' : '' }}">
                            <a href="{{ route('show.announcement.list') }}" class="dash-link d-flex align-items-center">
                                <span class="dash-micon">
                                    <i class="ti ti-confetti">
                                    </i>
                                </span>
                                <span class="dash-mtext">{{ __('Show Announcement list') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can(['manage-document'])
                        <li class="dash-item dash-hasmenu {{ request()->is('document*') ? 'active' : '' }}">
                            <a href="{{ route('document.index') }}" class="dash-link">
                                <span class="dash-micon">
                                    <i class="ti ti-file-text"></i>
                                </span>
                                <span class="dash-mtext">{{ __('Documents') }}</span>
                            </a>
                        </li>
                    @endcan
                    @canany(['manage-event'])
                        <li class="dash-item dash-hasmenu {{ request()->is('event*') ? 'active' : '' }}">
                            <a class="dash-link" href="{{ route('event.index') }}"><span class="dash-micon">
                                    <i class="ti ti-calendar"></i></span>
                                <span class="dash-mtext">{{ __('Event') }}</span>
                            </a>
                        </li>
                    @endcanany
                    @canany(['manage-setting'])
                        <li
                            class="dash-item dash-hasmenu {{ request()->is('settings*') ? 'active dash-trigger' : 'collapsed' }}">
                            <a class="dash-link"><span class="dash-micon"><i class="ti ti-apps"></i></span><span
                                    class="dash-mtext">{{ __('Account Setting') }}</span><span class="dash-arrow"><i
                                        data-feather="chevron-right"></i></span></a>
                            <ul class="dash-submenu">
                                @can('manage-setting')
                                    <li class="dash-item">
                                        <a class="dash-link" href="{{ route('settings') }}">{{ __('Settings') }}</a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany

                @endif
                @canany(['manage-chat'])
                    @if (setting('pusher_status') == '1')
                        <li
                            class="dash-item dash-hasmenu {{ request()->is('chat*') ? 'active dash-trigger' : 'collapsed' }}">
                            <a class="dash-link"><span class="dash-micon"><i class="ti ti-table"></i></span><span
                                    class="dash-mtext">{{ __('Support') }}</span><span class="dash-arrow"><i
                                        data-feather="chevron-right"></i></span></a>
                            <ul class="dash-submenu">
                                @can('manage-chat')
                                    @if (setting('pusher_status') == '1')
                                        <li class="dash-item">
                                            <a class="dash-link" href="{{ route('chat') }}">{{ __('Chats') }}</a>
                                        </li>
                                    @endif
                                @endcan
                            </ul>
                        </li>
                    @endif
                @endcanany
            </ul>
        </div>
    </div>
</nav>
