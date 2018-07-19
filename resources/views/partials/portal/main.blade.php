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
    <div class="header d-flex justify-content-between align-items-center mb-2">
        <div class="heading d-flex align-items-center">
            <h2 class="d-none d-sm-block">@yield('portal_page_title')</h2><h4 class="d-block d-sm-none">@yield('portal_page_title')</h4>
        </div>
        <div class="d-flex justify-content-end align-items-center">
            <div class="bc d-flex align-items-center">
                @yield('bc')
            </div>
            <?php $ncount = Auth::user()->unreadNotifications->count(); ?>
            <button id="notif-button" class="btn ml-2 p-0 d-flex align-items-center notif-toggle" type="button" @if($ncount == 0) disabled @endif>
                <i class="fas fa-bell mr-1 @if($ncount > 0) fa-2x texxt-danger @endif"></i>@if($ncount > 0) <span class="badge badge-danger" style="margin-left: -15px; margin-top: -15px">{{$ncount}}</span> @endif
            </button>
        </div>
    </div>

    @include('partials.messages')

    <div id="process-message"></div>

    <div id="portal">@yield('content')</div>
</div>
