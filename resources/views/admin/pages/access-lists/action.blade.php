<div class="d-inline-block text-nowrap">
  <button class="btn btn-sm btn-icon" onclick="renderProgramsTable({{$user->id}}, this);"><i class="fa fa-eye"></i></button>
  <button class="btn btn-sm btn-icon" data-toggle="ajax-modal" data-title="Edit Access" data-href="{{route('admin.admin-access-lists.edit', ['admin_access_list' => $user]) }}"><i class="ti ti-edit"></i></button>
  <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
      data-href="{{ route('admin.admin-access-lists.destroy', ['admin_access_list' => $user]) }}"><i
          class="ti ti-trash"></i></button>
</div>
