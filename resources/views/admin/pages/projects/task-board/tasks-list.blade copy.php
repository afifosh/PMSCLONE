@forelse ($project->tasks as $task)
  <li class="email-list-item email-marked-read fw-bold" data-mine="{{$task->assignees->where('id', auth()->id())->count() > 0}}" data-{{slug($task->status)}}="true" data-{{$task->priority}}="true">
    <div class="d-flex align-items-center">
      {{-- <div class="form-check mb-0">
        <input class="email-list-item-input form-check-input" type="checkbox" id="email-1">
        <label class="form-check-label" for="email-1"></label>
      </div> --}}
      {{-- <i class="email-list-item-bookmark ti ti-star ti-xs d-sm-inline-block d-none cursor-pointer ms-2 me-3"></i> --}}
      {{-- <img src="{{ asset('assets/img/avatars/1.png') }}" alt="user-avatar" class="d-block flex-shrink-0 rounded-circle me-sm-3 me-2" height="32" width="32" /> --}}
      <div class="email-list-item-content ms-2 ms-sm-0 me-2">
        {{-- <span class="h6 email-list-item-username me-2">Chandler Bing</span> --}}
        <span class="email-list-item-subject d-xl-inline-block d-block">{{$task->subject}}</span>
      </div>
      <div class="email-list-item-meta ms-auto d-flex align-items-center">
        <span class="el-hoh badge bg-label-{{$colors[$task->priority]}} me-2">{{$task->priority}}</span>
        @isset($task->assignees[0])
        <img src="{{ $task->assignees[0]->avatar }}" alt="user-avatar" class="el-hoh d-block flex-shrink-0 rounded-circle me-sm-3 me-2" height="32" width="32" />
        @endisset
        <ul class="list-inline email-list-item-actions text-nowrap me-4">
          <li class="list-inline-item" data-href="{{route('admin.projects.tasks.checklist-items.create', [$project, $task, 'from' => 'task-board'])}}" data-toggle="ajax-modal" data-title="Add Check Item"> <i class="tf-icons ti ti-plus scaleX-n1-rtl ti-sm"></i> </li>
          <li class="list-inline-item" data-href="{{route('admin.projects.tasks.edit', [$project, $task, 'from' => 'task-board'])}}" data-toggle="ajax-modal" data-title="Edit Task"> <i class="ti ti-edit"></i> </li>
          <li class="list-inline-item" data-href="{{route('admin.projects.tasks.destroy', [$project, $task, 'from' => 'task-board'])}}" data-toggle="ajax-delete"> <i class="ti ti-trash"></i> </li>
        </ul>
      </div>
    </div>
  </li>
  <div class="task-check-items" data-task-list="{{$task->id}}">
    @forelse ($task->checklistItems as $item)
      <li class="email-list-item" data-item-id="{{$item->id}}" data-{{slug($task->status)}}="true" data-{{$task->priority}}="true">
        <div class="d-flex align-items-center">
          <i class="el-voh drag-handle cursor-move fa-solid fa-ellipsis-vertical me-2"></i>
          <div class="form-check mb-0">
            <input class="email-list-item-input form-check-input checklist-status" type="checkbox" @checked($item->completed_by != null)>
            <label class="form-check-label" for="email-1"></label>
          </div>
          {{-- <i class="email-list-item-bookmark ti ti-star ti-xs d-sm-inline-block d-none cursor-pointer ms-2 me-3"></i> --}}
          <div class="email-list-item-content ms-2 ms-sm-0 me-2">
            {{-- <span class="h6 email-list-item-username me-2">Chandler Bing</span> --}}
            <span class="email-list-item-subject d-xl-inline-block d-block {{$item->completed_by != null ? 'text-decoration-line-through' : ''}}">{{$item->title}}</span>
          </div>
          <div class="email-list-item-meta ms-auto d-flex align-items-center">
            <span class="el-hoh badge bg-label-{{$colors[$task->priority]}} me-2">{{$task->priority}}</span>
            @if ($item->assignedTo)
              <img src="{{ $item->assignedTo->avatar }}" alt="user-avatar" class="el-hoh d-block flex-shrink-0 rounded-circle me-sm-3 me-2" height="32" width="32" />
            @endif
            <ul class="list-inline email-list-item-actions text-nowrap me-4">
              <li class="list-inline-item" data-toggle="ajax-modal" data-title="Edit Check Item" data-href="{{route('admin.projects.tasks.checklist-items.edit', [$project, $task, 'checklist_item' => $item->id, 'from' => 'task-board'])}}"> <i class="ti ti-edit"></i> </li>
              <li class="list-inline-item" data-toggle="ajax-delete"
              data-href="{{ route('admin.projects.tasks.checklist-items.destroy', ['project' => $task->project_id, 'task' => $task, 'checklist_item' => $item->id, 'from' => 'task-board']) }}"> <i class="ti ti-trash"></i> </li>
            </ul>
          </div>
        </div>
      </li>
    @empty
    @endforelse
  </div>
@empty
@endforelse
