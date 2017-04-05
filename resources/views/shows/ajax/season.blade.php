<ul class="collection">
    @foreach ($season as $episode)
    <li class="collection-item"><i>S{{ $episode->season_number }}E{{ $episode->episode_number }}</i> - <strong>{{ $episode->translation($lang)->title }}</strong></li>
    @endforeach  
</ul>