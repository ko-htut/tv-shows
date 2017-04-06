<div class="row">
    <input name="page" value="{{ $page }}" type="hidden">
    <div class="input-field col s12 m12 l12">
        <input type="text" id="search" name="search" class="autocomplete" value="@if (isset($_GET['search'])){{ $_GET['search'] }}@endif">
        <label for="search">Search</label>
    </div>
    <div class="input-field col l4 m6 s12">
        <select multiple name="genre[]" id="genres">
            <option value="" disabled selected>Select genres</option>
            @foreach ($filter['genres'] as $genre)
            <option value="{{ $genre->id }}" @if (isset($_GET['genre']) && in_array($genre->id, $_GET['genre']))selected @endif>{{ $genre->translation()->title }}</option>
            @endforeach  
        </select>
        <label>Genres</label>
    </div>
    <div class="input-field col l4 m6 s12">
        <select multiple name="network[]" id="networks">
            <option value="" disabled selected>Select networks</option>
            @foreach ($filter['networks'] as $network)
            <option value="{{ $network->id }}" @if (isset($_GET['network']) && in_array($network->id, $_GET['network']))selected @endif>{{ $network->translation()->value }}</option>
            @endforeach  
        </select>
        <label>Networks</label>
    </div>
    <div class="input-field col l4 m6 s12">
        <select multiple name="status[]" id="statuses">
            <option value="" disabled selected>Select statuses</option>
            @foreach ($filter['statuses'] as $status)
            <option value="{{ $status->id }}" @if (isset($_GET['status']) && in_array($status->id, $_GET['status']))selected @endif>{{ $status->translation()->value }}</option>
            @endforeach  
        </select>
        <label>Statuses</label>
    </div>

    <div class="col l12 m12 s12">
        <div id="runtimeSlider" data-step="1">
        </div>
        <input type="hidden" id="sMin" name="sMin" value="{{ $filter['runtime']['min'] }}">
        <input type="hidden" id="sMax" name="sMax" value="{{ $filter['runtime']['max'] }}">
    </div>
</div>