@extends('layouts.app')

@section('content')
    <user-manage :user_permissions='{{$user_permissions}}' :user_profiles='{{$user_profiles}}'></user-manage>
@stop
