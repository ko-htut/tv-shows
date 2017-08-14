@extends('layouts.main')

@section('meta_title', __('terms.'.$term->translation()->slug) )
@section('meta_description', __('terms.'.$term->translation()->slug) )
@section('page_title',  __('terms.'.$term->translation()->slug) )

@section('content')
<h1>{{ __('terms.'.$term->translation()->slug) }}</h1>
<form class="ajax" id="filter" action="GET">
    <div class="row">
        @include('shows.ajax.items')
    </div>
</form>
@endsection