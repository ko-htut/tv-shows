@extends('layouts.main')

@section('meta_title', $show->translation($layout['lang'])->meta_title)
@section('meta_description', $show->translation($layout['lang'])->meta_description)
@section('page_title', $show->translation($layout['lang'])->title)

@section('content')


@if($show->fanart() !== null)
<div class="row" itemscope itemtype="http://schema.org/TVSeries">
    <div class="parallax-container center-block" style="max-width: 1280px;">
        <div class="parallax"><img src="{{ $show->fanart()->getSrc(1187) }}"></div>
    </div>
</div>
@endif
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


<h3>Série</h3>
<ul class="collapsible" data-collapsible="accordion">
    @for ($i = $show->lastSeason(); $i >= $show->firstSeason(); $i--)
    <li itemprop="containsSeason" itemscope itemtype="http://schema.org/TVSeason" id="season-{{ $i }}">
        <div class="ajax collapsible-header @if($seasonNum == $i) active @endif " href="?season={{ $i }}"><span class="new badge" data-badge-caption="episodes" itemprop="numberOfEpisodes">{{ $show->seasonEpisodesCount($i) }}</span>
            <a href="?season={{ $i }}" class="ajax collection-item"><span itemprop="name">Season {{ $i }}</span></a>
        </div>
        <div class="collapsible-body">
            @if($seasonNum == $i)
            @include('shows.ajax.season')
            @else
            <div id="snippet-season-{{ $i }}"></div>
            @endif
        </div>
    </li>
    @endfor
</ul>

@if(count($show->actors()) > 0)
<h3>Herci</h3>
<div class="row truncate">
    @foreach ($show->actors() as $actor)
    <a href="{{ $actor->url($layout['lang'])}}" class="actor col s4 m2 l2">
        <div class="card actor">
            <div class="card-image">
                @if($actor->thumb() !== null)
                <img src="{{ $actor->thumb()->src() }}"  alt="{{$actor->name}} ">
                @endif
            </div>
            <div class="card-content">
                <span class="card-title truncate">{{$actor->role}}&nbsp;</span>
                <span class="card-title truncate">{{$actor->name}}&nbsp;</span> 
            </div>
        </div>
    </a>
    @endforeach
</div>
@endif
<h3>Komentáře</h3>
@include('components.comments.form', ['model' => $show])
@include('components.comments.display', ['comments' => $show->comments])
@endsection


