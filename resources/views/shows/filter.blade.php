<div class="row">
    <input name="page" value="{{ $page }}" type="hidden">

    <div class="input-field col s12 m12 l8">
        <input type="text" id="search" name="search" class="autocomplete" value="@if (isset($_GET['search'])){{ $_GET['search'] }}@endif">
        <label for="search">Search</label>
    </div>

    <div class="input-field col l4 m12 s12">
        <select name="order" id="order">
            <option value="" disabled selected>Order by</option>
            @foreach ($filter['orders'] as $key => $order)
            <option value="{{ $key }}" @if (isset($_GET['order']) && $key == $_GET['order']) selected @endif >{{ $order }}</option>
            @endforeach  
        </select>
        <label>Order</label>
    </div>

    <div class="input-field col l4 m6 s12">
        <select multiple name="genre[]" id="genres">
            <option value="" disabled selected>Select genres</option>
            @foreach ($filter['genres'] as $genre)
            <option value="{{ $genre->id }}" @if (isset($_GET['genre']) && in_array($genre->id, $_GET['genre']))selected @endif>{{ $genre->translation()->title }} ({{ $filter['genres_counter'][$genre->id]->count }})</option>
            @endforeach  
        </select>
                 <label>Genres</label>
    </div>
         <div class="input-field col l4 m6 s12">
        <select multiple name="network[]" id="networks">
            <option value="" disabled selected>Select networks</option>
            @foreach ($filter['networks'] as $network)
            <option value="{{ $network->id }}" @if (isset($_GET['network']) && in_array($network->id, $_GET['network']))selected @endif>{{ $network->translation()->value }} ({{ $filter['options_counter'][$network->id]->count }})</option>
            @endforeach  
        </select>
        <label>Networks</label>
    </div>
    <div class="input-field col l4 m6 s12">
            <select multiple name="status[]" id="statuses">
                    <option value="" disabled selected>Select statuses</option>
                    @foreach ($filter['statuses'] as $status)
                    <option value="{{ $status->id }}" @if (isset($_GET['status']) && in_array($status->id, $_GET['status']))selected @endif>{{ $status->translation()->value }} ({{ $filter['options_counter'][$status->id]->count }})</option>
                    @endforeach  
                </select>
                <label>Statuses</label>
                </div>

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


                </div>