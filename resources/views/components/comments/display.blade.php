@if(count($comments) > 0)
<ul class="collection comments">
    @foreach ($comments as $comment)
    <li class="collection-item avatar" itemprop="comment" itemscope itemtype="http://schema.org/UserComments">
        <img class="circle" alt="avatar" src="@if($comment->user->avatar !== null){{$comment->user->avatar->getSrc(64, 'thumb')}}@else{{$comment->user->avatarPlaceholder()}}@endif">
        <span class="username" itemprop="creator" >{{ $comment->user->username }}</span><br/>
        <span class="date" itemprop="commentTime">{{ $comment->updated_at }}</span>
        <p class="content" itemprop="commentText">{{$comment->content}}</p>
        @if(Auth::check())
            @if($comment->user->id == Auth::user()->id)
            <form method="POST" action="/comments/{{$comment->id}}">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
                <button class="btn-floating secondary-content delete-icon" type="submit" >
                    <i class="material-icons right">delete</i>
                </button>
            </form>
            @endif
        @endif
    </li>
    @endforeach
</ul>
@endif