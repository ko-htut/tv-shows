<div id="snippet-wrapper">
    @if($page == 1)
    <div class="input-field col l12 m12 s12">
        <p>Found {{ $results }} results</p>
    </div>
    @endif
    @foreach ($shows as $show)
    <div class="col s12 m6 l6">
        <div class="card">
            <div class="card-image">
                {{ $show->fanart()->resize(487) }}
                <img src="{{ $show->fanart()->src() }}" alt="{{$show->translation($lang)->title}}">
                <a href="{{ $show->url($lang)}}"
                   class="btn-floating halfway-fab waves-effect waves-light red">
                    <i class="material-icons">add</i>
                </a>
            </div>
            <div class="card-content">
                <span class="card-title truncate">{{ $show->translation($lang)->title }}&nbsp;</span>
              
                <div class="row truncate">
                    <p class="truncate"></p>
                </div>

                <div class="row truncate">
                    <div class="col s12 m6 l6">
                        <div class="chip">
                            {{ $show->rating }}
                            <i class="chip-icon material-icons">star_border</i>
                        </div>
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
                            <i class="chip-icon material-icons">all_inclusive</i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    <div id="snippet-more">
        @include('shows.ajax.more')
    </div>
</div>
