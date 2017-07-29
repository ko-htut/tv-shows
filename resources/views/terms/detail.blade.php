@extends('layouts.main')

@section('meta_title', $term->translation('en')->title)
@section('meta_description', $term->translation('en')->description)
@section('page_title', $term->translation('en')->title)

@section('content')
<form class="ajax" id="filter" action="GET">
    <div class="row">
        @include('shows.ajax.items')
    </div>
</form>
@endsection