<ul class="collection episodes">
    @foreach ($season as $key => $episode)
    <li class="collection-item 
        @if ($key % 2 == 0)
            grey lighten-4
        @endif
        " itemprop="episode" itemscope itemtype="http://schema.org/TVEpisode">
        <span class="hide-on-med-and-down"><i>S{{ $episode->season_number }}E<span itemprop="episodeNumber">{{ $episode->episode_number }}</span></i> - </span>
        <strong itemprop="name">{{ $episode->translation($layout['lang'])->title }}</strong> 
        <small class="hide-on-med-and-down" itemprop="releasedEvent" itemscope itemtype="http://schema.org/PublicationEvent" data-date="{{ $episode->first_aired }}"><span itemprop="startDate">{{ $episode->first_aired }}</span></small>
        <a itemprop="url" href="{{$episode->url($layout['lang'])}}"  class="secondary-content"><i class="material-icons">keyboard_arrow_right</i></a>
    </li>
    @endforeach  
</ul>