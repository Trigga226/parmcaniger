@extends('layouts.auth')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-6 col-md-6 text-center">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0 ">
                    <img src="{{asset('/img/mca.jpg')}}" style="height:745px;" alt="">
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-6 text-center">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0 ">
                    <img class="text-center" src="{{asset('/img/logo.png')}}" width="200" style="œmargin-top:30px;" alt="">
                    <h2>COMPACT-NIGER</h2>
                    <h3><strong>Projet irrigation et accès aux marchés</strong></h3>
                    <h4><strong>Plan d'action de réinstallation des routes :</strong></h4>
                    <h4><strong>RN7, RN35 et RRSambera</strong></h4>
                    <div class="row">
                        <div class="col-lg-8 offset-2 text-center">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4"><span id="date_time"></span></h1>
                                </div>
                                <auth-login :auth="'{{ route('store-login') }}'" :redirect="'{{ route('index-dashboard') }}'"></auth-login>
                                <hr>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
