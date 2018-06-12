<div class="d-sm-none d-flex justify-content-between" id="portal-mobile-header">
    <div>
        <a class="brand" href="{{ url('/') }}">
            <img src="{{ asset('images/brand-name.png') }}" alt="">
        </a>
    </div>
    <div class="d-flex align-items-center">
        <a class="portal-mobile-menu-button" id="portal-mobile-menu-button">
            <i class="fas fa-bars fa-2x"></i>
        </a>
    </div>
</div>
<div id="portal-right" class="p-3 p-md-5">
    <h1>@yield('portal_page_title')</h1>
    @yield('content')
</div>
