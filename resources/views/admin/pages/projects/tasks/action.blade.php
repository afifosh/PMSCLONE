<div class="d-inline-block text-nowrap">
    <button class="btn btn-sm btn-icon" data-toggle="ajax-modal" data-title="Edit Task" data-href="{{route('admin.projects.tasks.edit', ['task' => $task, 'project' => $task->project_id])}}"><i class="ti ti-edit"></i></button>
        <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
            data-href="{{ route('admin.projects.tasks.destroy', ['task' => $task, 'project' => $task->project_id]) }}"><i
                class="ti ti-trash"></i></button>
</div>
