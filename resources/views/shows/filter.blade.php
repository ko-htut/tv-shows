<div class="row">
    <input name="page" value="{{ $page }}" type="hidden">

    <div class="input-field col s12 m12 l12">
        <input type="text" id="search" name="search" class="autocomplete" value="@if (isset($_GET['search'])){{ $_GET['search'] }}@endif">
        <label for="search">Vyhledat seriály</label>
    </div>


    <div class="input-field col l4 m6 s12">
        <select multiple name="genre[]" id="genres">
            <option value="" disabled selected>Vyberte žánry</option>
            @foreach ($filter['genres'] as $genre)
            <option value="{{ $genre->id }}" @if (isset($_GET['genre']) && in_array($genre->id, $_GET['genre']))selected @endif>{{ $genre->translation()->title }} ({{ $filter['genres_counter'][$genre->id]->count }})</option>
            @endforeach  
        </select>
        <label>Žánry</label>
    </div>
    <div class="input-field col l4 m6 s12">
        <select multiple name="network[]" id="networks">
            <option value="" disabled selected>Vyberte televize</option>
            @foreach ($filter['networks'] as $network)
            <option value="{{ $network->id }}" @if (isset($_GET['network']) && in_array($network->id, $_GET['network']))selected @endif>{{ $network->translation()->value }} ({{ $filter['options_counter'][$network->id]->count }})</option>
            @endforeach  
        </select>
        <label>Televize</label>
    </div>
    
    <div class="input-field col l4 m12 s12">
        <select name="order" id="order">
            <option value="" disabled selected>Řadit podle</option>
            @foreach ($filter['orders'] as $key => $order)
            <option value="{{ $key }}" @if (isset($_GET['order']) && $key == $_GET['order']) selected @endif >{{ $order }}</option>
            @endforeach  
        </select>
        <label>Řazení</label>
    </div>
    
    <!--
    <div class="input-field col l6 m6 s6" style="">
        <div id="runtimeSlider" data-step="1">
        </div>
    </div>

    <div class="input-field col l3 m3 s3">
        <input type="number" id="sMin" name="sMin" value="{{ $filter['runtime']['min'] }}">
    </div>
    <div class="input-field col l3 m3 s3">
        <input type="number" id="sMax" name="sMax" value="{{ $filter['runtime']['max'] }}">
    </div>
    -->
</div>