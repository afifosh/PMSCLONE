<div class="d-inline-block text-nowrap">
  <button class="btn btn-sm btn-icon" data-toggle="ajax-modal" data-title="Edit Contract" data-href="{{route('admin.contracts.edit', $contract)}}"><i class="ti ti-edit"></i></button>
  <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
      data-href="{{ route('admin.contracts.destroy', $contract) }}"><i class="ti ti-trash"></i></button>
  <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></button>
  <div class="dropdown-menu dropdown-menu-end m-0">
      <a class="dropdown-item" href="{{route('admin.projects.contracts.stages.phases.index', ['project' => 'project', 'contract' => $contract->id, 'stage'])}}">{{__('Manage phases')}}</a>
      @if($contract->pending_retentions_count)
        <a href="javascript:;" data-toggle="confirm-action" data-href="{{route('admin.contracts.release-retentions', [$contract])}}" class="dropdown-item">{{__('Release Retentions')}}</a>
      @endif
  </div>
</div>
