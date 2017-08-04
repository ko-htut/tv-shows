@if(count($comments) > 0)
<ul class="collection comments">
    @foreach ($comments as $comment)
    <li class="collection-item avatar">
        <img src="/public/img/placeholders/man.png" alt="" class="circle">
        <span class="username">{{ $comment->user->username }}</span><br/>
        <span class="date">{{ $comment->updated_at }}</span>
        <p class="content">
            {{$comment->content}}
        </p>
        @if($comment->user_id == Auth::user()->id)
        <form method="POST" action="/comments/{{$comment->id}}">
            {{ csrf_field() }}
            {{ method_field('DELETE') }}
            <button class="btn-floating secondary-content delete-icon" type="submit" >
                <i class="material-icons right">delete</i>
            </button>
        </form>
        @endif
    </li>
    @endforeach
</ul>
@endif