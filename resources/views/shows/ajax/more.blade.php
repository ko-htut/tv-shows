@if (isset($more) && $more == true)
<input name="page" value="{{ $page }}" type="hidden">
<div class="row center">
    <div class="col s12 m12 l12">
        <a data-next="{{ $next_page }}" class="btn waves-effect waves-light" value="{{ $next_page }}" 
           href="?page={{$next_page}}"
           >
            @lang('strings.more')
        </a>
    </div>
</div>
@endif
