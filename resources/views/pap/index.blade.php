@extends('layouts.app')

@section('content')
<pap :geo='{{ json_encode($geo) }}'></pap>
@stop
