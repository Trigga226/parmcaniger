@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <profile-edit :save="'{{ route('update-profile') }}'" :user='{{ json_encode($user) }}'></profile-edit>
    </div>
@stop