<div class="d-inline-block text-nowrap">
  <button class="btn btn-sm btn-icon" data-toggle="ajax-modal" data-title="Edit Check Item" data-href="{{route('admin.project-templates.edit', ['project_template' => $template->id])}}"><i class="ti ti-edit"></i></button>
  <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
      data-href="{{ route('admin.project-templates.destroy', ['project_template' => $template->id]) }}"><i class="ti ti-trash"></i></button>
</div>
