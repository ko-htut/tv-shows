@extends('layouts.main')

@section('meta_title', $show->translation($layout['lang'])->title)

@section('content')
<div class="row" itemscope itemtype="http://schema.org/TVSeries">

    <div class="swiper-container">
        <div class="swiper-wrapper">
            @foreach ($show->fanarts() as $fanart)
            <div class="swiper-slide">  
                <img class="responsive-img" src="{{ $fanart->src() }}">
            </div>
            @endforeach  
        </div>
        <!-- Add Pagination -->
        <div class="swiper-pagination"></div>
    </div>

    <!-- Initialize Swiper -->
    <script>
        var swiper = new Swiper('.swiper-container', {
            pagination: '.swiper-pagination',
            paginationClickable: true
        });
    </script>



    <div class="col s12"><h1 itemprop="name">{{ $show->translation($layout['lang'])->title }}</h1></div>

    <div class="row">
        @foreach ($show->genres as $genre)
        <div class="chip">
            {{ $genre->translation()->title }}
        </div>
        @endforeach  
        <div class="chip">
            {{ $show->network()->translation()->value }}
            <i class="chip-icon material-icons">tv</i>
        </div>

        <div class="chip">
            {{ $show->runtime }} min
            <i class="chip-icon material-icons">timer</i>
        </div>
        <div class="chip">
            {{ $show->first_aired }}
            <i class="chip-icon material-icons">cake</i>
        </div>
    </div>

    <div class="col s12 m12 l12"><p itemprop="description">{{ $show->translation($layout['lang'])->content }}</p></div>
</div>

<h3>Seasons</h3>
<ul class="collapsible" data-collapsible="accordion">
    @for ($i = 1; $i < $show->lastSeason()+1; $i++)  
    <li itemprop="containsSeason" itemscope itemtype="http://schema.org/TVSeason">
        <div class="ajax collapsible-header" href="?season={{ $i }}"><span class="new badge" data-badge-caption="episodes" itemprop="numberOfEpisodes">{{ $show->seasonEpisodesCount($i) }}</span>
            <a href="?season={{ $i }}" class="ajax collection-item"><span itemprop="name">Season {{ $i }}</span></a>
        </div>
        <div class="collapsible-body">
            <div id="snippet-season-{{ $i }}">

            </div>
        </div>
    </li>
    @endfor
</ul>


<h3>Actors</h3>
<ul class="collection">
    @foreach ($show->actors() as $actor)
    
    <li class="collection-item avatar" itemprop="actor" itemscope itemtype="http://schema.org/Person">
        @if($actor->thumb() !== null) 
            <img src="{{$actor->thumb()->src()}}" alt="" class="to-quare circle">
        @endif
        <span class="title" itemprop="name">{{ $actor->name }}</span>
        <span class="truncate">{{ $actor->role }}</span>
        <a href="#!" class="secondary-content"><i class="material-icons">keyboard_arrow_right</i></a>
    </li>
    @endforeach  
</ul>


@endsection


