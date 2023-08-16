<div class="d-inline-block text-nowrap">
  <button class="btn btn-sm btn-icon" data-toggle="ajax-modal" data-title="Edit Type" data-href="{{route('admin.contract-types.edit', $contract_type)}}"><i class="ti ti-edit"></i></button>
  <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
      data-href="{{ route('admin.contract-types.destroy', $contract_type) }}"><i class="ti ti-trash"></i></button>
</div>
