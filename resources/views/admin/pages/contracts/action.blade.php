<div class="d-inline-block text-nowrap">
  <button class="btn btn-sm btn-icon" data-toggle="ajax-modal" data-title="Edit Contract" data-href="{{route('admin.contracts.edit', $contract)}}"><i class="ti ti-edit"></i></button>
  <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
      data-href="{{ route('admin.contracts.destroy', $contract) }}"><i class="ti ti-trash"></i></button>
  <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></button>
  <div class="dropdown-menu dropdown-menu-end m-0">
      <a href="javascript:;" class="dropdown-item" data-toggle="ajax-modal" data-title="{{__('Configure Payment Schedule')}}" data-href="{{route('admin.contracts.payment-schedules.create', ['contract' => $contract->id])}}">{{__('Configure Payment Schedule')}}</a>
      <a class="dropdown-item" href="{{route('admin.projects.contracts.phases.milestones.index', ['project' => 'project', 'contract' => $contract->id, 'phase'])}}">{{__('Manage milestones')}}</a>
  </div>
</div>
