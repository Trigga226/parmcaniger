@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="header text-center">
                    <h4 class="title"><i class="fa fa-edit"></i> Pour votre première connexion vous devez mettre à jour votre mote de passe</h4>
                </div>
                <section class="content">
                    <form action="{{ route('first-login-store') }}" method="post">
                        @include('messages.list')
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Nouveau mot de passe </label>
                                        <input type="password" name="password" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Confirmer le mot de passe </label>
                                        <input type="password" name="password_confirmation" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" data-rel="tooltip" data-placement="top" title="Mètre à jour" class="btn btn-warning btn-fill"><i class="fa fa-save"></i> Mètre à jour</button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>
@stop
