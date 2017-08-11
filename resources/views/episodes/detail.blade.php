@extends('layouts.main')
@section('meta_title', $show->translation($layout['lang'])->title . ' - ' . 'S' . $episode->season_number . 'E'. $episode->episode_number . ': ' . $episode->translation($layout['lang'])->meta_title)
@section('meta_description', $episode->translation($layout['lang'])->meta_description)

@section('content')

<div itemscope itemtype="http://schema.org/TVEpisode">
   
        <div class="parallax-container noise">
            <div class="parallax">
                @if($episode->thumb() !== null)
                <img src="{{ $episode->thumb()->src() }}" itemprop="image" alt="{{ $episode->translation($layout['lang'])->title }}">
                @else
                <img src="http://placehold.it/1/bbb/?text=+" alt="placeholder">
                @endif
            </div>
            @if($episode->prev() !== null)
            <a class="navigate prev" href="{{$episode->prev()->url($layout['lang'])}}"
               data-position="right"
               data-delay="50" 
               data-tooltip="Předchozí">
                <i class="material-icons">navigate_before</i>
            </a>
            @endif

            @if($episode->next() !== null)
            <a class="navigate next" 
               href="{{$episode->next()->url($layout['lang'])}}"
               data-position="left"
               data-delay="50" 
               data-tooltip="Další">
                <i class="material-icons">navigate_next</i>
            </a>
            @endif
            <div class="info">
                <h1 class="title" itemprop="name">{{$episode->translation($layout['lang'])->title}}</h1>
                @include('episodes.ajax.watched')
            </div>
        </div>
   

    <nav >
        <div class="nav-wrapper truncate">
            <div class="col s12">
                <a itemprop="partOfTVSeries" itemscope itemtype="http://schema.org/TVSeries" href="{{$show->url($layout['lang'])}}" class="breadcrumb">
                    <span itemprop="name">{{$show->translation($layout['lang'])->title}}</span>
                    <meta itemprop="url" content="{{$show->url($layout['lang'])}}">
                </a> 
                <a itemprop="partOfSeason" itemscope itemtype="http://schema.org/TVSeason" href="{{$show->url($layout['lang'])}}?season={{intval($episode->season_number)}}#season-{{intval($episode->season_number)}}" class="breadcrumb hide-on-small-only">
                    <span itemprop="name">Série&nbsp;{{ $episode->season_number }}</span>
                    <meta itemprop="url" content="{{$show->url($layout['lang'])}}?season={{intval($episode->season_number)}}#season-{{intval($episode->season_number)}}">
                </a>
                <span class="breadcrumb hide-on-med-and-down">S{{ $episode->season_number }}E<span itemprop="episodeNumber">{{ $episode->episode_number }}</span>&nbsp;-&nbsp;{{ $episode->translation($layout['lang'])->title }} </span>
            </div>
        </div>
    </nav>


    @if($episode->translation($layout['lang']) !== null)
    <div class="col s12 m12 l12">
        <p itemprop="description">{{ $episode->translation($layout['lang'])->content }}</p>
    </div>
    @endif

    <div class="row">
        <div class="col s12">
            @if( $show->network() !== null)
            <a href="{{$layout['lang_prefix']}}/networks/{{ $show->network()->slug }}" class="chip">
                <span itemprop="productionCompany" itemscope itemtype="http://schema.org/Organization">
                    <span itemprop="name">{{ $show->network()->translation()->value }}</span>
                    <meta itemprop="url" content="{{$layout['lang_prefix']}}/networks/{{ $show->network()->slug }}">
                </span>
                <i class="chip-icon material-icons">tv</i>
            </a>
            @endif
            <div class="chip">
                <span itemprop="datePublished" data-date="{{ $episode->first_aired }}">{{ $episode->first_aired }}</span>
                <i class="chip-icon material-icons">today</i>
            </div>
            <div class="chip">
                <span itemprop="timeRequired">{{ $show->runtime }}</span> min
                <i class="chip-icon material-icons">timer</i>
            </div>
        </div>
    </div>

    @if(isset($episode->imdb_id) || isset($episode->thetvdb_id))
    <h3>Více na</h3>
    @if(isset($show->imdb_id))
    <div class="col s12">
        <a href="http://www.imdb.com/title/{{$episode->imdb_id}}/" target="_blank">Imdb.com</a>
    </div>
    @endif
    @if(isset($show->thetvdb_id) && isset($show->thetvdb_id))
    <div class="col s12">
        <a href="http://thetvdb.com/?tab=episode&seriesid={{$show->thetvdb_id}}&id={{$episode->thetvdb_id}}" target="_blank" class="col s12">TheTvDb.com</a>
    </div>
    @endif
    @endif
    <h3>Komentáře</h3>
    @include('components.comments.form', ['model' => $episode])
    @include('components.comments.display', ['comments' => $episode->comments])
</div>
@endsection


