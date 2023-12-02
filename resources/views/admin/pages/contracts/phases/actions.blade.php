<div class="d-inline-block text-nowrap">
  <span class="bi-drag pt-1 cursor-grab"><i class="ti ti-menu-2"></i></span>
  <button class="btn btn-sm btn-icon {{$is_editable ?: 'disabled'}}" data-href="{{route('admin.projects.contracts.stages.phases.edit', ['project' => 'project', 'contract' => $contract_id, 'stage' => $phase->stage_id, $phase])}}" data-toggle="ajax-modal" data-title="Edit Phase"><i class="ti ti-edit"></i></button>
  <button class="btn btn-sm btn-icon {{$is_editable ?: 'disabled'}}" data-href="{{route('admin.projects.contracts.stages.phases.destroy', ['project' => 'project', 'contract' => $contract_id, 'stage' => $phase->stage_id, $phase])}}" data-toggle="ajax-delete"><i class="ti ti-trash"></i></button>

  <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></button>
  <div class="dropdown-menu dropdown-menu-end m-0">
      <button class="dropdown-item" data-href="{{route('admin.contracts.phases.taxes.create', ['contract' => $contract_id, 'phase' => $phase->id])}}" data-toggle="ajax-modal" data-title="Add Tax" >{{__('Add Tax')}}</button>
      <button class="dropdown-item" data-href="{{route('admin.contracts.phases.deductions.create', ['contract' => $contract_id, 'phase' => $phase->id])}}" data-toggle="ajax-modal" data-title="Add Deduction" >{{__('Add Deduction')}}</button>
      <button class="dropdown-item" id="phase-ex-{{$phase->id}}" onclick="expandPhaseDetails({{$contract_id}}, {{$phase->id}}, this)">{{__('Details')}}</button>
  </div>
</div>
