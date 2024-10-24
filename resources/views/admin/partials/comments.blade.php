@foreach($comments??[] as $comment)
    <div class="comment">
        <div class="comment-body">
            <div class="d-flex justify-content-between align-items-center flex-row">
                <span class="comment-user" data-initial="S">{{$comment->user?->name}}</span>
                <span class="">{{\Carbon\Carbon::parse($comment->created_at)->diffForHumans()}}</span>
            </div>
            <p class="comment-text">{{$comment->comment}}</p>
        </div>
        <div class="comment-footer">
            <button class="btn reply-btn" data-comment="{{$comment->id}}">Reply</button>
            <button class="btn" onclick="toggleReplies('replies_{{$comment->id}}')">Comments({{count($comment->replies)}})</button>
        </div>
        <div class="reply-form" style="display:none;">
            <input placeholder="Write a reply..." class="d-inline form-control w-50">
            <button class="btn submit-reply">Submit reply</button>
        </div>
        @if($comment->replies)
            <div class="replies replies_{{$comment->id}}">
                @foreach($comment->replies??[] as $reply)
                    <div class="reply" style="padding: 0px 20px;">
                        <div class="reply-body">
                            <div class="d-flex justify-content-between align-items-center flex-row mb-2">
                                <span class="comment-user" data-initial="S">{{$reply->user?->name}}</span>
                                <span class="">{{\Carbon\Carbon::parse($reply->created_at)->diffForHumans()}}</span>
                            </div>
                            <p class="reply-text">{{$reply->comment}}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endforeach
