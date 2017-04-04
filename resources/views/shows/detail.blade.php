@extends('layouts.main')
@section('content')

<div class="row">
    <img class="responsive-img" src="{{ $show->banner()->external_patch }}">
    <div class="col s12"><h1>{{ $show->translation($lang)->title }}</h1></div>
    <div class="col s12 m12 l12"><p>{{ $show->translation($lang)->content }}</p></div>
</div>

<div class="row">
    <ul class="collection">
        @for ($i = 1; $i < $show->lastSeason()+1; $i++)
            <li class="collection-item"><strong>Season {{ $i }}</strong></li>
        @endfor
    </ul>
</div>

<div class="row">
    <ul class="collection">
        @foreach ($show->allEpisodes as $episode)
        <li class="collection-item"><i>S{{ $episode->season_number }}E{{ $episode->episode_number }}</i> - <strong>{{ $episode->translation($lang)->title }}</strong></li>
        @endforeach  
    </ul>
</div>

<div class="row">
    <ul class="collection">
        @foreach ($show->genres as $genre)
            <li class="collection-item"><strong>{{ $genre->translation()->title }}</strong></li>
        @endforeach  
    </ul>
</div>
@endsection
