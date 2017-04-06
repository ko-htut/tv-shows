<ul class="collection">
    @foreach ($season as $episode)
    <li class="collection-item truncate">
        <i>S{{ $episode->season_number }}E{{ $episode->episode_number }}</i> - <strong>{{ $episode->translation($lang)->title }}</strong> <small>{{ $episode->first_aired }}</small>
        <a href="#!" class="secondary-content"><i class="material-icons">keyboard_arrow_right</i></a>
    </li>
    @endforeach  
</ul>