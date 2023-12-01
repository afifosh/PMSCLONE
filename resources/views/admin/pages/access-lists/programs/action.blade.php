<div class="d-inline-block text-nowrap">
  <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
      data-href="{{ route('admin.admin-access-lists.programs.destroy', ['admin_access_list' => $admin_id, 'program' => $program->id]) }}"><i
          class="ti ti-trash"></i></button>

  <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></button>
  <div class="dropdown-menu dropdown-menu-end m-0">
      <button class="dropdown-item">{{__('View Contracts')}}</button>
  </div>
</div>
