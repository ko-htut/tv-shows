@extends('layouts.main')
@section('meta_title', 'Seriálovna.cz :: Kalendář ')
@section('meta_description', 'Nadcházející televizní epizody')
@section('content')

<h1>Nadcházející televizní epizody</h1>


<?php $lastDate = null; ?>
@foreach ($episodes as $episode)

<?php $cDate = $episode->first_aired; ?>

@if($cDate != $lastDate)

@if($lastDate !== null)</div>@endif
<div class="row">
    
    <div class="row">
         <div class="col s12">
              <span data-date="{{$cDate}}">{{$cDate}}</span>
        </div>
    </div>
    @endif

    <div class="col s6 m4 l3 calendar-item" itemscope itemtype="http://schema.org/TVEpisode">
        <meta itemprop="name" content="{{$episode->translation($layout['lang'])->title}}">
        <meta itemprop="alternateName" content="{{$episode->number()}}">
        <meta itemprop="datePublished" content="{{$episode->first_aired}}">
        <meta itemprop="episodeNumber" content="{{intval($episode->episode_number)}}">
        <div class="card">
            <div class="card-image">
                <a href="{{$episode->url($layout['lang'])}}">
                    @if($episode->show->fanart() !== null)
                    <div class="focus">
                        <img itemprop="" src="{{ $episode->show->fanart()->getSrc(374)}}" class="focus" alt="{{$episode->show->translation($layout['lang'])->title}} fanart">
                    </div>
                    @endif
                </a>
            </div>
            <div class="card-content">
                <a href="{{$episode->url($layout['lang'])}}" class="episode-number" itemprop="url">{{$episode->number()}}</a>
                <span itemprop="partOfTVSeries" itemscope itemtype="http://schema.org/TVSeries">
                    <meta itemprop="name" content="{{$episode->show->translation($layout['lang'])->title}}">
                    <a itemprop="url"  href="{{$episode->show->url($layout['lang'])}}" class="show-name" itemprop="url">{{$episode->show->translation($layout['lang'])->title}}</a>
                </span>
                <hr>
                <div class="more">
                    <span class="air-time" itemprop="">{{$episode->show->air_time}}</span>
                    na
                    <a href="{{$layout['lang_prefix']}}/networks/{{ $episode->show->network()->slug }}" class="network" itemprop="">{{ $episode->show->network()->translation()->value }}</a>
                </div>
            </div>
        </div>
    </div>

<?php $lastDate = $episode->first_aired; ?>

@endforeach    
</div>

@endsection
