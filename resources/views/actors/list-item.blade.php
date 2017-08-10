<div class="col s6 m4 l2">
    <a href="{{ $actor->url($layout['lang'])}}">
        <div class="card actor">
            <div class="card-image">
                @if($actor->thumb() !== null)
                <img src="{{ $actor->thumb()->src() }}"  alt="{{$actor->name}} ">
                @else
                <img src="{{$actor->placeholder()}}"  alt="placeholder">
                @endif
            </div>
            <div class="card-content">
                <span class="card-title truncate">{{$actor->name}}&nbsp;</span>
                <span class="card-title truncate">{{$actor->role}}&nbsp;</span>
            </div>
        </div>
    </a>
</div>
