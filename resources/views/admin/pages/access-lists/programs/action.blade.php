<div class="d-inline-block text-nowrap">
  <button class="btn btn-sm btn-icon" data-toggle="ajax-modal" data-title="Edit Program Access Rule" data-href="{{route('admin.admin-access-lists.programs.edit', ['admin_access_list' => $admin_id, 'program' => $program->id]) }}"><i class="ti ti-edit"></i></button>

  <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
      data-href="{{ route('admin.admin-access-lists.programs.destroy', ['admin_access_list' => $admin_id, 'program' => $program->id]) }}"><i
          class="ti ti-trash"></i></button>

  <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></button>
  <div class="dropdown-menu dropdown-menu-end m-0">
      <button class="dropdown-item" onclick="revokeProgramAccess({{$admin_id}}, {{$program->id}})">{{__('Revoke Access')}}</button>
  </div>
</div>
