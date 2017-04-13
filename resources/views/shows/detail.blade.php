@extends('layouts.main')
@section('content')

<div class="row">
    <img class="responsive-img" src="{{ $show->banner()->external_patch }}">
    <div class="col s12"><h1>{{ $show->translation($lang)->title }}</h1></div>

    <div class="row">
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
            {{ $show->status()->translation()->value }}
            <i class="chip-icon material-icons">info_outline</i>
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

    <div class="col s12 m12 l12"><p>{{ $show->translation($lang)->content }}</p></div>
</div>





<ul class="collapsible" data-collapsible="accordion">
    @for ($i = 1; $i < $show->lastSeason()+1; $i++)  
    <li>
        <div class="ajax collapsible-header" href="?season={{ $i }}"><span class="new badge" data-badge-caption="episodes">{{ $show->seasonEpisodesCount($i) }}</span>
            <a href="?season={{ $i }}" class="ajax collection-item">Season {{ $i }}</a>
        </div>
        <div class="collapsible-body">
            <div id="snippet-season-{{ $i }}">
                
            </div>
        </div>
    </li>
    @endfor
</ul>

@endsection


