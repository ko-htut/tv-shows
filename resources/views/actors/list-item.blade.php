<div class="col s6 m4 l2" itemprop="actor" itemscope itemtype="http://schema.org/Person">
    <a itemprop="url" href="{{ $actor->url($layout['lang'])}}">
        <div class="card actor">
            <div class="card-image">
                @if($actor->thumb() !== null)
                <img itemprop="image" src="{{ $actor->thumb()->src() }}" alt="{{$actor->name}}">
                @else
                <img itemprop="image" src="{{$actor->placeholder()}}" alt="placeholder">
                @endif
            </div>
            <div class="card-content">
                <span class="card-title truncate" itemprop="name">{{$actor->name}}</span>
                <span class="card-title truncate" itemprop="alternateName">{{$actor->role}}&nbsp;</span>
            </div>
        </div>
    </a>
</div>
