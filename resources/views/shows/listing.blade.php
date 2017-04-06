@extends('layouts.main')
@section('content')
<form class="ajax" id="filter" action="GET">
     @include('shows.filter')
    <div class="row">
        @include('shows.ajax.items')
    </div>
</form>
@endsection
