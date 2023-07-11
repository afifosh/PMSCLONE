<div class="row">
  <div class="col-7 mt-3">
    <div class="col-md-12 d-flex">
      <span class="d-block fw-bold">Related: </span> <a href="#">{{$task->project->name}}</a>
    </div>
    <button class="btn btn-primary"><i class="fa fa-check"></i></button>
    <hr>
    <div class="col-md-12">
      <span class="d-block fw-bold">Description: </span> <span class="">{{$task->description}}</span>
    </div>
    <hr>
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

    <ul class="" id="reminders-list">
      @include('admin.pages.projects.tasks.reminders-index')
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
        {!! Form::select('recipient_id', $task->project->members()->pluck('email', 'admins.id'), null, ['class' => 'form-select form-select-sm globalOfSelect2']) !!}
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
  </div>
</div>

<script>
  function remove_reminder(rem_id)
  {
    if(!confirm('Are you sure you want to delete this reminder?')) return false;
    var url = "{{route('admin.projects.tasks.reminders.destroy', ['project' => $task->project_id, 'task' => $task, 'reminder' => ':rem_id'])}}";
    url = url.replace(':rem_id', rem_id);
    $.ajax({
      url: url,
      method: 'DELETE',
      data: {
        reminder: rem_id
      },
      success: function(data) {
        reload_reminder_list();
      }
    });
  }

  function reload_reminder_list()
  {
    $.ajax({
      url: "{{route('admin.projects.tasks.reminders.index', ['project' => $task->project_id, 'task' => $task])}}",
      method: 'GET',
      success: function(data) {
        $('#reminders-list').html(data.data.view_data);
      }
    });
  }
</script>
