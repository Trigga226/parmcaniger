<!DOCTYPE html>
<html lang="{{config('app.locale')}}">
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" type="image/png" sizes="96x96" href="img/favicon-96x96.png">
        <title>{{config('app.name')}}</title>
        <link href="{{asset('/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
        <link href="{{asset('/css/sb-admin-2.min.css')}}" rel="stylesheet">
        <link href="{{asset('/css/custom.css')}}" rel="stylesheet">
        <link href="{{asset('/vendor/toastr/toastr.css')}}" rel="stylesheet">
        <link href="{{asset('/vendor/sweetalert/sweetalert.css')}}" rel="stylesheet">
        <link href="{{asset('/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
        <script>
            @auth
                @php
                    $permission_array = [];
                    foreach (Auth::user()->profiles[0]->permissions as $permission){
                        array_push($permission_array,$permission->slug);
                    }
                    $role_array = [];
                    foreach (Auth::user()->profiles as $role){
                        array_push($role_array,$role->slug);
                    }
                @endphp
                window.userPermissions = {!! json_encode($permission_array, true) !!};
                window.userRoles = {!! json_encode(Auth::user()->profiles) !!};
            @else
                window.userPermissions = [];
                window.userRoles = [];
            @endauth
        </script>

    </head>
    <body id="page-top">
        <div id="wrapper">
            @include('layouts.partials.sidebar')
            <div id="content-wrapper" class="d-flex flex-column">
                <div id="content">
                    @include('layouts.partials.nav')
                    @yield('content')
                    <vue-progress-bar></vue-progress-bar>
                </div>
                <footer class="sticky-footer bg-white">
                    <div class="container my-auto">
                        <div class="copyright text-center my-auto">
                            <span>Copyright &copy; {{config('app.name')}} {{date('Y')}}</span>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>
        <script src="{{asset('/vendor/jquery/jquery.min.js')}}"></script>
        <script src="{{asset('/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
        <script src="{{asset('/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

        <script src="{{mix('/js/app.js')}}"></script>
        <script src="{{asset('/vendor/toastr/toastr.min.js')}}"></script>
        <script src="{{asset('/vendor/sweetalert/sweetalert.min.js')}}"></script>
        <script src="{{asset('/js/sb-admin-2.min.js')}}"></script>
        <script src="{{asset('/vendor/datatables/jquery.dataTables.min.js')}}"></script>
        <script src="{{asset('/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
        <script>
            $(document).ready(function() {
                $('.dataTable').DataTable();
            });
            function date_time(id) {let date = new Date;let year = date.getFullYear();let month = date.getMonth();let months = new Array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Decembre');let d = date.getDate();let day = date.getDay();let days = new Array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');let h = date.getHours();if(h<10) {h = "0"+h;}let m = date.getMinutes();if(m<10) {m = "0"+m;}let s = date.getSeconds();if(s<10){s = "0"+s;}let result = ''+days[day]+', '+d+' '+months[month]+' '+year+' / '+h+':'+m+':'+s;document.getElementById(id).innerHTML = result;setTimeout('date_time("'+id+'");','1000');return true;}
            date_time('date_time');
        </script>
        @include('messages.toast')
    </body>
</html>
