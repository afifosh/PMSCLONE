<div class="d-inline-block text-nowrap">
  <button class="btn btn-sm btn-icon" data-toggle="ajax-modal" data-title="Edit Party" data-href="{{route('admin.contracts.contract-parties.edit', [$contract, $contractParty])}}"><i class="ti ti-edit"></i></button>
    <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
        data-href="{{ route('admin.contracts.contract-parties.destroy', [$contract, $contractParty]) }}"><i class="ti ti-trash"></i></button>
</div>
