@forelse ($task->checklistItems as $item)
    <li class="list-group-item lh-1 d-flex justify-content-between align-items-center" data-task-id="{{$task->id}}" data-project-id="{{$task->project_id}}" data-checklist-id={{$item->id}}>
      <span class="d-flex justify-content-between align-items-center">
        <i class="drag-handle cursor-move ti ti-menu-2 align-text-bottom me-2"></i>
        <div class="form-check form-check-success">
          <input class="form-check-input mt-1 checklist-status" type="checkbox" value="1" @checked($item->status)>
        </div>
        <span class="{{$item->status ? 'text-decoration-line-through' : ''}}">{{$item->title}}</span>
      </span>
      <i onclick="delete_checklist({{$item->id}}, {{$task->id}})" class="fa-regular fa-md fa-trash-can cursor-pointer"></i>
    </li>
  @empty
  @endforelse
