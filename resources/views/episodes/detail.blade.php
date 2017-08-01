@extends('layouts.main')

@section('meta_title', $episode->translation($layout['lang'])->title)
@section('meta_description', $episode->translation($layout['lang'])->title)
@section('page_title', $episode->translation($layout['lang'])->title)

@section('content')
<div class="row" itemscope itemtype="http://schema.org/TVEpisode">

    <div class="swiper-container">
        <div class="swiper-wrapper">
            @if($episode->thumb() !== null)
            <div class="swiper-slide">
                <!-- Required swiper-lazy class and image source specified in data-src attribute -->
                <div class="noise">
                    <img src="{{ $episode->thumb()->src() }}" />
                </div>

            </div>
            @endif
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
        preloadImages: true,
        // Enable lazy loading
        lazyLoading: false,
        // Enable infinite loop
        //loop: true
    });
</script>


<div class="col s12 m12 l12">
    <p itemprop="description">{{ $episode->translation($layout['lang'])->content }}</p>
</div>


@endsection


