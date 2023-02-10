<div class="d-inline-block text-nowrap">
    {{-- @can(true)
      <button class="btn btn-sm btn-icon" data-toggle="ajax-offcanvas" data-title="Edit Department" data-href="{{route('admin.programs.users.edit', $department)}}"><i class="ti ti-edit"></i></button>
  @endcan --}}
    @can(true)
        @if (request()->program->id == $programUser->program_id)
            <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
                data-href="{{ route('admin.programs.users.destroy', ['program' => $programUser->program_id, 'user' => $programUser->admin_id]) }}"><i
                    class="ti ti-trash"></i></button>
        @endif
    @endcan
    <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i
            class="ti ti-dots-vertical"></i></button>
    <div class="dropdown-menu dropdown-menu-end m-0">
        <a href="javascript:;" class="dropdown-item">View</a>
        @can(true)
            <a href="javascript:;" class="dropdown-item">Suspend</a>
        @endcan
    </div>
</div>
