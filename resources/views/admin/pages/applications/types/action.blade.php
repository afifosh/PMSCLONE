<div class="d-inline-block text-nowrap">
  <button class="btn btn-sm btn-icon" data-toggle="ajax-modal" data-title="Edit Type" data-href="{{route('admin.applications.settings.types.edit', $type)}}"><i class="ti ti-edit"></i></button>
      <button class="btn btn-sm btn-icon delete-record {{$type->applications_count != 0 ? 'disabled' : ''}}" data-toggle="ajax-delete"
          data-href="{{ route('admin.applications.settings.types.destroy', ['type' => $type->id]) }}"><i
              class="ti ti-trash"></i></button>
</div>
