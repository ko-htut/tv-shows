<nav>
    <div class="nav-wrapper black">
        <ul class="left">
            <li>
                <a href="#!" class="">
                    <i class="material-icons button-collapse" data-activates="slide-out">menu</i>
                </a>
            </li>

        </ul>

        <ul class="right">
            <li class="search hide-on-med-and-down">
                <div class="input-field valign-wrapper">
                    <input id="search-input" type="search" placeholder="Vyledat seriály..."/>
                </div>
            </li>
        </ul>
        <ul id="slide-out" class="side-nav">
            <li><div class="userView grey darken-4">
                    @if(Auth::check())
                    <a href="{{route('users.edit', Auth::user()->id)}}"><img class="circle" src="@if(Auth::user()->avatar !== null){{Auth::user()->avatar->getSrc(64, 'thumb')}}?t={{strTotime(Auth::user()->avatar->updated_at)}}@else{{Auth::user()->avatarPlaceholder()}}@endif"></a>
                    <a href="{{route('users.edit', Auth::user()->id)}}"><span class="white-text name truncate">@if(Auth::user() !== null){{Auth::user()->username}}@endif</span></a>
                    <a href="{{route('users.edit', Auth::user()->id)}}"><span class="white-text email truncate">@if(Auth::user() !== null){{Auth::user()->email}}@endif</span></a>
                    @else
                    <a href="{{ url($layout['lang_prefix'] . '/login') }}"><img class="circle" src="/storage/app/public/img/placeholders/user.png"></a>
                    <a href="{{ url($layout['lang_prefix'] . '/login') }}"><span class="white-text">Přihlásit se</span></a>
                    @endif
                </div>
            </li>
            <li><a href="{{$layout['lang_prefix']}}/"><i class="material-icons smaller">explore</i>Seriály</a></li>
            <li><a href="/calendar"><i class="material-icons smaller">today</i>Kalendář</a></li>
            @if($layout['genres'] !== null)
            <li>
                <ul class="collapsible collapsible-accordion">
                    <li>
                        <a class="collapsible-header waves-effect"><i class="material-icons smaller">styles</i>Žánry<i class="material-icons right">arrow_drop_down</i></a>
                        <div class="collapsible-body">
                            <ul>
                                @foreach($layout['genres'] as $genre)
                                @if($genre !== null)
                                <li><a href="{{$layout['lang_prefix']}}/genres/{{ $genre->translation()->slug }}">{{ $genre->translation()->title }}<span class="counter">{{ $layout['genres_counter'][$genre->id]->count }}</span></a></li>
                                @endif
                                @endforeach

                            </ul>
                        </div>
                    </li>
                </ul>
            </li>
            @endif
            @if($layout['networks'] !== null)
            <li>
                <ul class="collapsible collapsible-accordion">
                    <li>
                        <a class="collapsible-header waves-effect"><i class="material-icons smaller">tv</i>Televize<i class="material-icons right">arrow_drop_down</i></a>
                        <div class="collapsible-body">
                            <ul>
                                @foreach($layout['networks'] as $network)
                                @if($network !== null)
                                <li>
                                    <a href="{{$layout['lang_prefix']}}/networks/{{ $network->slug }}">
                                        @if($network->translation() !== null){{ $network->translation()->value }}@endif
                                        @if(isset($network->id) && isset($layout['options_counter'][$network->id]))<span class="counter">{{ $layout['options_counter'][$network->id]->count }}</span>@endif
                                    </a>
                                </li>
                                @endif
                                @endforeach
                            </ul>
                        </div>
                    </li>
                </ul>
            </li>
            @endif
            <li><a href="{{$layout['lang_prefix']}}/actors/"><i class="material-icons smaller">supervisor_account</i>Herci</a></li>
            <li><div class="divider"></div></li>
            <li><a class="subheader">Ostatní</a></li>
            <!--<li><a class="waves-effect" href="#"><i class="material-icons smaller">settings</i>Nastavení</a></li>-->
            <li>
                <ul class="collapsible collapsible-accordion">
                    <li>
                        <a class="collapsible-header waves-effect"><i class="material-icons smaller">language</i>Jazyk<i class="material-icons right">arrow_drop_down</i></a>
                        <div class="collapsible-body">
                            <ul>
                                @foreach($layout['langs'] as $lang)
                                <li><a href="<?php echo url('/'); ?>/{{$lang->code}}">{{$lang->name}}<span class="counter">{{$lang->englishName}}</span></a></li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                </ul>
            </li>
            @if(Auth::check())
            <li>
                <a class="waves-effect" href="{{ route('logout') }}" onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();"><i class="material-icons smaller">lock</i>Odhlásit</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>
            </li>
            @endif
        </ul>
    </div>
</nav>
