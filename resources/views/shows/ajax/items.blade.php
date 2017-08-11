<div id="snippet-wrapper">
    @if(isset($page) && $page == 1)
    <div class="input-field col l12 m12 s12">
        <p>Nalezeno {{ $results }} seriálů</p>
    </div>
    @endif
    @if($shows !== null)
    @foreach ($shows as $show)
        @include('shows.list-item')
    @endforeach
    @endif
    <div id="snippet-more">
        @include('shows.ajax.more')
    </div>
</div>
