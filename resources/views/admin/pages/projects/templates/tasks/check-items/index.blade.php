@forelse ($task->checkItemTemplates as $item)
    <div class="mx-4">
        <hr>
        <div class="d-flex justify-content-between">
            <span>{{ $item->title }}</span>
            <div class="d-inline-block text-nowrap">
              <button class="btn btn-sm btn-icon" data-toggle="ajax-modal" data-title="Edit Check Item" data-href="{{route('admin.project-templates.tasks.check-items.edit', ['project_template' => $task->project_template_id, 'task' => $task, 'check_item' => $item->id])}}"><i class="ti ti-edit"></i></button>
              <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
                  data-href="{{ route('admin.project-templates.tasks.check-items.destroy', ['project_template' => $task->project_template_id, 'task' => $task, 'check_item' => $item->id]) }}"><i class="ti ti-trash"></i></button>
            </div>
        </div>
    </div>
@empty
@endforelse
