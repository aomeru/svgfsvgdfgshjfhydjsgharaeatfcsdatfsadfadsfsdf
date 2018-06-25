<div class="p-3" id="portal-left">
    <div class="row d-none d-sm-block">
        <div class="d-flex justify-content-center">
            <div class="col-9">
                <a class="" href="{{ route('portal') }}">
                    <img src="{{ asset('images/brand-name.png') }}" alt="" class="img-fluid">
                </a>
            </div>
        </div>
    </div>

    <hr class="mb-4 d-none d-sm-block">
    <div class="d-flex justify-content-start">
        <div class="mr-2 d-flex align-items-center" style="height: 70px">
            <img src="@if(!Auth::user()->photo) {{ asset('images/user.png') }} @else data:image.jpg;base64,{{Auth::user()->photo}} @endif" class="img-fluuid rounded-circle border border-cyan" alt="" width="auto" height="100%">
        </div>
        <div class="d-flex align-items-center">
            <div class="">
                <h4 class="mb-0">
                    {{Auth::user()->firstname.' '.Auth::user()->lastname}}
                </h4>
                <p class="mb-0 c-666">
                    <em>{{Auth::user()->job_title ? Auth::user()->job_title : 'Job Title'}} / {{Auth::user()->unit != null ? Auth::user()->unit->department->title : 'Department'}}</em>
                </p>
            </div>
        </div>
    </div>
    <hr class="mb-4">

    <ul class="nav flex-column px-2">
        @if(Laratrust::can('dashboard'))<li class="nav-item"><a href="{{ route('portal') }}" class="nav-link @if(!isset($nav)) active @endif"><i class="fas fa-desktop fa-fw mr-2"></i>Dashboard</a></li>@endif

        @if(Laratrust::can('*leave*'))
        <li class="nav-item">
            <a class="nav-link has-sub-nav @if(isset($nav) && $nav == 'leave') sub-active @endif"><i class="far fa-calendar-alt fa-fw mr-2"></i>Leave</a>
            <ul id="testleave" class="sub-nav flex-column ml-4 pl-4">
                <li class="sub-nav-item"><a href="" class="sub-nav-link"><i class="far fa-calendar-plus fa-fw mr-2"></i>Apply for Leave</a></li>
                <li class="sub-nav-item"><a href="" class="sub-nav-link"><i class="far fa-calendar-alt fa-fw mr-2"></i>My Leave</a></li>
                <li class="sub-nav-item"><a href="" class="sub-nav-link"><i class="far fa-calendar-check fa-fw mr-2"></i>Leave Approvals</a></li>
                @if(Laratrust::can('read-leave-allocation'))<li class="sub-nav-item"><a href="{{route('leave-allocation.index')}}" class="sub-nav-link @if(isset($subnav) && $subnav == 'leave-allocation') active @endif"><i class="fas fa-calendar fa-fw mr-2"></i>Leave Allocation</a></li>@endif
                @if(Laratrust::can('read-leave-type'))<li class="sub-nav-item"><a href="{{route('leave-type.index')}}" class="sub-nav-link @if(isset($subnav) && $subnav == 'leave-type') active @endif"><i class="far fa-calendar fa-fw mr-2"></i>Leave Types</a></li>@endif
            </ul>
        </li>
        @endif

        <li class="nav-item"><a href="" class="nav-link"><i class="fas fa-chart-line fa-fw mr-2"></i>KPI Objectives</a></li>

        @if(Laratrust::can('*-user|*-manager'))
        <li class="nav-item">
            <a class="nav-link has-sub-nav @if(isset($nav) && $nav == 'users') sub-active @endif"><i class="far fa-user-circle fa-fw mr-2"></i>Users</a>
            <ul id="testuser" class="sub-nav flex-column ml-4 pl-4">
                @if(Laratrust::can('*-user'))<li class="sub-nav-item"><a href="{{route('portal.users')}}" class="sub-nav-link @if(isset($subnav) && $subnav == 'all-users') active @endif"><i class="fas fa-users fa-fw mr-2"></i>All Users</a></li>@endif
                @if(Laratrust::can('*-manager'))<li class="sub-nav-item"><a href="" class="sub-nav-link"><i class="fas fa-user-tie fa-fw mr-2"></i>Managers</a></li>@endif
            </ul>
        </li>
        @endif

        @if(Laratrust::can('*-department|*-unit|*-role|*-permission'))
            <li class="nav-item">
                <a class="nav-link has-sub-nav @if(isset($nav) && $nav == 'settings') sub-active @endif"><i class="fas fa-cogs fa-fw mr-2"></i>Settings</a>
                <ul id="testuser" class="sub-nav flex-column ml-4 pl-4">
                    @if(Laratrust::can('read-department|read-unit'))<li class="sub-nav-item"><a href="{{ route('portal.depts') }}" class="sub-nav-link @if(isset($subnav) && $subnav == 'departments-and-units') active @endif"><i class="fas fa-university fa-fw mr-2"></i>Departments & Units</a></li>@endif

                    @if(Laratrust::can('read-role'))<li class="sub-nav-item"><a href="{{route('roles.index')}}" class="sub-nav-link @if(isset($subnav) && $subnav == 'roles') active @endif"><i class="fas fa-user-shield fa-fw mr-2"></i>Roles</a></li>@endif

                    @if(Laratrust::can('read-permission'))<li class="sub-nav-item"><a href="{{route('permissions.index')}}" class="sub-nav-link @if(isset($subnav) && $subnav == 'permissions') active @endif"><i class="fas fa-lock fa-fw mr-2"></i>Permissions</a></li>@endif
                </ul>
            </li>
        @endif

        <div class="dropdown-divider"></div>

        <li class="nav-item"><a href="{{ route('home') }}" class="nav-link"><i class="fas fa-home fa-fw mr-2"></i>Homepage</a></li>

        <li class="nav-item"><a href="{{ route('logout') }}" class="nav-link nav-link-red"><i class="fas fa-power-off fa-fw mr-2"></i>LogOut</a></li>
    </ul>

</div>
