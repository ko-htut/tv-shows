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

@endsection


