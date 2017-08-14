@extends('layouts.main')
@section('meta_title', 'Seriálovna.cz | Televizní seriály ke shlédnutí zdarma')
@section('meta_description', 'Televizní seriály, nadcházející epizody, televizní kalendář')

@section('content')
<h1>@lang('strings.tv_shows')</h1>
<form class="ajax" id="filter" action="GET">
     @include('shows.filter')
    <div class="row">
        @include('shows.ajax.items')
    </div>
</form>
@endsection

