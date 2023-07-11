@forelse ($task->media as $media)
  <div data-task-file-id="{{$media->id}}" class="task-attachment-col col-md-6">
        <ul class="list-unstyled task-attachment-wrapper" data-placement="right"
            data-toggle="tooltip" data-title="readme.txt">
            <li class="mbot10 task-attachment highlight-bg">
                <div class="mbot10 float-end task-attachment-user">
                    <a href="#" class="float-end" onclick="remove_task_attachment({{$media->id}}, {{$task->id}}); return false;"><i class="fa fa fa-times"></i></a>
                    <a href="#" target="_blank">Kristian Ziemann</a> <span class="text-has-action d-block tw-text-sm">{{$media->created_at->diffForHumans()}}</span>
                </div>
                <div class="clearfix"></div>
                <div class="task-attachment-no-preview">
                    <a href="{{$media->getUrl()}}" target="_blank"> <i class="mime mime-file"></i>{{$media->filename}} </a>
                </div>
                <div class="clearfix"></div>
            </li>
        </ul>
  </div>
@empty
@endforelse
