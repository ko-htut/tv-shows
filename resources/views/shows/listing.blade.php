@extends('layouts.main')
@section('content')
<div class="row">
    @foreach ($shows as $show)
    <div class="col s12 m6">
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
</div>
@include('pagination.custom', ['paginator' => $shows])
@endsection
