<div id="snippet-wrapper">
    @if(isset($page) && $page == 1)
    <div class="input-field col l12 m12 s12">
        <p>Nalezeno {{ $results }} seriálů</p>
    </div>
    @endif
    @if($shows !== null)
    @foreach ($shows as $show)
    <div class="col s12 m6 l4">
        <div class="card">
            <div class="card-image">
                <div class="focus">
                    @if($show->fanart())
                    <img src="{{ $show->fanart()->getSrc(374) }}" class="focus" 
                         @if($show->translation($layout['lang']) !== null)
                         alt="{{$show->translation($layout['lang'])->title}}"
                         @endif
                         >
                    @endif
                </div>
                @if($show->url($layout['lang']) !== null)
                <a href="{{ $show->url($layout['lang'])}}"
                   class="btn-floating halfway-fab waves-effect waves-light red">
                    <i class="material-icons">add</i>
                </a>
                @endif        
            </div>
            <div class="card-content">
                <span class="card-title truncate">
                    @if($show->translation($layout['lang']) !== null)
                    {{ $show->translation($layout['lang'])->title }}@endif
                    &nbsp;
                </span>
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
                        <div class="chip">
                            {{ $show->network()->translation()->value }}
                            <i class="chip-icon material-icons">tv</i>
                        </div>
                        @endif
                        <div class="chip">
                            {{ $show->runtime }} min
                            <i class="chip-icon material-icons">timer</i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    @endif
    <div id="snippet-more">
        @include('shows.ajax.more')
    </div>
</div>
