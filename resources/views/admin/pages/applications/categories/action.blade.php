<div class="d-inline-block text-nowrap">
  <button class="btn btn-sm btn-icon" data-title={{__('Edit Application Type')}} data-toggle="ajax-modal" data-href="{{ route('admin.applications.settings.categories.edit', $applicationCategory) }}"><i class="ti ti-edit"></i></button>
  <button class="btn btn-sm btn-icon" data-toggle="ajax-delete"
      data-href="{{ route('admin.applications.settings.categories.destroy', $applicationCategory) }}"><i class="ti ti-trash"></i></button>
</div>
