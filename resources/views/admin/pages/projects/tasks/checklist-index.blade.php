<div class="mt-2">
  @include('admin._partials.sections.progressBar', ['perc' => $task->checklistItems->count() > 0 ? round($task->checklistItems->whereNotNull('completed_by')->count()/$task->checklistItems->count()*100) : 0, 'color' => 'primary', 'height' => '14px', 'show_perc' => true])
</div>
<div class="d-flex justify-content-end">
  @if ($task->is_completed_checklist_hidden)
    <form action="{{route('admin.projects.tasks.hide-completed', ['project' => $task->project_id, 'task' => $task->id, 'status' => false])}}" method="POST">
      @method('PUT')
      <button type="button" data-form="ajax-form" class="btn btn-primary btn-sm mt-2">Show Completed ({{$task->checklistItems->whereNotNull('completed_by')->count()}})</button>
    </form>
  @else
    <form action="{{route('admin.projects.tasks.hide-completed', ['project' => $task->project_id, 'task' => $task->id, 'status' => true])}}" method="POST">
      @method('PUT')
      <button type="button" data-form="ajax-form" class="btn btn-primary btn-sm mt-2">Hide Completed</button>
    </form>
  @endif

</div>
@php
    $items = $task->is_completed_checklist_hidden ? $task->checklistItems->whereNull('completed_by') : $task->checklistItems;
@endphp
@forelse ($items as $item)
    <li class="list-group-item lh-1 d-flex justify-content-between align-items-center" data-task-id="{{$task->id}}" data-project-id="{{$task->project_id}}" data-checklist-id={{$item->id}}>
      <div>
        <span class="d-flex align-items-center">
          <i class="drag-handle cursor-move ti ti-menu-2 align-text-bottom me-2"></i>
          <div class="form-check form-check-success">
            <input class="form-check-input mt-1 checklist-status" type="checkbox" value="1" @checked($item->status)>
          </div>
          <span class="{{$item->status ? 'text-decoration-line-through' : ''}}">{{$item->title}}</span>
        </span>
        @if ($item->created_by)
          <small>Created by {{$item->createdBy->full_name}}</small>
        @endif
        @if ($item->completed_by)
          , <small>Completed by {{$item->completedBy->full_name}}</small>
        @endif
      </div>
      <div>
        <i data-toggle="ajax-modal" data-title="Edit Task" data-size="modal-xl" data-href="{{route('admin.projects.tasks.checklist-items.edit', ['project' => $task->project_id, 'task' => $task->id, 'checklist_item' => $item->id])}}" class="ti ti-edit cursor-pointer"></i>
        <i data-toggle="ajax-delete" data-href="{{route('admin.projects.tasks.checklist-items.destroy', ['project' => $task->project_id, 'task' => $task->id, 'checklist_item' => $item->id])}}" class="ti ti-trash cursor-pointer"></i>
      </div>
    </li>
  @empty
  @endforelse
