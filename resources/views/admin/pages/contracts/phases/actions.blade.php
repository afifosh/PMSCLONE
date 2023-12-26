<div class="d-inline-block text-nowrap">
  <span class="bi-drag pt-1 cursor-grab"><i class="ti ti-menu-2"></i></span>
  <button class="btn btn-sm btn-icon" data-href="{{route('admin.projects.contracts.stages.phases.edit', ['project' => 'project', 'contract' => $contract_id, 'stage' => $phase->stage_id, $phase])}}" data-toggle="ajax-modal" data-title="Edit Phase"><i class="ti ti-edit"></i></button>
  <button class="btn btn-sm btn-icon {{$is_editable ?: 'disabled'}}" data-href="{{route('admin.projects.contracts.stages.phases.destroy', ['project' => 'project', 'contract' => $contract_id, 'stage' => $phase->stage_id, $phase])}}" data-toggle="ajax-delete"><i class="ti ti-trash"></i></button>
</div>
