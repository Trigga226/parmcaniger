@extends('layouts.app')

@section('content')
<bmap :paps_data='{{ $paps }}'></bmap>
@stop
