<div class="d-inline-block text-nowrap">
  @can(true)
    <button class="btn btn-sm btn-icon" data-toggle="ajax-modal" data-title="Edit Category" data-href="{{route('admin.project-categories.edit', $category)}}"><i class="ti ti-edit"></i></button>
  @endcan
  @can(true)
        <button class="btn btn-sm btn-icon delete-record {{$category->projects->count() != 0 ? 'disabled' : ''}}" data-toggle="ajax-delete"
            data-href="{{ route('admin.project-categories.destroy', ['project_category' => $category->id]) }}"><i
                class="ti ti-trash"></i></button>
  @endcan
  {{-- <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i
          class="ti ti-dots-vertical"></i></button>
  <div class="dropdown-menu dropdown-menu-end m-0">
      <a href="javascript:;" class="dropdown-item">View</a>
      @can(true)
          <a href="javascript:;" class="dropdown-item">Suspend</a>
      @endcan
  </div> --}}
</div>
