@extends('layouts.main')

@section('meta_title', $network->translation('en')->value)
@section('meta_description', $network->translation('en')->value)
@section('page_title', $network->translation('en')->value )
@section('content')
<form class="ajax" id="filter" action="GET">
    <div class="row">
        @include('shows.ajax.items')
    </div>
</form>
@endsection