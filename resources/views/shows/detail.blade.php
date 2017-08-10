@extends('layouts.main')
@section('meta_title', $show->translation($layout['lang'])->meta_title)
@section('meta_description', $show->translation($layout['lang'])->meta_description)
@section('content')
<div class="row" itemscope itemtype="http://schema.org/TVSeries">
    <div class="parallax-container center-block" style="max-width: 1280px;">
        <div class="parallax">
            @if($fanart !== null)
            <img src="{{ $fanart->getSrc(1187) }}">
            @else
            <img src="http://placehold.it/1/bbb/?text=+">
            @endif
        </div>
        <div class="info">
            <h1 class="title">{{$show->translation($layout['lang'])->title}}</h1>
            @include('shows.ajax.favourite')
        </div>
    </div>
</div>


<div class="row">
    <div class="col s12 m12 l12">
        @foreach ($show->genres as $genre)
        <a href="{{$layout['lang_prefix']}}/genres/{{ $genre->translation()->slug }}" class="chip">
            {{ $genre->translation()->title }}
        </a>
        @endforeach
        @if( $show->network() !== null)
        <a href="{{$layout['lang_prefix']}}/networks/{{ $show->network()->slug }}" class="chip">
            {{ $show->network()->translation()->value }}
            <i class="chip-icon material-icons">tv</i>
        </a>
        @endif
        <div class="chip">
            {{ $show->runtime }} min
            <i class="chip-icon material-icons">timer</i>
        </div>
        <div class="chip">
            {{ $show->first_aired }} @if($show->last_aired)- {{ $show->last_aired }}@endif
        </div>
    </div>
</div>

<div class="col s12 m12 l12">
    <p itemprop="description">{{ $show->translation($layout['lang'])->content }}</p>
</div>

@if($show->lastSeason() !== null && $show->firstSeason() !== null)
<h3>Série</h3>
<ul class="collapsible" data-collapsible="accordion">
    @for ($i = $show->lastSeason(); $i >= $show->firstSeason(); $i--)
    @if($show->seasonEpisodesCount($i) > 0)
    <li itemprop="containsSeason" itemscope itemtype="http://schema.org/TVSeason" id="season-{{ $i }}">
        <div class="ajax collapsible-header @if($seasonNum == $i) active @endif " href="?season={{ $i }}"><span class="new badge" data-badge-caption="epizod" itemprop="numberOfEpisodes">{{ $show->seasonEpisodesCount($i) }}</span>
            <a href="?season={{ $i }}" class="ajax collection-item"><span itemprop="name">Série {{ $i }}</span></a>
        </div>
        <div class="collapsible-body">
            @if($seasonNum == $i)
            @include('shows.ajax.season')
            @else
            <div id="snippet-season-{{ $i }}"></div>
            @endif
        </div>
    </li>
    @endif
    @endfor
</ul>
@endif

<h3>Galerie</h3>
<div id="gallery">
    @foreach($gallery as $fanart)
    <a href="{{$fanart->src()}}">
        Otevřít galerii
    </a>
    @endforeach
</div>

<h3>Herci</h3>
<div class="row truncate">
    @foreach ($show->actorsLimit() as $actor)
        @include('actors.list-item')
    @endforeach
</div>

<h3>Více na</h3>
@if(isset($show->imdb_id))
<div class="col s12">
    <a href="http://www.imdb.com/title/{{$show->imdb_id}}/" target="_blank">Imdb.com</a>
</div>
@endif
@if(isset($show->thetvdb_id))
<div class="col s12">
    <a href="http://thetvdb.com/?tab=series&id={{$show->thetvdb_id}}" target="_blank" class="col s12">TheTvDb.com</a>
</div>
@endif

<h3>Komentáře</h3>
@include('components.comments.form', ['model' => $show])
@include('components.comments.display', ['comments' => $show->comments])
@endsection


