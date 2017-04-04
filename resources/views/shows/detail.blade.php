@extends('layouts.main')
@section('content')

<div class="row">
    <img class="responsive-img" src="{{ $show->banner()->external_patch }}">
    <div class="col s12"><h1>{{ $show->translation($lang)->title }}</h1></div>
    <div class="col s12 m12 l12"><p>{{ $show->translation($lang)->content }}</p></div>
</div>

<div class="row">
    <ul class="collection">
        @foreach ($show->episodes as $episode)
        <li class="collection-item"><i>S{{ $episode->season_number }}E{{ $episode->episode_number }}</i> - <strong>{{ $episode->translation($lang)->title }}</strong></li>

        @endforeach  
    </ul>
</div>
@endsection
