@extends('layouts.main')

@section('meta_title', $actor->name)
@section('meta_description', $actor->name)
@section('page_title', $actor->name )

@section('content')

<div class="row" itemscope itemtype="http://schema.org/Person">
    <div class="col s12 m6 l6" style="width: 300px;">
        <div class="swiper-container">
            <div class="swiper-wrapper">
                @foreach ($actors as $a)
                @if($a->thumb() !== null)
                <div class="swiper-slide">
                    <!-- Required swiper-lazy class and image source specified in data-src attribute -->
                    <img data-src="{{$a->thumb()->getSrc(300) }}" class="swiper-lazy">
                    <!-- Preloader image -->
                    <div class="swiper-lazy-preloader swiper-lazy-preloader-black"></div>
                </div>
                @endif
                @endforeach
            </div>
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
        </div>
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
    @foreach($shows as $show)
    @include('shows.list-item')
    @endforeach
</div>

@endsection