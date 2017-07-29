@extends('layouts.main')

@section('meta_title', $show->translation($layout['lang'])->meta_title)
@section('meta_description', $show->translation($layout['lang'])->meta_description)
@section('page_title', $show->translation($layout['lang'])->title)

@section('content')
<div class="row" itemscope itemtype="http://schema.org/TVSeries">

    <div class="swiper-container">
        <div class="swiper-wrapper">
            @foreach ($show->fanarts() as $fanart)
            <div class="swiper-slide">
                <!-- Required swiper-lazy class and image source specified in data-src attribute -->
                <img data-src="{{ $fanart->getSrc(1187) }}" class="swiper-lazy">
                <!-- Preloader image -->
                <div class="swiper-lazy-preloader swiper-lazy-preloader-black"></div>
            </div>
            @endforeach
        </div>
        <!-- Add Pagination -->
        <div class="swiper-pagination"></div>
    </div>

</div>

<!-- Initialize Swiper -->
<script>
    var swiper = new Swiper('.swiper-container', {
        pagination: '.swiper-pagination',
        paginationClickable: true,
        //nextButton: '.swiper-button-next',
        //prevButton: '.swiper-button-prev',
        // Disable preloading of all images
        preloadImages: false,
        // Enable lazy loading
        lazyLoading: true,
        // Enable infinite loop
        loop: true
    });
</script>


<div class="row">
    <div class="col s12 m12 l12">
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
</div>

<div class="col s12 m12 l12">
    <p itemprop="description">{{ $show->translation($layout['lang'])->content }}</p>
</div>


<h3>Série</h3>
<ul class="collapsible" data-collapsible="accordion">
    @for ($i = $show->lastSeason(); $i > 0; $i--)
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




<h3>Herci</h3>
<ul class="collection">
    @foreach ($show->actors() as $actor)
    <li class="collection-item avatar" itemprop="actor" itemscope itemtype="http://schema.org/Person">
        @if($actor->thumb() !== null)
        <img src="{{$actor->thumb()->getSrc(50, 'thumb')}}" alt="" class="to-quare circle">
        @endif
        <span class="title" itemprop="name">{{ $actor->name }}</span>
        <span class="truncate">{{ $actor->role }}</span>
        <a href="{{$layout['lang_prefix']}}{{$actor->url()}}" class="secondary-content"><i class="material-icons">keyboard_arrow_right</i></a>
    </li>
    @endforeach
</ul>


@endsection


