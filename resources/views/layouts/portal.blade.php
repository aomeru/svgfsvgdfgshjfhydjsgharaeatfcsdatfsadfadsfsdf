<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('page_title'){{ config('app.name') }}</title>
        <link rel="shortcut icon" href="{{asset('images/favicon.png')}}" />
        <link href="{{ asset('css/fontawesome-all.css') }}" rel="stylesheet">
        <link href="{{ asset('css/datatables.min.css') }}" rel="stylesheet">
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
        <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
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
                        msg = msg + value[0];
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

            function palert(msg,type) {
                var elem = $('#alertDiv');
                elem.toggleClass('alert');
                if(type == 'success') {
                    elem.toggleClass('alert-success').html('<i class="fas fa-check mr-3"></i>' + msg);
                } else {
                    var con = '';
                    $.each(msg.responseJSON.errors, function(key, value){
                        con = con + '<i class="fas fa-times mr-2"></i>' + value[0] + '<br>';
                    });
                    elem.toggleClass('alert-danger').html(con);
                }
            }

            function swal_alert(title,message,icon,button='',timer='')
            {
                swal({
                    title :  title,
                    text :  message,
                    icon :  icon,
                    button : button ? button : false,
                    animation : false,
                    timer : timer
                });
            }

            function get_slug(t)
            {
                slug = t.toLowerCase().replace(/  /g, ' ').replace(/ /g, '-').replace(/&/g, 'and');
                return slug;
            }

            $(document).ready(function(){
                $(document).on('click','.notif-item',function(e){
                    e.preventDefault();
                    var elem = $(this),
                        nid = elem.data('id'),
                        nurl = elem.data('url'),
                        load_element = "#notif-div",
                        token ='{{ Session::token() }}';

                    $.ajax({
                        type: "POST",
                        url: '{{ route('read.notif') }}',
                        data: {
                            id: nid,
                            _token: token
                        },
                        success: function(response) {
                            $(load_element).load(location.href + " "+ load_element +">*","");
                            if(nurl !== '') window.location.href = nurl;
                        }
                    });
                });
            });
        </script>

        @yield('scripts')

    </body>
</html>
