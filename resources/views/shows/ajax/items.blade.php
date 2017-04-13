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
                <img src="{{ $show->banner()->external_patch }}">
                <a href="{{ $lang }}/shows/{{ $show->translation($lang)->slug }}" 
                   class="btn-floating halfway-fab waves-effect waves-light red">
                    <i class="material-icons">add</i>
                </a>
            </div>
            <div class="card-content">
                <span class="card-title truncate">{{ $show->translation($lang)->title }}</span>
                <div class="row truncate">
                    <p class="truncate">{{ $show->translation($lang)->content }}</p>
                </div>

                <div class="row truncate">
                    <div class="col s12 m6 l6">
                        <div class="chip">
                            {{ $show->network()->translation()->value }}
                            <i class="close material-icons">tv</i>
                        </div>
                        <div class="chip">
                            {{ $show->status()->translation()->value }}
                            <i class="close material-icons">info_outline</i>
                        </div>
                        <div class="chip">
                            {{ $show->runtime }} min
                            <i class="close material-icons">timer</i>
                        </div>
                        <div class="chip">
                            {{ $show->first_aired }}
                            <i class="close material-icons">cake</i>
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
