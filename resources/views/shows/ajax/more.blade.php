@if (isset($more) && $more == true)
<div class="row center">
    <div class="col s12 m12 l12">
        <button data-next class="btn waves-effect waves-light" value="{{ $next_page }}">More
        </button>
    </div>
</div>
@endif
