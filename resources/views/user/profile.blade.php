@extends('layouts.app')

@section('content')
    <user-profile :user_permissions='{{$user_permissions}}'></user-profile>
@stop
