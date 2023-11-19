<div class="d-inline-block text-nowrap">
    <button class="btn btn-sm btn-icon" data-toggle="ajax-modal" data-title="Edit Medium" data-href="{{route('admin.mediums.edit', $medium)}}"><i class="ti ti-edit"></i></button>
      <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
          data-href="{{ route('admin.mediums.destroy', $medium) }}"><i class="ti ti-trash"></i></button>
  </div>
  