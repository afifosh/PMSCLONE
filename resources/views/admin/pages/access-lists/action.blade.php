<div class="d-inline-block text-nowrap">
  <button class="btn btn-sm btn-icon" data-toggle="ajax-modal" data-title="Edit Access" data-href="{{route('admin.admin-access-lists.edit', ['admin_access_list' => $user]) }}"><i class="ti ti-edit"></i></button>
  <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
      data-href="{{ route('admin.admin-access-lists.destroy', ['admin_access_list' => $user]) }}"><i
          class="ti ti-trash"></i></button>

  <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></button>
  <div class="dropdown-menu dropdown-menu-end m-0">
      <button class="dropdown-item" onclick="renderProgramsTable({{$user->id}}, this);">{{__('View Programs')}}</button>
  </div>
</div>
