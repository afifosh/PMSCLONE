<div class="row">
  <div class="col-7">
    <div class="col-md-12 d-flex">
      <span class="d-block fw-bold">Related: </span> <a href="#">{{$task->project->name}}</a>
    </div>
    <button class="btn btn-primary"><i class="fa fa-check"></i></button>
    <button class="btn btn-light"><i class="fa-regular fa-clock"></i> Start Timer</button>
    <hr>
    <div class="col-md-12">
      <span class="d-block fw-bold">Description: </span> <span class="">{{$task->description}}</span>
    </div>
    <hr>
    <div class="col-12">
      <a href="#" onclick="add_checklist({{$task->id}}, this)"><i class="fa fa-plus-circle me-1"></i>Add checklist Item</a>
    </div>
    <ul class="p-0 list-group list-group-flush" id="sortable">
      {{-- <li class="list-group-item drag-item cursor-move d-flex mt-1">
        <div class="form-check form-check-success mt-2">
          <input class="form-check-input rounded-circle" type="checkbox" value="1">
        </div>
        <div class="">
            <input type="text" name="" id="" class="form-control">
        </div>
        <div class="mt-2 ms-3">
            <i class="fa-regular fa-xl fa-user cursor-pointer"></i>
            <i class="fa-solid fa-xl fa-copy cursor-pointer"></i>
            <i onclick="delete_checklist({{$task->id}}, this)" class="fa-regular fa-xl fa-trash-can cursor-pointer"></i>
        </div>
      </li> --}}
      @forelse ($task->checklistItems as $item)
        <li class="list-group-item drag-item cursor-move d-flex mt-1" data-task-id="{{$task->id}}" data-project-id="{{$task->project_id}}" data-checklist-id={{$item->id}}>
          <div class="form-check form-check-success mt-2">
            <input class="form-check-input rounded-circle" type="checkbox" value="1" @checked($item->status)>
          </div>
          <div class="">
              <input type="text" value="{{$item->title}}" class="form-control">
          </div>
          <div class="mt-2 ms-3">
              <i class="fa-regular fa-xl fa-user cursor-pointer"></i>
              <i class="fa-solid fa-xl fa-copy cursor-pointer"></i>
              <i onclick="delete_checklist(this)" class="fa-regular fa-xl fa-trash-can cursor-pointer"></i>
          </div>
        </li>
      @empty
      @endforelse
    </ul>
    <hr>
    <h4>Attachments</h4>
    <div class="row">
      @forelse ($task->media as $media)
      <div class="card col-11 mt-2" style="background-color: #edfaff; border: 1px solid #def6ff; box-shadow: none">
        <div class="p-3">
          <span>{{$media->filename}}</span>
        </div>
        <hr class="mt-0 pt-0">
        <div class="text-center p-5">
          <a href="{{$media->getUrl()}}" target="_blank">{{$media->filename}}</a>
        </div>
      </div>
      @empty
      @endforelse
    </div>
  </div>
  <div class="col-5 bg-light rounded">
    <h5 class="mt-3">Task Info</h5>
    <div class="col-md-12 d-flex">
      <i class="fa-regular fa-star mt-1 me-1"></i>Status:<span class="fw-bold">{{$task->status}}</span>
    </div>
    <div class="col-md-12 d-flex">
      <i class="fa-regular fa-calendar fa-margin mt-1 me-1"></i>Start Date:<span class="fw-bold">{{$task->start_date}}</span>
    </div>
    <div class="col-md-12 d-flex">
      <i class="fa-regular fa-calendar-check mt-1 me-1"></i>Dute Date:<span class="fw-bold">{{$task->due_date}}</span>
    </div>
    <div class="col-md-12 d-flex">
      <i class="fa fa-bolt mt-1 me-1"></i>Priority:<span class="fw-bold">{{$task->priority}}</span>
    </div>
    <div class="dropzone needsclick bg-white mt-5 mb-2" data-upload-url="{{ route('admin.projects.tasks.files.store',['project' => $task->project_id, 'task' => $task->id])}}" data-response="#{{'fields_'.$task['id']}}">
      <div class="dz-message needsclick">
        <small class="h6"> Drop files here to upload </small>
      </div>
    </div>
  </div>
</div>
