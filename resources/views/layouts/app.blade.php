<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page_title'){{ config('app.name') }}</title>
    <link rel="shortcut icon" href="{{asset('images/favicon.png')}}" />
    <link href="{{ asset('css/fontawesome-all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>

    <div id="app">
        <nav class="navbar navbar-expand-sm navbar-light navbar-laravel">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ asset('images/brand-name-w.png') }}" alt="" class="img-fluid">
                </a>

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="btn btn-light btn-sm d-none d-sm-block br6 px-3" href="{{ route('login') }}"><i class="fab fa-microsoft pr-2"></i>{{ __('Login') }}</a>
                                <a class="nav-link d-block d-sm-none" href="{{ route('login') }}"><i class="fab fa-microsoft pr-2"></i>{{ __('Login') }}</a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('portal')}}"><i class="fas fa-home fa-fw mr-2"></i>Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('logout') }}"><i class="fas fa-power-off fa-fw mr-2"></i>{{ __('Logout') }}</a>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <script src="{{ asset('js/app.js') }}" defer></script>

</body>
</html>
