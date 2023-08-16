<div class="d-inline-block text-nowrap">
  <button class="btn btn-sm btn-icon" data-toggle="ajax-modal" data-title="Edit Contract" data-href="{{route('admin.contracts.edit', $contract)}}"><i class="ti ti-edit"></i></button>
  <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
      data-href="{{ route('admin.contracts.destroy', $contract) }}"><i class="ti ti-trash"></i></button>
</div>
