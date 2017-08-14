<div id="snippet-favourite">
    <a class="favourite ajax {{$isFavourite ? 'active' : ''}}" 
       href="?favourite=true" 
       data-history="false"
       data-position="bottom"
       data-delay="50" 
       data-tooltip="{{$isFavourite ? __('strings.remove_from_favourites') : __('strings.add_to_favourites') }}">
        <i class="material-icons">favorite</i>
    </a>
    <script>
        $(document).ready(function () {
            $('.favourite').tooltip({delay: 50});
        });
    </script>
</div>