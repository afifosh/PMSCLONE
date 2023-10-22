<div class="d-inline-block text-nowrap">
  <button class="btn btn-sm btn-icon" data-href="{{route('admin.contracts.stages.edit', ['contract' => $stage->contract_id, 'stage' => $stage->id])}}" data-toggle="ajax-modal" data-title="Edit Stage"><i class="ti ti-edit"></i></button>
  <button class="btn btn-sm btn-icon" data-href="{{route('admin.contracts.stages.destroy', ['contract' => $stage->contract_id, 'stage' => $stage->id])}}" data-toggle="ajax-delete"><i class="ti ti-trash"></i></button>
  {{-- <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></button>
  <div class="dropdown-menu dropdown-menu-end m-0">
      <a class="dropdown-item" href="{{route('admin.contracts.stages.logs.index', ['contract' => $stage->contract_id, 'stage' => $stage->id])}}">{{__('View Logs')}}</a>
  </div> --}}
</div>
