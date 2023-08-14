<div class="d-inline-block text-nowrap">
  <button class="btn btn-sm btn-icon" data-toggle="ajax-modal" data-title="Copy Template" data-href="{{route('admin.project-templates.edit', ['project_template' => $template->id, 'type' => 'copy'])}}"><i class="fa-solid fa-lg fa-copy"></i></button>
  <button class="btn btn-sm btn-icon" data-toggle="ajax-modal" data-title="Edit Template" data-href="{{route('admin.project-templates.edit', ['project_template' => $template->id])}}"><i class="ti ti-edit"></i></button>
  <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
      data-href="{{ route('admin.project-templates.destroy', ['project_template' => $template->id]) }}"><i class="ti ti-trash"></i></button>
</div>
