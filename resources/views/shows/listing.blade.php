@extends('layouts.main')
@section('content')

<div class="row">

    <form class="ajax" id="filter" action="GET">
        <div class="input-field col l4 m6 s12">
            <select multiple name="genre[]" id="genres">
                <option value="" disabled selected>Select genres</option>
                @foreach ($filter['genres'] as $genre)
                <option value="{{ $genre->id }}">{{ $genre->translation()->title }}</option>
                @endforeach  
            </select>
            <label>Genres</label>
        </div>

        <div class="input-field col l4 m6 s12">
            <select multiple name="network[]" id="networks">
                <option value="" disabled selected>Select networks</option>
                @foreach ($filter['networks'] as $network)
                <option value="{{ $network->id }}">{{ $network->translation()->value }}</option>
                @endforeach  
            </select>
            <label>Networks</label>
        </div>

        <div class="input-field col l4 m6 s12">
            <select multiple name="status[]" id="statuses">
                <option value="" disabled selected>Select statuses</option>
                @foreach ($filter['statuses'] as $status)
                <option value="{{ $status->id }}">{{ $status->translation()->value }}</option>
                @endforeach  
            </select>
            <label>Statuses</label>
        </div>

    </form>
</div>

<div class="row" id="snippet-items">
    @foreach ($shows as $show)
    <div class="col s12 m6 l6">
        <div class="card">
            <div class="card-image">
                <img src="{{ $show->banner()->external_patch }}">
                <a href="{{ $lang }}/shows/{{ $show->translation($lang)->slug }}" 
                   class="btn-floating halfway-fab waves-effect waves-light red">
                    <i class="material-icons">add</i>
                </a>
            </div>
            <div class="card-content">
                <span class="card-title">{{ $show->translation($lang)->title }}</span>
                <p class="truncate">{{ $show->translation($lang)->content }}</p>
            </div>

        </div>
    </div>
    @endforeach  
    @include('pagination.custom', ['paginator' => $shows])
</div>
@endsection
