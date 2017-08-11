@extends('layouts.main')

@section('meta_title', $network->translation('en')->value)
@section('meta_description', $network->translation('en')->value)
@section('content')
<h1>{{$network->translation('en')->value}}</h1>
<form class="ajax" id="filter" action="GET">
    <div class="row">
        @include('shows.ajax.items')
    </div>
</form>
@endsection