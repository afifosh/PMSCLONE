<div class="d-inline-block text-nowrap">
  <button class="btn btn-sm btn-icon" data-href="{{route('admin.contracts.stages.edit', ['contract' => $stage->contract_id, 'stage' => $stage->id])}}" data-toggle="ajax-modal" data-title="Edit Phase"><i class="ti ti-edit"></i></button>
  <button class="btn btn-sm btn-icon delete-record" data-href="{{route('admin.contracts.stages.destroy', ['contract' => $stage->contract_id, 'stage' => $stage->id])}}" data-toggle="ajax-delete"><i class="ti ti-trash"></i></button>
</div>
