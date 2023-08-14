@forelse ($project->tasks as $task)
  <li class="p-0 m-0 task task-item" data-id="{{$task->id}}" data-mine="{{$task->assignees->where('id', auth()->id())->count() > 0}}" data-{{slug($task->status)}}="true" data-{{$task->priority}}="true">
    <div class="email-list-item todo-item d-flex align-items-center fw-bold task-header">
      <i class="el-voh drag-handle cursor-move fa-solid fa-ellipsis-vertical me-2"></i>
      <div class="email-list-item-content ms-2 ms-sm-0 me-2">
        <span class="email-list-item-subject d-xl-inline-block d-block">{{$task->subject}}</span>
      </div>
      <div class="email-list-item-meta ms-auto d-flex align-items-center">
        <div class="el-hoh me-2" style="width: 80px">
          @include('admin._partials.sections.progressBar', ['perc' => $task->progress_percentage(), 'color' => 'primary', 'show_perc' => true, 'height' => '14px'])
        </div>
        <span class="el-hoh badge bg-label-{{$colors[$task->priority]}} me-2">{{$task->priority}}</span>
        @isset($task->assignees[0])
        <img src="{{ $task->assignees[0]->avatar }}" alt="user-avatar" class="el-hoh d-block flex-shrink-0 rounded-circle me-sm-3 me-2" height="32" width="32" />
        @endisset
        <ul class="list-unstyled el-foh">
          <form action="{{route('admin.projects.tasks.hide-completed', ['project' => $task->project_id, 'task' => $task->id, 'status' => !$task->is_completed_checklist_hidden, 'from' => 'task-board'])}}" method="POST">
            @method('PUT')
          @if ($task->is_completed_checklist_hidden)
            <li class="m-0 me-2 p-0"> <button class="btn btn-sm btn-primary" data-form="ajax-form">Show Completed ({{$task->checklistItems->whereNotNull('completed_by')->count()}})</button> </li>
          @else
            <li class="m-0 me-2 p-0"> <button class="btn btn-sm btn-primary" data-form="ajax-form">Hide Completed</button> </li>
          @endif
        </form>
          <li class="m-0 me-2 p-0" data-href="{{route('admin.projects.tasks.checklist-items.create', [$project, $task, 'from' => 'task-board'])}}" data-toggle="ajax-modal" data-title="Add Check Item"> <i class="tf-icons ti ti-plus scaleX-n1-rtl ti-sm"></i> </li>
          <li class="m-0 me-2 p-0" data-href="{{route('admin.projects.tasks.edit', [$project, $task, 'from' => 'task-board'])}}" data-toggle="ajax-modal" data-title="Edit Task"> <i class="ti ti-edit"></i> </li>
          <li class="m-0 me-2 p-0" data-href="{{route('admin.projects.tasks.destroy', [$project, $task, 'from' => 'task-board'])}}" data-toggle="ajax-delete"> <i class="ti ti-trash"></i> </li>
        </ul>
      </div>
    </div>
    <ul class="list-unstyled task-check-items todo-task-list subtasks ms-2" data-task-list="{{$task->id}}">
      @forelse ($task->checklistItems as $item)
        @if($task->is_completed_checklist_hidden && $item->completed_by != null)
          @continue
        @endif
        <li class="email-list-item subtask" data-item-id="{{$item->id}}" data-{{slug($task->status)}}="true" data-{{$task->priority}}="true">
          <div class="d-flex align-items-center">
            <i class="el-voh drag-handle cursor-move fa-solid fa-ellipsis-vertical me-2"></i>
            <div class="form-check mb-0">
              <input class="email-list-item-input form-check-input checklist-status" type="checkbox" @checked($item->completed_by != null)>
              <label class="form-check-label" for="email-1"></label>
            </div>
            <div class="email-list-item-content ms-2 ms-sm-0 me-2">
              <span class="email-list-item-subject d-xl-inline-block d-block {{$item->completed_by != null ? 'text-decoration-line-through' : ''}}">{{$item->title}}</span>
            </div>
            <div class="email-list-item-meta ms-auto d-flex align-items-center">
              @if ($item->assignedTo)
                <img src="{{ $item->assignedTo->avatar }}" alt="user-avatar" class="el-hoh d-block flex-shrink-0 rounded-circle me-sm-3 me-2" height="32" width="32" />
              @endif
              <ul class="list-unstyled el-foh">
                <li class="m-0 me-2 p-0" data-toggle="ajax-modal" data-title="Edit Check Item" data-href="{{route('admin.projects.tasks.checklist-items.edit', [$project, $task, 'checklist_item' => $item->id, 'from' => 'task-board'])}}"> <i class="ti ti-edit"></i> </li>
                <li class="m-0 me-2 p-0" data-toggle="ajax-delete"
                data-href="{{ route('admin.projects.tasks.checklist-items.destroy', ['project' => $task->project_id, 'task' => $task, 'checklist_item' => $item->id, 'from' => 'task-board']) }}"> <i class="ti ti-trash"></i> </li>
              </ul>
            </div>
          </div>
        </li>
      @empty
      @endforelse
    </ul>
  </li>
@empty
@endforelse
