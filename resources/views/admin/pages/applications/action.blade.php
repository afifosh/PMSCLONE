<div class="d-inline-block text-nowrap">
  <a class="btn btn-sm btn-icon" href="{{ route('admin.applications.edit', $application) }}"><i class="ti ti-edit"></i></a>
  <button class="btn btn-sm btn-icon" data-toggle="ajax-delete"
      data-href="{{ route('admin.applications.destroy', $application) }}"><i class="ti ti-trash"></i></button>
</div>
