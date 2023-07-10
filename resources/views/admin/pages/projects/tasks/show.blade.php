<style>
  .list-group-item:hover {
    background-color: #f5f5f5;
  }
  input:focus {
    outline: none;
    border: none;
  }
</style>
<div class="row">
  <div class="col-7">
    <div class="col-md-12 d-flex">
      <span class="d-block fw-bold">Related: </span> <a href="#">{{$task->project->name}}</a>
    </div>
    <button class="btn btn-primary"><i class="fa fa-check"></i></button>
    <hr>
    <div class="col-md-12">
      <span class="d-block fw-bold">Description: </span> <span class="">{{$task->description}}</span>
    </div>
    <hr>
    <div class="col-12">
      <a href="#" onclick="add_checklist({{$task->id}}, this)"><i class="fa fa-plus-circle me-1"></i>Add checklist Item</a>
    </div>
    <ul class="p-0 list-group list-group-flush" id="sortable">
      @forelse ($task->checklistItems as $item)
        <li class="list-group-item drag-item cursor-move rounded d-flex border-0" data-task-id="{{$task->id}}" data-project-id="{{$task->project_id}}" data-checklist-id={{$item->id}}>
          <div class="form-check form-check-success">
            <input class="form-check-input rounded-circle" type="checkbox" value="1" @checked($item->status)>
          </div>
          <div class="flex-grow-1">
              <input type="text" value="{{$item->title}}" class="w-100 border-0">
          </div>
          <div class="ms-1">
              <i class="fa-regular fa-md fa-user cursor-pointer"></i>
              <i class="fa-solid fa-md fa-copy cursor-pointer"></i>
              <i onclick="delete_checklist(this)" class="fa-regular fa-md fa-trash-can cursor-pointer"></i>
          </div>
        </li>
      @empty
      @endforelse
    </ul>
    <hr>
    <h4>Attachments</h4>
    <div class="row">
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
  </div>
  </div>
  <div class="col-md-5 task-single-col-right rounded">
    <h5 class="mt-3">Task Info</h5>
    <div class="task-info task-status task-info-status">
      <h5 class="tw-inline-flex tw-items-center tw-space-x-1.5">
          <i class="fa-regular fa-star fa-fw fa-lg pull-left task-info-icon"></i>Status:
          <span class="task-single-menu task-menu-status">
              <span class="trigger pointer manual-popover text-has-action tw-text-neutral-800"
                  data-original-title="" title="">
                  {{$task->status}}</span>
              <span class="content-menu hide">
              </span>
          </span>
      </h5>
    </div>
    <div class="task-info task-status task-info-status">
      <h5 class="tw-inline-flex tw-items-center tw-space-x-1.5">
          <i class="fa-regular fa-calendar fa-fw fa-lg pull-left task-info-icon"></i>Start Date:
          <span class="task-single-menu task-menu-status">
              <span class="trigger pointer manual-popover text-has-action tw-text-neutral-800"
                  data-original-title="" title="">
                  {{$task->start_date}}</span>
              <span class="content-menu hide">
              </span>
          </span>
      </h5>
    </div>
    <div class="task-info task-status task-info-status">
      <h5 class="tw-inline-flex tw-items-center tw-space-x-1.5">
          <i class="fa-regular fa-calendar-check fa-fw fa-lg pull-left task-info-icon"></i>Due Date:
          <span class="task-single-menu task-menu-status">
              <span class="trigger pointer manual-popover text-has-action tw-text-neutral-800"
                  data-original-title="" title="">
                  {{$task->due_date}}</span>
              <span class="content-menu hide">
              </span>
          </span>
      </h5>
    </div>
    <div class="task-info task-status task-info-status">
      <h5 class="tw-inline-flex tw-items-center tw-space-x-1.5">
          <i class="fa fa-bolt fa-fw fa-lg pull-left task-info-icon"></i>Priority:
          <span class="task-single-menu task-menu-status">
              <span class="trigger pointer manual-popover text-has-action tw-text-neutral-800"
                  data-original-title="" title="">
                  {{$task->priority}}</span>
              <span class="content-menu hide">
              </span>
          </span>
      </h5>
    </div>
    <hr>
    <div class="task-info task-status task-info-status">
      <h5 class="tw-inline-flex tw-items-center tw-space-x-1.5 fw-bold">
        <i class="fa-regular fa-bell fa-lg pull-left"></i><span class="ms-2"> Reminders </span>
      </h5>
    </div>
    <span class="text-danger cursor-pointer" onclick="create_reminder()">Create Reminder</span>

    <ul class="mtop10">
      @forelse ($task->reminders as $reminder)
        <li class="" data-id="4">
          <div class="mbot15">
            <div>
              <p class="bold">Reminder for {{$reminder->recipient->email}} on {{$reminder->remind_at}}
                <a href="#" class="  text-muted" onclick="edit_reminder(4, this); return false;">
                  <i class="fa fa-edit"></i>
                </a>
                <a href="#" class="text-danger delete-reminder">
                  <i class="fa fa-remove"></i>
                </a>
              </p>{{$reminder->description}}
            </div>
          </div>
        </li>
        <hr>
      @empty
      @endforelse
    </ul>
    <form id="create-reminder-form" method="POST" action="{{route('admin.projects.tasks.reminders.store', ['project' => $task->project_id, 'task' => $task])}}" style="display: none;">
      @csrf
      {!! Form::hidden('reminder_id', null, []) !!}
      <div class="">
        <label class="form-label">Remind At</label>
        <input type="datetime-local" name="remind_at" class="form-control form-control-sm">
      </div>
      <div class="">
        <label class="form-label">Set Reminder To</label>
        {!! Form::select('recipient_id', $task->project->members()->pluck('email', 'admins.id'), null, ['class' => 'form-select form-select-sm select2']) !!}
      </div>
      <div class="form-group">
        <label for="description" class="control-label"> <small class="req text-danger">* </small>Description</label>
        <textarea id="description" name="description" class="form-control form-control-sm" rows="3"></textarea>
      </div>
      <div class="checkbox checkbox-primary">
        <input type="checkbox" name="notify_by_email" id="notify_by_email">
        <label for="notify_by_email">Send also an email for this reminder</label>
      </div>
      <div class="d-flex justify-content-end">
        <button class="btn btn-sm btn-primary" data-form="ajax-form">Save Reminder</button>
      </div>
    </form>

    <div class="dropzone needsclick bg-white mt-5 mb-2" data-upload-url="{{ route('admin.projects.tasks.files.store',['project' => $task->project_id, 'task' => $task->id])}}" data-response="#{{'fields_'.$task['id']}}">
      <div class="dz-message needsclick">
        <small class="h6"> Drop files here to upload </small>
      </div>
    </div>
  </div>
</div>
<script>
  function create_reminder(id = null){
    $('[name="reminder_id"]').val(null);
    $('#create-reminder-form').toggle();
  }

   function update_reminder_form(){
    var form = $('#create-reminder-form');
    form.hide();
    var reminder_id = form.find('[name="reminder_id"]').val();
    var remind_at = form.find('[name="remind_at"]').val();
    var recipient_id = form.find('[name="recipient_id"]').val();
    var description = form.find('[name="description"]').val();

    if(reminder_id == null){
      form.before('<div class="alert alert-info">Reminder will be created</div>')
    }
  }
</script>
