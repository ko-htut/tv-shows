@extends('layouts.main')

@section('meta_title', $actor->name)
@section('meta_description', $actor->name)

@section('content')
<div itemscope itemtype="http://schema.org/Person">
    <h1 itemprop="name">{{$actor->name}}</h1>
    <meta itemprop="jobTitle" content="Actor">
    <div class="row">
        <div class="col s12 m12 l2 center-align">
            @if($actor->thumb() !== null)
            <img itemprop="image" src="{{$actor->thumb()->src() }}" alt="{{$actor->name}}" class="responsive-img">
            @else
            <img src="{{$actor->placeholder()}}" alt="placeholder" class="responsive-img">
            @endif
            <div id="gallery">
                @foreach($actors as $a)
                @if($a->thumb() !== null)
                <a href="{{$a->thumb()->src()}}">
                    Gelerie
                </a>
                @endif
                @endforeach
            </div>
        </div>
        <div class="col s12 m12 l10">
            <h3>V seriálech</h3>
            @foreach($shows as $show)
            @include('shows.list-item',  ['detail' => true])
            @endforeach
            <div class="col s12">
                <h3>Více na</h3>
                <a href="http://www.imdb.com/find?s=nm&q={{urlencode($actor->name)}}" target="_blank">Imdb.com</a>
            </div>
            <div class="col s12">
                <h3>Komentáře</h3>
                @include('components.comments.form', ['model' => $actor])
                @include('components.comments.display', ['comments' => $actor->comments])
            </div>
        </div>
    </div>
</div>
@endsection