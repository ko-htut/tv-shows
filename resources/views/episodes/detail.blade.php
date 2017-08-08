@extends('layouts.main')
@section('meta_title', $episode->translation($layout['lang'])->meta_title)
@section('meta_description', $episode->translation($layout['lang'])->meta_description)
@section('page_title', $episode->translation($layout['lang'])->title)


@section('content')


@if($episode->thumb() !== null)
<div class="row" itemscope itemtype="http://schema.org/TVSeries">
    <div class="parallax-container noise">
        <div class="parallax"><img src="{{ $episode->thumb()->src() }}"></div>
    </div>
</div>
@endif

@if($episode->translation($layout['lang']) !== null)
<div class="col s12 m12 l12">
    <p itemprop="description">{{ $episode->translation($layout['lang'])->content }}</p>
</div>
@endif

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
@endsection


