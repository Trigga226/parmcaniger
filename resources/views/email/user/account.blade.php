@extends('email.master')

@section('title')
    OSAAS
@stop

@section('content')
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
        <tr>
            <td>&nbsp;</td>
            <td class="container">
                <div class="content">
                    <table role="presentation" class="main">
                        <tr>
                            <td class="wrapper">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>
                                            <p>Bonjour {{ $data['user']->name }}</p>
                                            @if($data['action'] == 'store')
                                                <p>Les administrateurs de OSAAS viennent de vous créer un nouveau compte.</p>
                                                <p>Votre compte est : {{ $data['user']->email }}</p>
                                                <p>Votre mot de passe est : {{ $data['password'] }}</p>
                                                <p>Le statut de votre compte est : {{ status($data['user']->status_pritn) }}</p>
                                            @elseif($data['action'] == 'update')
                                                <p>Les administrateurs de OSAAS viennent de mettre à jour votre compte.</p>
                                                <p>Votre compte est : {{ $data['user']->email }}</p>
                                                @if($data['password'])
                                                    <p>Votre nouveau mot de passe est : {{ $data['password'] }}</p>
                                                @endif
                                                <p>Le statut de votre compte est : {{ status($data['user']->statu_pritns) }}</p>
                                            @elseif($data['action'] == 'delete')
                                                <p>Les administrateurs de OSAAS viennent de supprimer votre compte.</p>
                                                <p>Pour plus d'informations contacter les administrateurs.</p>
                                                <p>À bientôt...</p>
                                            @endif
                                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                                                <tbody>
                                                <tr>
                                                    <td align="left">
                                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                                            <tbody>
                                                            <tr>
                                                                <td> <a href="{{ url('/') }}" target="_blank">Connectez-vous maintenant</a> </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <div class="footer">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="content-block">
                                    <span class="apple-link">OSAAS</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="content-block powered-by">
                                    Par <a href="#">OSAAS</a>.
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </td>
            <td>&nbsp;</td>
        </tr>
    </table>
@stop
