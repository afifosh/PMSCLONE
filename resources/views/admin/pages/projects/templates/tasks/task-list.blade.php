@forelse ($project_template->taskTemplates as $task)
  <li class="p-0 m-0 task task-item" data-id="{{$task->id}}" data-{{$task->priority}}="true">
    <div class="email-list-item todo-item d-flex align-items-center fw-bold task-header">
      <i class="el-voh drag-handle cursor-move fa-solid fa-ellipsis-vertical me-2"></i>
      <div class="email-list-item-content ms-2 ms-sm-0 me-2">
        <span class="email-list-item-subject d-xl-inline-block d-block">{{$task->subject}}</span>
      </div>
      <div class="email-list-item-meta ms-auto d-flex align-items-center">
        <span class="el-hoh badge bg-label-{{$colors[$task->priority]}} me-2" style="width: 68px;">{{$task->priority}}</span>
        @isset($task->assignees[0])
        <img src="{{ $task->assignees[0]->avatar }}" alt="user-avatar" class="el-hoh d-block flex-shrink-0 rounded-circle me-sm-3 me-2" height="32" width="32" />
        @endisset
        <ul class="list-unstyled el-foh">
          <li class="m-0 me-2 p-0" data-href="{{route('admin.project-templates.tasks.check-items.create', [$project_template, $task])}}" data-toggle="ajax-modal" data-title="Add Check Item"> <i class="tf-icons ti ti-plus scaleX-n1-rtl ti-sm"></i> </li>
          <li class="m-0 me-2 p-0" data-href="{{route('admin.project-templates.tasks.edit', [$project_template, $task])}}" data-toggle="ajax-modal" data-title="Edit Task"> <i class="ti ti-edit"></i> </li>
          <li class="m-0 me-2 p-0" data-href="{{route('admin.project-templates.tasks.destroy', [$project_template, $task])}}" data-toggle="ajax-delete"> <i class="ti ti-trash"></i> </li>
        </ul>
      </div>
    </div>
    <ul class="list-unstyled task-check-items todo-task-list subtasks ms-2" data-task-list="{{$task->id}}">
      @forelse ($task->checkItemTemplates as $item)
        <li class="email-list-item subtask" data-item-id="{{$item->id}}" data-{{slug($task->status)}}="true" data-{{$task->priority}}="true">
          <div class="d-flex align-items-center">
            <i class="el-voh drag-handle cursor-move fa-solid fa-ellipsis-vertical me-2"></i>
            <div class="email-list-item-content ms-2 ms-sm-0 me-2">
              <span class="email-list-item-subject d-xl-inline-block d-block">{{$item->title}}</span>
            </div>
            <div class="email-list-item-meta ms-auto d-flex align-items-center">
              <ul class="list-unstyled el-foh">
                <li class="m-0 me-2 p-0" data-toggle="ajax-modal" data-title="Edit Check Item" data-href="{{route('admin.project-templates.tasks.check-items.edit', ['project_template' => $task->project_template_id, 'task' => $task, 'check_item' => $item->id])}}"> <i class="ti ti-edit"></i> </li>
                <li class="m-0 me-2 p-0" data-toggle="ajax-delete"
                data-href="{{ route('admin.project-templates.tasks.check-items.destroy', ['project_template' => $task->project_template_id, 'task' => $task, 'check_item' => $item->id]) }}"> <i class="ti ti-trash"></i> </li>
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
