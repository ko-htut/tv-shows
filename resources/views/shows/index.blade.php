@extends('layouts.main')
@section('meta_title', 'Seriálovna.cz :: Televizní seriály ke shlédnutí zdarma ')
@section('meta_description', 'Televizní seriály, nadcházející epizody, televizní kalendář')
@section('content')
<!-- index -->
<h1>Televizní seriály</h1>
<form class="ajax" id="filter" action="GET">
     @include('shows.filter')
    <div class="row">
        @include('shows.ajax.items')
    </div>
</form>
@endsection

