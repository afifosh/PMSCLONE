@forelse ($task->comments as $comment)
    <div class="highlight-bg p-3 mt-2 pb-0 rounded">
        <a href="#" class="task-date-as-comment-id"><span
                class="tw-text-sm"><span class="text-has-action inline-block">{{$comment->created_at->diffForHumans()}}</span></span></a>
        <a href="#"><img src="{{ $comment->admin->avatar }}"
                class="staff-profile-image-small media-object img-circle float-start me-3"></a><span
            class="float-end"><a href="#" onclick="remove_task_comment({{$comment->id}});"><i
                    class="fa fa-times text-danger"></i></a></span>
        <div class="media-body comment-wrapper">
            <div class="mleft40"><a href="#">{{$comment->admin->full_name}}</a>
                <br>
                <div data-edit-comment="35" class="hide edit-task-comment">
                    <span>{!!$comment->comment!!}</span>
                </div>
            </div>
            <hr class="task-info-separator">
        </div>
    </div>
@empty
@endforelse
