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
                                            <p>Cher(e) {{ $data['name']}},</p>
                                            <p>Trouvez en pièce jointe votre facture  s'élevant à {{$data['total']}} FCFA de {{$data['company']}}.</p>
                                            <p>N'hésitez pas à nous contacter si vous avez des questions.</p>
                                            <p>Cordialement.</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <div class="footer">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
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
