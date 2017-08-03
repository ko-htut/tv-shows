<div class="row">
    <form class="col s12" method="POST" action="{{ route('login') }}" >
        {{ csrf_field() }}
        @if(Auth::check())
        <input type="hidden" name="user_id" value="@if(Auth::user()->id !== null){{Auth::user()->id}}@endif">
        @endif
        <input type="hidden" name="model_id" value="{{$model->id}}">
        <input type="hidden" name="model_type" value="{{$model->type}}">
        <div class="row">
            <div class="input-field col s12">
                <textarea id="content" placeholder="Sem napište svůj komentář..." class="materialize-textarea" data-length="500"></textarea>
                <label for="content">Komentář</label>
            </div>
            <div class="col s12 right-align">
                <button type="submit" class="btn blue darken-3 comment-submit">
                    Vložit
                </button>
            </div>
        </div>
    </form>
</div>
