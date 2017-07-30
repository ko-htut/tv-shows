<nav>
    <div class="nav-wrapper black">
        <ul class="left">
            <li>
                <a href="#!" class="">
                    <i class="material-icons button-collapse" data-activates="slide-out">menu</i>    
                    <h1 class="page-title">@yield('page_title', 'Televizní seriály')</h1>
                </a>
            </li>
        </ul>
        <ul class="right">
        </ul>
        <ul id="slide-out" class="side-nav">
            <li><div class="userView grey darken-4">
                    <a href="#"><img class="circle" src="http://placehold.it/70/ffd600/?text=THUMB"></a>
                    <a href="#"><span class="white-text name truncate">Username</span></a>
                    <a href="#"><span class="white-text email truncate">user@email.com</span></a>
                </div></li>
            <li><a href="{{$layout['lang_prefix']}}/"><i class="material-icons smaller">explore</i>Seriály</a></li>
            <li><a href="{{$layout['lang_prefix']}}/calendar"><i class="material-icons smaller">today</i>Kalendář</a></li>
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
            <li><a class="waves-effect" href="{{$layout['lang_prefix']}}/settings"><i class="material-icons smaller">settings</i>Nastavení</a></li>
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
        </ul>
    </div>
</nav>
