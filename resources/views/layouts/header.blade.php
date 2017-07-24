
<!-- Dropdown Structure -->
<ul id="dropdown1" class="dropdown-content">
    @foreach($layout['langs'] as $lang)
        <li><a href="<?php echo url('/'); ?>/{{$lang->code}}">{{$lang->name}} ({{$lang->englishName}})</a></li>
    @endforeach
</ul>
<nav>
    <div class="nav-wrapper teal">
        <ul class="right hide-on-med-and-down">
            <!-- Dropdown Trigger -->
            <li><a class="dropdown-button" href="#!" data-activates="dropdown1">Jazyky<i class="material-icons right">arrow_drop_down</i></a></li>
        </ul>
    </div>
</nav>
