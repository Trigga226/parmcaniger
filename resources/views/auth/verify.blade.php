@extends('layouts.app')

@section('content')
    <div class="col-md-12 content-center">
        <div class="row" style="margin-top: 150px;">
            <div class="col-sm-3"></div>
            <div class="col-sm-6">
                <div class="card-plain">
                    <div class="body">
                        <div class="card-header">{{ __('Vérifiez votre adresse e-mail') }}</div>
                        <div class="card-body">
                            @if (session('resent'))
                                <div class="alert alert-success" role="alert">
                                    {{ __('Un nouveau lien de vérification a été envoyé à votre adresse e-mail.') }}
                                </div>
                            @endif
                            {{ __('Avant de continuer, veuillez vérifier votre courrier électronique pour un lien de vérification.') }}
                            {{ __('Si vous n\'avez pas reçu l\'email') }}, <a href="{{ route('resend-verification') }}">{{ __('cliquez ici pour demander un autre') }}</a>.
                        </div>
                    </div>
                    <div class="footer">
                        <a href="{{ route('destroy-login') }}" class="btn btn-primary btn-round btn-block">DÉCONNEXION</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-3"></div>
        </div>
    </div>
@endsection
