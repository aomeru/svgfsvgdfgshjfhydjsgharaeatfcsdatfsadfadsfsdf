<div class="p-3" id="portal-left">
    <div class="row d-none d-sm-block">
        <div class="d-flex justify-content-center">
            <div class="col-9">
                <a class="" href="{{ url('/') }}">
                    <img src="{{ asset('images/brand-name.png') }}" alt="" class="img-fluid">
                </a>
            </div>
        </div>
    </div>

    <hr class="mb-4 d-none d-sm-block">
    @if(session()->has('userinfo'))
    <div class="d-flex justify-content-start">
        <div class="mr-2 d-flex align-items-center" style="height: 70px">
            <img src="@if(!session('userinfo.photo')) {{ asset('images/user.png') }} @else data:image.jpg;base64,{{session('userinfo.photo')}} @endif" class="img-fluuid rounded-circle border border-cyan" alt="" width="auto" height="100%">
        </div>
        <div class="d-flex align-items-center">
            <div class="">
                <h4 class="mb-0">
                    {{session('userinfo.display_name')}}
                </h4>
                <p class="mb-0 c-666">
                    <em>{{session('userinfo.job_title') ? session('userinfo.job_title') : 'Job Title'}} / {{session('userinfo.dept') ? session('userinfo.dept') : 'Department'}}</em>
                </p>
            </div>
        </div>
    </div>
    @endif
    <hr class="mb-4">

    <ul class="nav flex-column px-2">
        <li class="nav-item"><a href="" class="nav-link lactive"><i class="fas fa-desktop fa-fw mr-2"></i>Dashboard</a></li>

        <li class="nav-item">
            <a class="nav-link has-sub-nav"><i class="far fa-calendar-alt fa-fw mr-2"></i>Leave</a>
            <ul id="testleave" class="sub-nav flex-column ml-4 pl-4">
                <li class="sub-nav-item"><a href="" class="sub-nav-link"><i class="far fa-calendar-plus fa-fw mr-2"></i>Apply for Leave</a></li>
                <li class="sub-nav-item"><a href="" class="sub-nav-link"><i class="far fa-calendar-alt fa-fw mr-2"></i>My Leave</a></li>
                <li class="sub-nav-item"><a href="" class="sub-nav-link"><i class="far fa-calendar-check fa-fw mr-2"></i>Leave Approvals</a></li>
                <li class="sub-nav-item"><a href="" class="sub-nav-link"><i class="far fa-calendar fa-fw mr-2"></i>Leave Management</a></li>
            </ul>
        </li>

        <li class="nav-item"><a href="" class="nav-link"><i class="fas fa-chart-line fa-fw mr-2"></i>KPI Objectives</a></li>

        <li class="nav-item">
            <a class="nav-link has-sub-nav sub-active"><i class="far fa-user-circle fa-fw mr-2"></i>Users</a>
            <ul id="testuser" class="sub-nav flex-column ml-4 pl-4">
                <li class="sub-nav-item"><a href="" class="sub-nav-link active"><i class="fas fa-users fa-fw mr-2"></i>All Users</a></li>
                <li class="sub-nav-item"><a href="" class="sub-nav-link"><i class="fas fa-user-tie fa-fw mr-2"></i>Managers</a></li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link has-sub-nav"><i class="fas fa-shield-alt fa-fw mr-2"></i>Roles & Permissions</a>
            <ul id="testuser" class="sub-nav flex-column ml-4 pl-4">
                <li class="sub-nav-item"><a href="" class="sub-nav-link"><i class="fas fa-user-shield fa-fw mr-2"></i>Roles</a></li>
                <li class="sub-nav-item"><a href="" class="sub-nav-link"><i class="fas fa-lock fa-fw mr-2"></i>Permissions</a></li>
                <li class="sub-nav-item"><a href="" class="sub-nav-link"><i class="fas fa-unlock-alt fa-fw mr-2"></i>Grant Access</a></li>
            </ul>
        </li>

        <div class="dropdown-divider"></div>

        <li class="nav-item"><a href="{{ route('home') }}" class="nav-link"><i class="fas fa-home fa-fw mr-2"></i>Homepage</a></li>

        <li class="nav-item"><a href="{{ route('logout') }}" class="nav-link nav-link-red"><i class="fas fa-power-off fa-fw mr-2"></i>LogOut</a></li>
    </ul>

</div>
