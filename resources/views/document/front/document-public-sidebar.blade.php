<nav class="navbar navbar-expand-lg main-navbar">
    <form class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
            <li>
                <a href="javascript:void(0);" class="logo-space" tabindex="0">
                    {!! Form::image(
                        $document->logo
                            ? (Storage::exists($document->logo)
                                ? asset('storage/app/' . $document->logo)
                                : Storage::url('app-logo/app-dark-logo.png'))
                            : ($document->logo != ''
                                ? asset('storage/app/' . $document->logo)
                                : Storage::url('app-logo/app-dark-logo.png')),
                        null,
                        [
                            'class' => 'me-3 wid-logo side-bar-logo',
                        ],
                    ) !!}
                </a>
            </li>
        </ul>
        <ul class="navbar-nav mr-3">
            <li>
                <a href="javascript:void(0);" data-toggle="sidebar"
                    class="nav-link nav-link-lg nav-color">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
        </ul>
    </form>
</nav>
<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="javascript:void(0);" class="logo-space" tabindex="0">
                {!! Form::image(
                    $document->logo
                        ? (Storage::exists($document->logo)
                            ? asset('storage/app/' . $document->logo)
                            : Storage::url('app-logo/app-dark-logo.png'))
                        : ($document->logo != ''
                            ? asset('storage/app/' . $document->logo)
                            : Storage::url('app-logo/app-dark-logo.png')),
                    null,
                    [
                        'class' => 'me-3 wid-logo side-bar-logo',
                    ],
                ) !!}
            </a>
        </div>
        <ul class="sidebar-menu acnav-list">
            @php $i = 0; @endphp
            @foreach ($docMenu as $key => $menu)
                @if ($menu->parent_id == 0)
                    @if (in_array($menu->id, $parentArray))
                        @php
                            $documentmenu = App\Models\DocumentMenu::where('parent_id', $menu->id)->get();
                        @endphp
                        <li
                            class="menu-item has-children @if (request()->is('document/' . $menu->slug . '/' . $menus->slug . '/*')) is-open @endif
                                @foreach ($documentmenu as $key => $docmenu)
                                    @if (Request::is('document/' . $menu->slug . '/*'))
                                        active
                                    @elseif (isset($documentMenu->slug))
                                        @if ($menu->slug == $documentMenu->slug)
                                            active
                                        @endif
                                    @else
                                        @if ($i == 0 && Request::is('document/public*'))
                                            active
                                        @endif
                                    @endif
                                @endforeach
                                ">
                                <span class="acnav-label">
                                    <a href="{{ route('documentmenu.menu', $menu->slug) }}"
                                        class="nav-link text-menu">
                                        <span
                                            class="">
                                                {{ substr($menu->title, 0, 15) . (strlen($menu->title) > 15 ? '...' : '') }}
                                        </span>
                                    </a>
                                    <div class="down-arrow">
                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M7.29571 15.3171C7.48307 15.5034 7.73652 15.6079 8.00071 15.6079C8.26489 15.6079 8.51834 15.5034 8.70571 15.3171L12.2957 11.7771L15.8357 15.3171C16.0231 15.5034 16.2765 15.6079 16.5407 15.6079C16.8049 15.6079 17.0583 15.5034 17.2457 15.3171C17.3394 15.2242 17.4138 15.1136 17.4646 14.9917C17.5154 14.8698 17.5415 14.7391 17.5415 14.6071C17.5415 14.4751 17.5154 14.3444 17.4646 14.2225C17.4138 14.1007 17.3394 13.9901 17.2457 13.8971L13.0057 9.65712C12.9127 9.56339 12.8021 9.48899 12.6803 9.43823C12.5584 9.38746 12.4277 9.36132 12.2957 9.36132C12.1637 9.36132 12.033 9.38746 11.9111 9.43823C11.7893 9.48899 11.6787 9.56339 11.5857 9.65712L7.29571 13.8971C7.20198 13.9901 7.12758 14.1007 7.07681 14.2225C7.02605 14.3444 6.99991 14.4751 6.99991 14.6071C6.99991 14.7391 7.02605 14.8698 7.07681 14.9917C7.12758 15.1136 7.20198 15.2242 7.29571 15.3171Z">
                                            </path>
                                        </svg>
                                    </div>
                                </span>
                                <ul class="dropdown-menu acnav-list">
                                    @foreach ($documentmenu as $key => $docmenu)
                                        <li class="@if (Request::is('document/' . $menu->slug . '/' . $docmenu->slug)) active @endif">
                                            <a class="nav-link"
                                                href="{{ route('documentsubmenu.submenu', [$menu->slug, $docmenu->slug]) }}">
                                                {{ substr($docmenu->title, 0, 20) . (strlen($docmenu->title) > 20 ? '...' : '') }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </span>
                        </li>
                    @else
                        <li
                            class="@if (isset($documentMenu->id)) @if ($menu->id == $documentMenu->id)
                                            active @endif
                                                @else
                                                @if ($key == 0) active @endif
                                    @endif">
                            <a class="nav-link" href="{{ route('documentmenu.menu', $menu->slug) }}">
                                <span>{{ substr($menu->title, 0, 15) . (strlen($menu->title) > 15 ? '...' : '') }}</span>
                            </a>
                        </li>
                    @endif
                @endif
                @php $i++; @endphp
            @endforeach
        </ul>
    </aside>
</div>
