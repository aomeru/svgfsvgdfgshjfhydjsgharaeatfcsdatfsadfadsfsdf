<div class="d-sm-none d-flex justify-content-between" id="portal-mobile-header">
    <div>
        <a class="brand" href="{{ url('/') }}">
            <img src="{{ asset('images/brand-name.png') }}" alt="">
        </a>
    </div>
    <div class="d-flex align-items-center">
        <a class="portal-mobile-menu-button d-flex align-items-center" id="portal-mobile-menu-button">
            <i class="fas fa-bars"></i>
        </a>
    </div>
</div>
<div id="portal-right" class="p-3 p-md-5">
    <div class="header d-flex justify-content-between mb-2">
        <div class="heading d-flex align-items-center">
            <h2 class="d-none d-sm-block">@yield('portal_page_title')</h2><h4 class="d-block d-sm-none">@yield('portal_page_title')</h4>
        </div>
        <div class="d-flex jestify-content-end">
            <div class="bc d-flex align-items-center">
                @yield('bc')
            </div>
        </div>
    </div>

    @include('partials.messages')
    <div id="process-message"></div>

    @yield('content')
</div>
