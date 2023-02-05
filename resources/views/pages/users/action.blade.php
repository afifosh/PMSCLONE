<div class="d-inline-block text-nowrap">
  @can('update user')
      <button class="btn btn-sm btn-icon" data-toggle="ajax-offcanvas" data-title="Edit User" data-href="{{route('users.edit', $user)}}"><i class="ti ti-edit"></i></button>
  @endcan
  @can('delete user')
      <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
          data-href="{{ route('users.destroy', $user) }}"><i class="ti ti-trash"></i></button>
  @endcan
  <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i
          class="ti ti-dots-vertical"></i></button>
  <div class="dropdown-menu dropdown-menu-end m-0">
      <a href="javascript:;" class="dropdown-item">View</a>
      @can('update user')
          <a href="javascript:;" class="dropdown-item">Suspend</a>
      @endcan

  </div>
</div>
