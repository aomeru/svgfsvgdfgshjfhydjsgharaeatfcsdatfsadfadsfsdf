<?php
    $theme = 'dark';
    $brand = $theme == 'dark' ? '-w' : '';
?>
<div class="p-3 {{$theme}}" id="portal-left">
    <div class="row d-none d-sm-block">
        <div class="d-flex justify-content-center">
            <div class="col-9">
                <a class="" href="{{ route('portal') }}">
                    <img src="{{ asset('images/brand-name'.$brand.'.png') }}" alt="" class="img-fluid">
                </a>
            </div>
        </div>
    </div>

    <hr class="mb-4 d-none d-sm-block @if($theme == 'dark') border-dark @endif">
    <div class="d-flex justify-content-start">
        <div class="mr-2 d-flex align-items-center" style="height: 70px">
            @include('partials.portal.profile-image',['userdata' => Auth::user()])
        </div>
        <div class="d-flex align-items-center">
            <div class="">
                <h4 class="mb-0 text-white">
                    {{Auth::user()->firstname.' '.Auth::user()->lastname}}
                </h4>
                <p class="mb-0 c-999">
                    <small><em>{{Auth::user()->job_title ? Auth::user()->job_title : 'Job Title'}} <br> {{Auth::user()->unit != null ? Auth::user()->unit->department->title : 'Department'}}</em></small>
                </p>
            </div>
        </div>
    </div>
    <hr class="mb-4 @if($theme == 'dark') border-dark @endif">

    <ul class="nav flex-column px-2">
        @if(Laratrust::can('dashboard'))
            <li class="nav-item"><a href="{{ route('portal') }}" class="nav-link @if(!isset($nav)) active @endif"><i class="fas fa-desktop fa-fw mr-2"></i>Dashboard</a></li>
        @endif


        @if(Laratrust::can('*leave*'))
            <li class="nav-item">
                <a class="nav-link has-sub-nav @if(isset($nav) && $nav == 'leave') sub-active @endif"><i class="far fa-calendar-alt fa-fw mr-2"></i>Leave</a>
                <ul id="testleave" class="sub-nav flex-column ml-4 pl-4">
                    @if(Laratrust::can('*-leave'))<li class="sub-nav-item"><a href="{{route('portal.leave')}}" class="sub-nav-link @if(isset($subnav) && $subnav == 'leave') active @endif"><i class="far fa-calendar-alt fa-fw mr-2"></i>My Leave</a></li>@endif

                    @if(Laratrust::can('update-leave-request'))<li class="sub-nav-item"><a href="{{route('portal.leave.request')}}" class="sub-nav-link @if(isset($subnav) && $subnav == 'leave-requests') active @endif"><i class="far fa-calendar-check fa-fw mr-2"></i>Leave Requests</a></li>@endif

                    @if(Laratrust::can('*-holiday'))<li class="sub-nav-item"><a href="{{route('holiday.index')}}" class="sub-nav-link @if(isset($subnav) && $subnav == 'holiday') active @endif"><i class="fas fa-plane fa-fw mr-2"></i>Holidays</a></li>@endif

                    @if(Laratrust::can('*-leave-allocation'))<li class="sub-nav-item"><a href="{{route('leave-allocation.index')}}" class="sub-nav-link @if(isset($subnav) && $subnav == 'leave-allocation') active @endif"><i class="fas fa-calendar fa-fw mr-2"></i>Leave Allocation</a></li>@endif

                    @if(Laratrust::can('*-leave-type'))<li class="sub-nav-item"><a href="{{route('leave-type.index')}}" class="sub-nav-link @if(isset($subnav) && $subnav == 'leave-type') active @endif"><i class="far fa-calendar fa-fw mr-2"></i>Leave Types</a></li>@endif

                    @if(Laratrust::can('*-leave-record'))<li class="sub-nav-item"><a href="{{route('leave-record.index')}}" class="sub-nav-link @if(isset($subnav) && $subnav == 'leave-record') active @endif"><i class="far fa-calendar fa-fw mr-2"></i>Leave Record</a></li>@endif
                </ul>
            </li>
        @endif


        @if(Laratrust::can('*-kpi*'))
            <li class="nav-item">
                <a class="nav-link has-sub-nav @if(isset($nav) && $nav == 'kpi') sub-active @endif"><i class="fas fa-chart-line fa-fw mr-2"></i>KPI</a>
                <ul class="sub-nav flex-column ml-4 pl-4">
                    @if(Laratrust::can('*-kpi-goals'))
                        <li class="sub-nav-item"><a href="{{route('portal.kpi.goals')}}" class="sub-nav-link @if(isset($subnav) && $subnav == 'goals') active @endif"><i class="fas fa-chart-line fa-fw mr-2"></i>Goals</a></li>
                    @endif

                    @if(Laratrust::can('*-kpi-settings'))
                        <li class="sub-nav-item"><a href="{{route('portal.kpi.settings')}}" class="sub-nav-link @if(isset($subnav) && $subnav == 'settings') active @endif"><i class="fas fa-cogs fa-fw mr-2"></i>KPI Settings</a></li>
                    @endif
                </ul>
            </li>
        @endif

        @if(Laratrust::can('*-user|*-manager'))
        <li class="nav-item">
            <a class="nav-link has-sub-nav @if(isset($nav) && $nav == 'users') sub-active @endif"><i class="far fa-user-circle fa-fw mr-2"></i>Users</a>
            <ul class="sub-nav flex-column ml-4 pl-4">
                @if(Laratrust::can('*-user'))<li class="sub-nav-item"><a href="{{route('portal.users')}}" class="sub-nav-link @if(isset($subnav) && $subnav == 'all-users') active @endif"><i class="fas fa-users fa-fw mr-2"></i>All Users</a></li>@endif
                @if(Laratrust::can('*-manager'))<li class="sub-nav-item"><a href="{{route('managers.index')}}" class="sub-nav-link @if(isset($subnav) && $subnav == 'managers') active @endif"><i class="fas fa-user-tie fa-fw mr-2"></i>Managers</a></li>@endif
            </ul>
        </li>
        @endif

        @if(Laratrust::can('*-department|*-unit|*-role|*-permission'))
            <li class="nav-item">
                <a class="nav-link has-sub-nav @if(isset($nav) && $nav == 'settings') sub-active @endif"><i class="fas fa-cogs fa-fw mr-2"></i>Settings</a>
                <ul class="sub-nav flex-column ml-4 pl-4">
                    @if(Laratrust::can('read-department|read-unit'))<li class="sub-nav-item"><a href="{{ route('portal.depts') }}" class="sub-nav-link @if(isset($subnav) && $subnav == 'departments-and-units') active @endif"><i class="fas fa-university fa-fw mr-2"></i>Departments & Units</a></li>@endif

                    @if(Laratrust::can('read-role'))<li class="sub-nav-item"><a href="{{route('roles.index')}}" class="sub-nav-link @if(isset($subnav) && $subnav == 'roles') active @endif"><i class="fas fa-user-shield fa-fw mr-2"></i>Roles</a></li>@endif

                    @if(Laratrust::can('read-permission'))<li class="sub-nav-item"><a href="{{route('permissions.index')}}" class="sub-nav-link @if(isset($subnav) && $subnav == 'permissions') active @endif"><i class="fas fa-lock fa-fw mr-2"></i>Permissions</a></li>@endif
                </ul>
            </li>
        @endif

        <div class="dropdown-divider @if($theme == 'dark') border-dark @endif"></div>

        <li class="nav-item"><a href="{{ route('home') }}" class="nav-link"><i class="fas fa-home fa-fw mr-2"></i>Homepage</a></li>

        <li class="nav-item"><a href="{{ route('logout') }}" class="nav-link nav-link-red"><i class="fas fa-power-off fa-fw mr-2"></i>LogOut</a></li>
    </ul>

</div>
