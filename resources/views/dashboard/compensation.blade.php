@extends('layouts.app')

@section('content')
<compensation :geo='{{ json_encode($geo) }}'></compensation>
@stop
