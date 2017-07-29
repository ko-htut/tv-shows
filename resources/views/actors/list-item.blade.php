<div class="col s6 m4 l3">
    <div class="card actor">
        <div class="card-image">

            @if($actor->thumb() !== null)
                <img src="{{ $actor->thumb()->src() }}"  alt="{{$actor->name}} ">
            @endif
            <a href="{{ $actor->url($layout['lang'])}}"
               class="btn-floating halfway-fab waves-effect waves-light red">
                <i class="material-icons">add</i>
            </a>
        </div>
        <div class="card-content">
            <span class="card-title truncate">{{$actor->name}}&nbsp;</span>
        </div>
    </div>
</div>
