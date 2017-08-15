<div class="col s12 m6 @if(isset($detail) && $detail == true) l4 @else l4 @endif" itemscope itemtype="http://schema.org/TVSeries">
    <div class="card">
        <div class="card-image">
            @if($show->fanart() !== null)
            <div class="focus">
                <img itemprop="image" src="{{ $show->fanart()->getSrc(374)}}" class="focus" alt="{{$show->translation($layout['lang'])->title}}">
            </div>
            @endif
            <a itemprop="url" href="{{ $show->url($layout['lang'])}}"
               class="btn-floating halfway-fab waves-effect waves-light red">
                <i class="material-icons">add</i>
            </a>
        </div>
        <div class="card-content">
            <span class="card-title truncate" itemprop="name">{{ $show->translation($layout['lang'])->title }}</span>
            <div class="row truncate">
                <p class="truncate"></p>
            </div>
            <div class="row truncate">
                <div class="col s12 m6 l6">
                    <div class="chip">
                        {{ $show->rating }}
                        <i class="chip-icon material-icons">star_border</i>
                    </div>
                    @if($show->network() !== null)
                    <div class="chip" itemprop="productionCompany" itemscope itemtype="http://schema.org/Organization">
                        <span itemprop="name">{{ $show->network()->translation()->value }}</span>
                        <i class="chip-icon material-icons">tv</i>
                    </div>
                    @endif
                    <div class="chip">
                        {{ $show->runtime }}&nbsp;min
                        <i class="chip-icon material-icons">timer</i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>