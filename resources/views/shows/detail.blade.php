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
            Network: {{ $show->network()->translation()->value }}
        </div>
        <div class="chip">
            Status: {{ $show->status()->translation()->value }}
        </div>
        <div class="chip">
            Content rating: {{ $show->content_rating()->translation()->value }}
        </div>
        <div class="chip">
            Runtime: {{ $show->runtime }} min
        </div>
        <div class="chip">
            Since: {{ $show->first_aired }}
        </div>
    </div>

    <div class="col s12 m12 l12"><p>{{ $show->translation($lang)->content }}</p></div>
</div>




<ul class="collapsible" data-collapsible="accordion">
    @for ($i = 1; $i < $show->lastSeason()+1; $i++)  
    <li>
        <div class="ajax collapsible-header" href="?season={{ $i }}"><span class="new badge" data-badge-caption="episodes">{{ $show->seasonEpisodesCount($i) }}</span>
            <strong>
                Season {{ $i }}
            </strong>
        </div>
        <div class="collapsible-body">
            <div id="snippet-season-{{ $i }}">
                {{-- 
                <ul class="collection">
                    @foreach ($show->seasonEpisodes($i) as $episode)
                    <li class="collection-item"><i>S{{ $episode->season_number }}E{{ $episode->episode_number }}</i> - <strong>{{ $episode->translation($lang)->title }}</strong></li>
                @endforeach  
                </ul>
                --}}
            </div>
        </div>
    </li>
    @endfor
</ul>

@endsection


