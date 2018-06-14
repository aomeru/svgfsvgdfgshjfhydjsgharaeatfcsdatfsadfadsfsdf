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
        <link href="{{ asset('css/datatables.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/user.css') }}" rel="stylesheet">
    </head>

    <body>

        <div id="app">
            <div id="portal">
                @include('partials.portal.nav')
                @include('partials.portal.main')
            </div>
        </div>

        @yield('page_footer')
        <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
        <script src="{{ asset('js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/datatables.min.js') }}"></script>
        <script src="{{ asset('js/select2.min.js') }}"></script>
        <script src="{{ asset('js/sweetalert.min.js') }}"></script>
        <script src="{{ asset('js/user.js') }}" defer></script>

        <script>
            function getErrorMessage(jqXHR, exception)
            {
                var msg = '';
                if (jqXHR.responseJSON) {
                    var errors = (jqXHR.responseJSON.errors);
                    $.each(errors, function(key, value){
                        msg = value[0];
                    })
                } else if(jqXHR['errors']) {
                    msg = jqXHR['errors'];
                } else if (jqXHR.status === 0) {
                    msg = 'Not connect.\n Verify Network. <br>Please Contact Support Team.';
                } else if (jqXHR.status == 404) {
                    msg = 'Requested page not found. [404]. <br>Please Contact Support Team.';
                } else if (jqXHR.status == 500) {
                    msg = 'Internal Server Error [500]. <br>Please Contact Support Team.\n' + jqXHR.responseText;
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed. <br>Please Contact Support Team.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error';
                } else if (exception === 'abort') {
                    msg = 'Request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                return msg;
            }

            function swal_alert(title,message,icon,button='')
            {
                swal(title,message,icon, {
                    title :  title,
                    text :  message,
                    icon :  icon,
                    button : button ? button : false,
                });
            }
        </script>

        @yield('scripts')

    </body>
</html>
