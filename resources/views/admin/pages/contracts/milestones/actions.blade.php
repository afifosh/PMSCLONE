<div class="d-inline-block text-nowrap">
  <button class="btn btn-sm btn-icon" data-href="{{route('admin.projects.contracts.phases.milestones.edit', ['project' => 'project', 'contract' => $phase->contract_id, 'phase' => $milestone->phase_id, $milestone])}}" data-toggle="ajax-modal" data-title="Edit Milestone"><i class="ti ti-edit"></i></button>
  <button class="btn btn-sm btn-icon delete-record" data-href="{{route('admin.projects.contracts.phases.milestones.destroy', ['project' => 'project', 'contract' => $phase->contract_id, 'phase' => $milestone->phase_id, $milestone])}}" data-toggle="ajax-delete"><i class="ti ti-trash"></i></button>
</div>
