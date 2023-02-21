@extends('layouts.app')

@section('content')
<dashboard :paps_nbr='{{ $paps }}' :locations_nbr='{{ $locations }}' :communes_nbr='{{ $communes }}' :stats_data='{{ $stats }}'></dashboard>
@stop
