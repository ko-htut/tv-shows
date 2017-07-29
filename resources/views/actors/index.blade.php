@extends('layouts.main')
@section('meta_title', 'Seriálovna.cz :: Herci ')
@section('meta_description', 'Herci v televizních seriálech')
@section('content')
<h1>Herci v seriálech</h1>

<div id="snippet-wrapper">
    <form class="ajax" id="filter" action="GET">
        <div class="row">
            @foreach ($actors as $actor)
                @include('actors.list-item')
            @endforeach
        </div>
    </form>
    <div class="row">
        <div class="col s12 center">
            {{$actors->links()}}
        </div>
    </div>
</div>
@endsection
