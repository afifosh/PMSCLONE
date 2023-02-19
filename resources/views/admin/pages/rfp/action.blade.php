<div class="d-inline-block text-nowrap">
  @can(true)
      <button class="btn btn-sm btn-icon" data-toggle="ajax-modal" data-title="Edit RFP" data-href="{{route('admin.draft-rfps.edit', $rfp)}}"><i class="ti ti-edit"></i></button>
  @endcan
  @can(true)
      <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
          data-href="{{ route('admin.draft-rfps.destroy', $rfp) }}"><i class="ti ti-trash"></i></button>
  @endcan
  <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i
          class="ti ti-dots-vertical"></i></button>
  {{-- <div class="dropdown-menu dropdown-menu-end m-0">
      <a href="javascript:;" class="dropdown-item">View</a>
  </div> --}}
</div>
