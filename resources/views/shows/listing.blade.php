@extends('layouts.main')
@section('meta_title', 'Seriálovna.cz :: Televizní seriály ke shlédnutí zdarma ')
@section('meta_description', 'Televizní seriály, nadcházející epizody, televizní kalendář')
@section('page_title', 'Seriály' )

@section('content')
<form class="ajax" id="filter" action="GET">
     @include('shows.filter')
    <div class="row">
        @include('shows.ajax.items')
    </div>
</form>
@endsection
