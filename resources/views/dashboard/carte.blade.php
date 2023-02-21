@extends('layouts.app')

@section('content')
<carte :cartes_data='{{ $cartes }}'></carte>
@stop
