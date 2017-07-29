<ul class="collection episodes">
    @foreach ($season as $episode)
    <li class="collection-item" itemprop="episode" itemscope itemtype="http://schema.org/TVEpisode">
        <i>S{{ $episode->season_number }}E<span itemprop="episodeNumber">{{ $episode->episode_number }}</span></i> - <strong itemprop="name">{{ $episode->translation($layout['lang'])->title }}</strong> <small itemprop="datePublished">{{ $episode->first_aired }}</small>
        <a href="#!" class="secondary-content"><i class="material-icons">keyboard_arrow_right</i></a>
    </li>
    @endforeach  
</ul>