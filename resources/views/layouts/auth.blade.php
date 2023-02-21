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
    <style>
        #wrapper {display: block;}
    </style>
</head>
<body class="bg-gradient-primary">
<div id="wrapper" class="container" >
    @yield('content')
</div>
<script src="{{asset('/vendor/jquery/jquery.min.js')}}"></script>
<script src="{{asset('/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('/js/sb-admin-2.min.js')}}"></script>
<script src="{{asset('/js/app.js')}}"></script>
<script src="{{asset('/vendor/toastr/toastr.min.js')}}"></script>
<script>
    function date_time(id) {let date = new Date;let year = date.getFullYear();let month = date.getMonth();let months = new Array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Decembre');let d = date.getDate();let day = date.getDay();let days = new Array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');let h = date.getHours();if(h<10) {h = "0"+h;}let m = date.getMinutes();if(m<10) {m = "0"+m;}let s = date.getSeconds();if(s<10){s = "0"+s;}let result = ''+days[day]+', '+d+' '+months[month]+' '+year+' <br/> '+h+':'+m+':'+s;document.getElementById(id).innerHTML = result;setTimeout('date_time("'+id+'");','1000');return true;}
    date_time('date_time');
</script>
@include('messages.toast')
</body>
</html>
